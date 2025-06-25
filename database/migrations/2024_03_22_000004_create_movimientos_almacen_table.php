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
            $table->foreignId('user_id')->constrained('users');
            $table->string('tipo_pago')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('tipo_operacion')->nullable();
            $table->string('forma_pago')->nullable();
            $table->string('tipo_moneda')->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->json('productos')->nullable();
            $table->string('estado')->nullable();
            $table->text('observaciones')->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('descuento', 10, 2)->nullable();
            $table->decimal('impuesto', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos_almacen');
    }
};
