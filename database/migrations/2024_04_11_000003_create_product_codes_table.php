<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_almacen_id')->constrained('product_almacens')->onDelete('cascade');
            $table->string('output_code')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_codes');
    }
};
