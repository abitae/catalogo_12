<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_exits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_code_id')->constrained('product_codes')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->integer('quantity')->unsigned();
            $table->timestamp('exit_date')->useCurrent();
            $table->timestamps();

            $table->index('product_code_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_exits');
    }
};
