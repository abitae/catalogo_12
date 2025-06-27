<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activities_crm', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->string('asunto');
            $table->text('descripcion')->nullable();
            $table->string('estado')->default('pendiente');
            $table->string('prioridad')->default('normal');
            $table->string('image')->nullable();
            $table->string('archivo')->nullable();
            $table->foreignId('opportunity_id')->constrained('opportunities_crm');
            $table->foreignId('contact_id')->constrained('contacts_crm');
            $table->foreignId('user_id')->constrained('users')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities_crm');
    }
};
