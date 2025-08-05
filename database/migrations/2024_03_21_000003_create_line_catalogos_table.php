<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('line_catalogos', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('fondo')->nullable();
            $table->string('archivo')->nullable();
            $table->string('color')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('line_catalogos');
    }
};
