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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('ruc');
            $table->string('razonSocial');
            $table->string('nombreComercial');
            $table->string('email');
            $table->string('telephone');
            $table->foreignId('address_id')->constrained('addresses');
            $table->string('ctaBanco')->nullable();
            $table->string('nroMtc')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('sol_user')->nullable();
            $table->string('sol_pass')->nullable();
            $table->string('cert_path')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->date('inicio_suscripcion')->nullable();
            $table->date('fin_suscripcion')->nullable();
            $table->date('inicio_produccion')->nullable();
            $table->date('fin_produccion')->nullable();
            $table->boolean('isProduction');
            $table->boolean('isActive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
