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
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->text('notas')->nullable();
            $table->timestamp('ultima_fecha_contacto')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('id')->on('leads_crm');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts_crm');
    }
};
