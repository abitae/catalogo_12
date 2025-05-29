<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('producto_catalogos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brand_catalogos')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('category_catalogos')->onDelete('cascade');
            $table->foreignId('line_id')->constrained('line_catalogos')->onDelete('cascade');
            $table->string('code');
            $table->string('code_fabrica')->nullable();
            $table->string('code_peru')->nullable();
            $table->decimal('price_compra', 10, 2);
            $table->decimal('price_venta', 10, 2);
            $table->integer('stock')->default(0);
            $table->integer('dias_entrega')->default(0);
            $table->text('description')->nullable();
            $table->string('garantia')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('image')->nullable();
            $table->string('archivo')->nullable();
            $table->string('archivo2')->nullable();
            $table->json('caracteristicas')->default('[]')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('producto_catalogos');
    }
};