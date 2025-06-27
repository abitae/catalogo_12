<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contacts_crm', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo');
            $table->string('telefono')->nullable();
            $table->string('cargo')->nullable();
            $table->string('empresa')->nullable();
            $table->dateTime('ultima_fecha_contacto')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->foreignId('customer_id')->constrained('customers');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts_crm');
    }
};
