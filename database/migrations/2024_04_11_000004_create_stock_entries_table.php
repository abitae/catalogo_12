<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_almacen_id')->constrained('product_almacens')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->integer('quantity')->unsigned();
            $table->timestamp('entry_date')->useCurrent();
            $table->timestamps();

            $table->index('warehouse_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_entries');
    }
};
