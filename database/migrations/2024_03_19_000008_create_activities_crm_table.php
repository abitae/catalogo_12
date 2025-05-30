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
            $table->timestamp('fecha_vencimiento')->nullable();
            $table->string('estado')->default('pendiente');
            $table->string('prioridad')->default('normal');
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('opportunity_id')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('deal_id')->nullable();
            $table->unsignedBigInteger('asignado_a')->nullable();
            $table->timestamp('fecha_completado')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('id')->on('leads_crm');
            $table->foreign('opportunity_id')->references('id')->on('opportunities_crm');
            $table->foreign('deal_id')->references('id')->on('deals_crm');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities_crm');
    }
};
