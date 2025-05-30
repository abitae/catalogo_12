<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deals_crm', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('opportunity_id');
            $table->decimal('valor', 10, 2);
            $table->string('etapa');
            $table->timestamp('fecha_cierre')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('terminos')->nullable();
            $table->unsignedBigInteger('asignado_a')->nullable();
            $table->string('estado')->default('activo');
            $table->integer('probabilidad');
            $table->decimal('ingreso_esperado', 10, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('opportunity_id')->references('id')->on('opportunities_crm');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deals_crm');
    }
};
