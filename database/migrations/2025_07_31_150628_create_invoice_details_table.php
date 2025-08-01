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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->string('unidad');
            $table->decimal('cantidad', 8, 2)->default(0);// 2
            $table->string('codProducto')->nullable();
            $table->string('codProdSunat')->nullable();
            $table->string('codProdGS1')->nullable();
            $table->string('descripcion');
            $table->string('tipAfeIgv');
            $table->decimal('mtoValorUnitario', 8, 2)->default(0);// 100
            $table->decimal('mtoValorVenta', 8, 2)->default(0);// 200
            $table->decimal('descuento', 8, 2)->default(0);// 0
            $table->decimal('mtoBaseIgv', 8, 2)->default(0);// 200
            $table->decimal('totalImpuestos', 8, 2)->default(0);// 36
            $table->decimal('porcentajeIgv', 8, 2)->default(0);// 18
            $table->decimal('igv', 8, 2)->default(0);// 36
            $table->decimal('mtoPrecioUnitario', 8, 2)->default(0);// 100
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
