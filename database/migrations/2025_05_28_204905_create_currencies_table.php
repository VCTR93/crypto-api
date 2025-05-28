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
            $table->string('code', 3)->unique()->comment('Código ISO 4217 de la moneda (ej. USD, EUR)'); // Código de la moneda
            $table->string('name')->comment('Nombre de la moneda (ej. Dólar estadounidense, Euro)'); // Nombre de la moneda
            $table->timestamps(); 
            $table->comment('Tabla para almacenar información sobre las monedas'); // Comentario general de la tabla
        });

        Schema::create('crypto_currencies', function (Blueprint $table) {
            $table->increments('id')->comment("Clave primaria de la tabla de criptomonedas");
            $table->string('name')->comment('Nombre completo de la criptomoneda (ej. Bitcoin)');
            $table->string('symbol', 10)->unique()->comment('Símbolo de la criptomoneda (ej. BTC)');
            $table->decimal('current_price', 18, 8)->comment('Precio actual de la criptomoneda (con alta precisión)');

            // Relación con la tabla 'currencies' (monedas fiduciarias)
            $table->unsignedBigInteger('currency_id')->comment('ID de la moneda fiduciaria a la que se asocia el precio (ej. USD, EUR)');
            $table->foreign('currency_id')
                  ->references('id')
                  ->on('currencies')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete()
                  ->comment('Relación con la tabla de monedas fiduciarias');
            
            $table->timestamps();
            
            $table->comment('Tabla para almacenar información sobre las criptomonedas'); // Comentario general de la tabla
        });

        Schema::create('crypto_histories', function (Blueprint $table) {
            $table->increments('id')->comment("Clave primaria de la tabla de historial de precios de criptomonedas"); 
            
            // Clave foránea para relacionar con la criptomoneda
            $table->unsignedBigInteger('crypto_currency_id')->comment('ID de la criptomoneda a la que pertenece este registro de historial');
            $table->foreign('crypto_currency_id')
                  ->references('id')
                  ->on('crypto_currencies')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete()
                  ->comment('Relación con la tabla de criptomonedas');
            
            $table->decimal('price', 18, 8)->comment('Precio de la criptomoneda en el momento del registro');
            $table->timestamp('date')->useCurrent()->comment('Fecha y hora del registro del precio');

            $table->comment('Tabla para almacenar el historial de precios de las criptomonedas'); // Comentario general de la tabla
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('crypto_currencies');
    }
};
