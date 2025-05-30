<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leads_crm', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo');
            $table->string('telefono')->nullable();
            $table->string('empresa')->nullable();
            $table->string('estado')->default('nuevo');
            $table->string('origen')->nullable();
            $table->text('notas')->nullable();
            $table->unsignedBigInteger('asignado_a')->nullable();
            $table->timestamp('ultima_fecha_contacto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads_crm');
    }
};
