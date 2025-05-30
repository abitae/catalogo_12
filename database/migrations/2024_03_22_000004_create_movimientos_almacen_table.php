<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movimientos_almacen', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('tipo_movimiento');
            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->foreignId('producto_id')->constrained('productos_almacen');
            $table->decimal('cantidad', 10, 2);
            $table->dateTime('fecha_movimiento');
            $table->string('motivo');
            $table->string('documento_referencia')->nullable();
            $table->string('estado');
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->decimal('valor_unitario', 10, 2);
            $table->decimal('valor_total', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos_almacen');
    }
};
