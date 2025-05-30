<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('opportunities_crm', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('estado')->default('nueva');
            $table->unsignedBigInteger('tipo_negocio_id');
            $table->unsignedBigInteger('marca_id');
            $table->unsignedBigInteger('lead_id');
            $table->decimal('valor', 10, 2);
            $table->string('etapa');
            $table->integer('probabilidad');
            $table->timestamp('fecha_cierre_esperada')->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('asignado_a')->nullable();
            $table->timestamp('ultima_fecha_actividad')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tipo_negocio_id')->references('id')->on('tipos_negocio_crm');
            $table->foreign('marca_id')->references('id')->on('marcas_crm');
            $table->foreign('lead_id')->references('id')->on('leads_crm');
        });
    }

    public function down()
    {
        Schema::dropIfExists('opportunities_crm');
    }
};
