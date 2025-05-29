<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id')->comment("Clave primaria de la tabla de monedas");
            $table->string('name')->comment('Nombre de la moneda (ej. Dólar estadounidense, Euro)');
            $table->string('code', 3)->unique()->comment('Código ISO 4217 de la moneda (ej. USD, EUR)');
            $table->string('symbol', 5);
            $table->timestamps(); 
            $table->comment('Tabla para almacenar información sobre las monedas');
        });
    
        Schema::create('crypto_currencies', function (Blueprint $table) {
            $table->increments('id')->comment("Clave primaria de la tabla de criptomonedas");
            $table->string('name')->comment('Nombre completo de la criptomoneda (ej. Bitcoin)');
            $table->string('symbol', 10)->unique()->comment('Símbolo de la criptomoneda (ej. BTC)');
            $table->timestamps();
            $table->comment('Tabla para almacenar información sobre las criptomonedas');
        });
    
        Schema::create('crypto_prices', function (Blueprint $table) {
            $table->id()->comment('Clave primaria');
            $table->unsignedInteger('crypto_currency_id')->comment('ID de la criptomoneda');
            $table->foreign('crypto_currency_id')->references('id')->on('crypto_currencies')->cascadeOnDelete();
    
            $table->unsignedBigInteger('currency_id')->comment('ID de la moneda fiduciaria (USD, EUR)');
            $table->foreign('currency_id')->references('id')->on('currencies')->cascadeOnDelete();
    
            $table->decimal('price_value', 18, 8)->comment('Valor numérico del precio');
    
            $table->unique(['crypto_currency_id', 'currency_id']);
            $table->timestamps();
            $table->comment('Tabla de precios de criptomonedas por moneda fiduciaria');
        });

        Schema::create('crypto_histories', function (Blueprint $table) {
            $table->increments('id')->comment("Clave primaria de la tabla de historial de precios de criptomonedas"); 
            $table->unsignedInteger('crypto_currency_id')->comment('ID de la criptomoneda a la que pertenece este registro de historial');
            $table->foreign('crypto_currency_id')->references('id')->on('crypto_currencies')->cascadeOnUpdate()->restrictOnDelete()->comment('Relación con la tabla de criptomonedas');
            
            $table->unsignedBigInteger('currency_id')->comment('ID de la moneda fiduciaria para este precio histórico');
            $table->foreign('currency_id')->references('id')->on('currencies')->cascadeOnUpdate()->restrictOnDelete()->comment('Relación con la tabla de monedas fiduciarias');
    
            $table->decimal('price', 18, 8)->comment('Precio de la criptomoneda en el momento del registro');
            $table->timestamp('date')->useCurrent()->comment('Fecha y hora del registro del precio');
    
            $table->comment('Tabla para almacenar el historial de precios de las criptomonedas');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('crypto_currencies');
        Schema::dropIfExists('crypto_histories');
        Schema::dropIfExists('crypto_prices');
    }
};
