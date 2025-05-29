<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
// Importa tus modelos correctos según tus migraciones:
use App\Models\Currency; // Para la tabla 'currencies'
use App\Models\CryptoCurrency; // Para la tabla 'crypto_currencies'
use App\Models\CryptoHistory; // Para la tabla 'crypto_histories' (si la usas)


class ActualizarPreciosCripto extends Command
{
    protected $signature = 'app:actualizar-precios-cripto';
    protected $description = 'Actualiza los precios actuales y el historial de las criptomonedas';

    public function handle()
    {
        try {
            $this->info('Actualizando precios de criptomonedas...');

            // Paso 1: Obtener el ID de la moneda fiduciaria (ej. USD)
            // Asume que siempre actualizas precios en USD.
            // Si la moneda no existe, el comando fallará.
            $usdCurrency = Currency::where('code', 'USD')->first();

            if (!$usdCurrency) {
                $this->error('ERROR: Moneda USD no encontrada en la tabla currencies. ¡Asegúrate de que exista!');
                Log::error('Moneda USD no encontrada al actualizar precios de criptomonedas.');
                return Command::FAILURE; // Detener la ejecución si no se encuentra USD
            }

            // Paso 2: Obtener los precios de la API (ej. CoinGecko)
            $apiPrices = $this->fetchCryptoPrices();

            if (empty($apiPrices)) {
                $this->warn('No se pudieron obtener precios de criptomonedas de la API. Saliendo.');
                Log::warning('No se obtuvieron precios de criptomonedas al actualizar desde la API.');
                return Command::SUCCESS; // No hay datos, pero no es un fallo fatal
            }

            // Paso 3: Procesar y guardar los precios
            $updatedCount = 0;
            $historyAddedCount = 0;

            foreach ($apiPrices as $apiSymbol => $data) {
                $cryptoPrice = $data['usd'] ?? null; // Asumiendo que la API devuelve precios en USD

                if ($cryptoPrice !== null) {
                    // Buscar la criptomoneda por su símbolo (ej. 'btc', 'eth')
                    $cryptoCurrency = CryptoCurrency::where('symbol', strtoupper($apiSymbol))->first();

                    if ($cryptoCurrency) {
                        // Actualizar el precio actual en la tabla crypto_currencies
                        $cryptoCurrency->current_price = $cryptoPrice;
                        $cryptoCurrency->currency_id = $usdCurrency->id; // Asignar el ID de USD
                        $cryptoCurrency->save();
                        $updatedCount++;
                        $this->info("Precio de {$cryptoCurrency->name} ({$cryptoCurrency->symbol}) actualizado a $ {$cryptoPrice}");

                        // Opcional: Guardar en el historial de precios
                        CryptoHistory::create([
                            'crypto_currency_id' => $cryptoCurrency->id,
                            'price' => $cryptoPrice,
                            'date' => now(), // Registra la fecha y hora actual
                        ]);
                        $historyAddedCount++;

                    } else {
                        $this->warn("Criptomoneda con símbolo {$apiSymbol} no encontrada en la tabla crypto_currencies. Saltando.");
                        Log::warning("Criptomoneda no encontrada en DB: {$apiSymbol}");
                    }
                } else {
                    $this->warn("Precio USD no disponible para {$apiSymbol} en la respuesta de la API.");
                }
            }

            $this->info("Proceso completado. Se actualizaron {$updatedCount} criptomonedas y se añadieron {$historyAddedCount} registros al historial.");
            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->error('¡ERROR FATAL al ejecutar el comando ActualizarPreciosCripto!');
            $this->error('Mensaje de error: ' . $e->getMessage());
            $this->error('Archivo del error: ' . $e->getFile() . ' (Línea: ' . $e->getLine() . ')');
            
            Log::error('Error detallado en ActualizarPreciosCripto: ' . $e->getMessage(), ['exception' => $e]);
            return Command::FAILURE; // Indicar que el comando falló
        }
    }

    /**
     * Fetches cryptocurrency prices from a public API (e.g., CoinGecko).
     * Puedes ajustar esta URL para obtener las criptomonedas que necesites.
     *
     * @return array Associative array of symbol => price data (e.g., ['bitcoin' => ['usd' => 60000]])
     */
    protected function fetchCryptoPrices(): array
    {
        $client = new Client();
        $prices = [];
        try {
            // Ejemplo con CoinGecko para Bitcoin y Ethereum
            // Asegúrate de que los IDs aquí (bitcoin, ethereum) coincidan con lo que quieres obtener
            $response = $client->get('https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,solana,cardano&vs_currencies=usd');
            $data = json_decode($response->getBody()->getContents(), true);

            // Reestructura los datos para que sean fáciles de usar, convirtiendo el ID a un símbolo común
            // NOTA: CoinGecko usa IDs como 'bitcoin', no símbolos como 'BTC' directamente en la respuesta.
            // Necesitas mapear esos IDs a tus símbolos de DB (BTC, ETH, SOL, ADA).
            if (!empty($data)) {
                if (isset($data['bitcoin'])) {
                    $prices['btc'] = ['usd' => $data['bitcoin']['usd']];
                }
                if (isset($data['ethereum'])) {
                    $prices['eth'] = ['usd' => $data['ethereum']['usd']];
                }
                if (isset($data['solana'])) {
                    $prices['sol'] = ['usd' => $data['solana']['usd']];
                }
                 if (isset($data['cardano'])) {
                    $prices['ada'] = ['usd' => $data['cardano']['usd']];
                }
            }
            
            Log::info('Precios de criptomonedas obtenidos de la API CoinGecko.');

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->error('Error del cliente HTTP (4xx) al obtener precios de la API: ' . $e->getMessage());
            Log::error('Guzzle Client Error: ' . $e->getMessage(), ['exception' => $e, 'code' => $e->getCode()]);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $this->error('Error del servidor API (5xx) al obtener precios: ' . $e->getMessage());
            Log::error('Guzzle Server Error: ' . $e->getMessage(), ['exception' => $e, 'code' => $e->getCode()]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->error('Error de red/solicitud al obtener precios de la API: ' . $e->getMessage());
            Log::error('Guzzle Request Error: ' . $e->getMessage(), ['exception' => $e]);
        } catch (Throwable $e) {
            $this->error('Error inesperado al obtener precios de la API: ' . $e->getMessage());
            Log::error('API Fetch Unknown Error: ' . $e->getMessage(), ['exception' => $e]);
        }
        return $prices;
    }
}