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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('tipoDoc');
            $table->string('numDoc');
            $table->string('rznSocial');
            $table->string('nombreComercial')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('codigoPostal')->nullable();
            $table->string('image')->nullable();
            $table->string('archivo')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('tipo_customer_id')->constrained('tipo_customers')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
