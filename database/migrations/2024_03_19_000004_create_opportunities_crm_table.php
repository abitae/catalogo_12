<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('opportunities_crm', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('estado')->default('nueva');
            $table->decimal('valor', 10, 2);
            $table->string('etapa');
            $table->integer('probabilidad')->nullable();
            $table->timestamp('fecha_cierre_esperada')->nullable();
            $table->string('fuente')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('notas')->nullable();
            $table->string('image')->nullable();
            $table->string('archivo')->nullable();
            $table->timestamp('ultima_fecha_actividad')->nullable();
            $table->foreignId('tipo_negocio_id')->nullable()->constrained('tipos_negocio_crm');
            $table->foreignId('marca_id')->nullable()->constrained('marcas_crm');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('contact_id')->nullable()->constrained('contacts_crm');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('opportunities_crm');
    }
};
