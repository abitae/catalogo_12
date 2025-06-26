<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('productos_almacen', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->json('codes_exit')->nullable();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('categoria');
            $table->string('unidad_medida');
            $table->decimal('stock_minimo', 10, 2);
            $table->decimal('stock_actual', 10, 2);
            $table->decimal('precio_unitario', 10, 2);
            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->string('lote')->nullable();
            $table->boolean('estado')->default(true);
            $table->string('codigo_barras')->nullable();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos_almacen');
    }
};
