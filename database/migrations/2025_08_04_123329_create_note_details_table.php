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
        Schema::create('note_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained('notes');
            $table->string('unidad');
            $table->decimal('cantidad', 12, 2);
            $table->string('codProducto');
            $table->string('codProdSunat')->nullable();
            $table->string('codProdGS1')->nullable();
            $table->text('descripcion');
            $table->string('tipAfeIgv');
            $table->decimal('mtoValorUnitario', 12, 2);
            $table->decimal('mtoValorVenta', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('mtoBaseIgv', 12, 2);
            $table->decimal('totalImpuestos', 12, 2);
            $table->decimal('porcentajeIgv', 5, 2);
            $table->decimal('igv', 12, 2);
            $table->decimal('mtoPrecioUnitario', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_details');
    }
};
