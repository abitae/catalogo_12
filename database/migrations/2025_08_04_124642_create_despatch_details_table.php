<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('despatch_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('despatch_id')->constrained('despatches');
            $table->string('unidad');
            $table->decimal('cantidad', 12, 2);
            $table->string('codProducto');
            $table->string('codProdSunat')->nullable();
            $table->string('codProdGS1')->nullable();
            $table->text('descripcion');
            $table->decimal('pesoBruto', 12, 2)->nullable();
            $table->decimal('pesoNeto', 12, 2)->nullable();
            $table->string('codLote')->nullable();
            $table->string('fechaVencimiento')->nullable();
            $table->string('codigoUnidadMedida')->nullable();
            $table->string('codigoProductoSUNAT')->nullable();
            $table->string('codigoProductoGS1')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despatch_details');
    }
};
