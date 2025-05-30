<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('almacenes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nombre');
            $table->string('direccion');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->boolean('estado')->default(true);
            $table->decimal('capacidad', 10, 2);
            $table->string('responsable');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('almacenes');
    }
};
