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
        Schema::create('sucursals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ruc');
            $table->string('razonSocial');
            $table->string('nombreComercial');
            $table->string('email');
            $table->string('telephone');
            $table->foreignId('address_id')->constrained('addresses');
            $table->foreignId('company_id')->constrained('companies');
            $table->string('logo_path')->nullable(); // path de la imagen de la sucursal
            $table->string('series_suffix')->nullable(); // sufijo de la serie de documentos ejemplo F001, F002, B001, B002, T001, T002, NC001, NC002, ND001, ND002, etc.
            $table->string('codigoSunat')->nullable(); // código de la sucursal en el sistema de SUNAT
            $table->boolean('isActive')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursals');
    }
};
