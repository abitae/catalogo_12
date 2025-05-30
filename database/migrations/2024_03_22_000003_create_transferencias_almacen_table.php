<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transferencias_almacen', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('almacen_origen_id')->constrained('almacenes');
            $table->foreignId('almacen_destino_id')->constrained('almacenes');
            $table->json('productos')->comment('Array de productos con sus cantidades');
            $table->dateTime('fecha_transferencia');
            $table->string('estado')->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->dateTime('fecha_confirmacion')->nullable();
            $table->text('motivo_transferencia')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transferencias_almacen');
    }
};
