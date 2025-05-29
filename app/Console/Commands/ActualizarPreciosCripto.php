<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\Currency;
use App\Models\CryptoCurrency;
use App\Models\CryptoHistory;
use App\Models\CryptoPrice;

class ActualizarPreciosCripto extends Command
{
    protected $signature = 'app:actualizar-precios-cripto';
    protected $description = 'Actualiza los precios actuales y el historial de las criptomonedas en USD y EUR';

    public function handle()
    {
        try {
            $this->info('Iniciando actualización de precios de criptomonedas...');

            // Obtener las monedas fiduciarias (USD y EUR)
            $usdCurrency = Currency::where('code', 'USD')->first();
            $eurCurrency = Currency::where('code', 'EUR')->first();

            if (!$usdCurrency) {
                $this->error('ERROR: Moneda USD no encontrada en la tabla currencies. ¡Asegúrate de que exista!');
                return Command::FAILURE;
            }
            if (!$eurCurrency) {
                $this->error('ERROR: Moneda EUR no encontrada en la tabla currencies. ¡Asegúrate de que exista!');
                return Command::FAILURE;
            }

            // Obtener los precios de la API
            $this->line("--- Fetching API Prices ---");
            $apiPrices = $this->fetchCryptoPrices();
            $this->line("--- Raw API Data Formatted (Console Output) ---");
            dump($apiPrices);
            $this->line("--- End Raw API Data Formatted ---");

            if (empty($apiPrices)) {
                $this->warn('No se pudieron obtener precios de la API. Saliendo.');
                return Command::SUCCESS;
            }

            $updatedCurrentPricesCount = 0;
            $historyAddedCount = 0;

            foreach ($apiPrices as $apiSymbol => $priceData) {
                $this->line("--- Processing Symbol: {$apiSymbol} ---");

                $cryptoCurrency = CryptoCurrency::where('symbol', strtoupper($apiSymbol))->first();

                if (!$cryptoCurrency) {
                    $this->warn("Criptomoneda con símbolo {$apiSymbol} no encontrada en la DB. Saltando...");
                    continue;
                }

                // actualiza los valores de la tabla `crypto_prices` para USD
                $usdPrice = $priceData['usd'] ?? null;
                $this->line("DEBUG: Precio USD para {$apiSymbol}: " . var_export($usdPrice, true) . ", is_numeric: " . (is_numeric($usdPrice) ? 'true' : 'false'));

                if ($usdPrice !== null && is_numeric($usdPrice)) {
                    CryptoPrice::updateOrCreate(
                        [
                            'crypto_currency_id' => $cryptoCurrency->id,
                            'currency_id' => $usdCurrency->id,
                        ],
                        [
                            'price_value' => $usdPrice,
                        ]
                    );
                    $updatedCurrentPricesCount++;
                    $this->info("Precio actual de {$cryptoCurrency->symbol} en USD actualizado a \${$usdPrice}");
                } else {
                    $this->warn("Precio USD no disponible o inválido para {$apiSymbol}. NO SE ACTUALIZARÁ.");
                }

                // actualiza los valores de la tabla `crypto_prices` para EUR
                $eurPrice = $priceData['eur'] ?? null;
                $this->line("DEBUG: Precio EUR para {$apiSymbol}: " . var_export($eurPrice, true) . ", is_numeric: " . (is_numeric($eurPrice) ? 'true' : 'false'));

                if ($eurPrice !== null && is_numeric($eurPrice)) {
                    CryptoPrice::updateOrCreate(
                        [
                            'crypto_currency_id' => $cryptoCurrency->id,
                            'currency_id' => $eurCurrency->id,
                        ],
                        [
                            'price_value' => $eurPrice,
                        ]
                    );
                    $updatedCurrentPricesCount++;
                    $this->info("Precio actual de {$cryptoCurrency->symbol} en EUR actualizado a €{$eurPrice}");
                } else {
                    $this->warn("Precio EUR no disponible o inválido para {$apiSymbol}. NO SE ACTUALIZARÁ.");
                }

                // --- se encarga de guardar el historial de la criptomonedas (`crypto_histories`) ---
                if ($usdPrice !== null && is_numeric($usdPrice)) {
                    CryptoHistory::create([
                        'crypto_currency_id' => $cryptoCurrency->id,
                        'currency_id' => $usdCurrency->id, 
                        'price' => $usdPrice,
                        'date' => now(),
                    ]);
                    $historyAddedCount++;
                }

                if ($eurPrice !== null && is_numeric($eurPrice)) {
                    CryptoHistory::create([
                        'crypto_currency_id' => $cryptoCurrency->id,
                        'currency_id' => $eurCurrency->id, 
                        'price' => $eurPrice,
                        'date' => now(),
                    ]);
                    $historyAddedCount++;
                }
            }

            $this->info("Completado: {$updatedCurrentPricesCount} precios actuales actualizados y {$historyAddedCount} registros históricos añadidos.");
            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->handleError($e);
            return Command::FAILURE;
        }
    }

    protected function fetchCryptoPrices(): array
    {
        $client = new Client();
        $cryptoIds = 'bitcoin,ethereum,solana,cardano';
        $vsCurrencies = 'usd,eur';

        try {
            $response = $client->get("https://api.coingecko.com/api/v3/simple/price", [
                'query' => [
                    'ids' => $cryptoIds,
                    'vs_currencies' => $vsCurrencies,
                ],
                'verify' => false
            ]);

            $contents = $response->getBody()->getContents();
            $this->line("--- Raw CoinGecko API Response (Console Output) ---");
            dump($contents);
            $this->line("--- End Raw CoinGecko API Response ---");

            $data = json_decode($contents, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Error al decodificar la respuesta JSON de la API.');
                return [];
            }

            $this->info('Precios de criptomonedas obtenidos de la API CoinGecko.');
            return $this->formatApiData($data);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->error('Error del cliente HTTP (4xx) al obtener precios: ' . $e->getMessage());
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $this->error('Error del servidor API (5xx) al obtener precios: ' . $e->getMessage());
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->error('Error de red/solicitud al obtener precios: ' . $e->getMessage());
        } catch (Throwable $e) {
            $this->error('Error inesperado al obtener precios de la API: ' . $e->getMessage());
        }
        return [];
    }

    protected function formatApiData(array $apiData): array
    {
        $formatted = [];

        foreach ($apiData as $cryptoName => $currencies) {
            $symbol = $this->mapCryptoNameToSymbol($cryptoName);
            if ($symbol) {
                $formatted[$symbol] = [
                    'usd' => $currencies['usd'] ?? null,
                    'eur' => $currencies['eur'] ?? null
                ];
            }
        }
        return $formatted;
    }

    protected function mapCryptoNameToSymbol(string $name): ?string
    {
        $mapping = [
            'bitcoin' => 'btc',
            'ethereum' => 'eth',
            'solana' => 'sol',
            'cardano' => 'ada',
        ];

        return $mapping[strtolower($name)] ?? null;
    }

    protected function handleError(Throwable $e): void
    {
        $this->error("ERROR CRÍTICO DEL COMANDO: {$e->getMessage()}");
        $this->error("Archivo: {$e->getFile()}:{$e->getLine()}");
        Log::error("Command Fatal Error", [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'exception' => $e
        ]);
    }
}