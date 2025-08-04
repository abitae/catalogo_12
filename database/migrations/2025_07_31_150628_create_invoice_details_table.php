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
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');

            // Información del Producto
            $table->string('unidad', 3); // Unidad de medida (Catálogo 03)
            $table->decimal('cantidad', 12, 3)->default(0); // Cantidad
            $table->string('codProducto', 30)->nullable(); // Código del producto
            $table->string('codProdSunat', 30)->nullable(); // Código SUNAT del producto
            $table->string('codProdGS1', 30)->nullable(); // Código GS1 del producto
            $table->text('descripcion'); // Descripción del producto

            // Tipo de Afectación IGV
            $table->string('tipAfeIgv', 2); // Catálogo 07: 10-Gravado, 20-Exonerado, 30-Inafecto, 40-Exportación

            // Valores
            $table->decimal('mtoValorUnitario', 12, 2)->default(0); // Valor unitario sin impuestos
            $table->decimal('mtoValorVenta', 12, 2)->default(0); // Valor de venta sin impuestos
            $table->decimal('descuento', 12, 2)->default(0); // Descuento
            $table->decimal('mtoBaseIgv', 12, 2)->default(0); // Base imponible IGV
            $table->decimal('totalImpuestos', 12, 2)->default(0); // Total impuestos
            $table->decimal('porcentajeIgv', 5, 2)->default(18.00); // Porcentaje IGV
            $table->decimal('igv', 12, 2)->default(0); // IGV

            // Precios
            $table->decimal('mtoPrecioUnitario', 12, 2)->default(0); // Precio unitario sin impuestos

            // Campos adicionales para GREENTER
            $table->decimal('mtoOperGratuitas', 12, 2)->default(0); // Operaciones gratuitas
            $table->decimal('mtoIGVGratuitas', 12, 2)->default(0); // IGV de operaciones gratuitas
            $table->decimal('mtoOperInafectas', 12, 2)->default(0); // Operaciones inafectas
            $table->decimal('mtoOperExoneradas', 12, 2)->default(0); // Operaciones exoneradas

            // Anticipos
            $table->decimal('anticipo_mtoBase', 12, 2)->nullable(); // Base anticipo
            $table->decimal('anticipo_mto', 12, 2)->nullable(); // Monto anticipo

            // Tributos adicionales
            $table->json('tributos')->nullable(); // Tributos adicionales

            // Información adicional
            $table->string('codBienDetraccion', 3)->nullable(); // Catálogo 54
            $table->string('codMedioPago', 2)->nullable(); // Catálogo 59
            $table->string('ctaBanco', 20)->nullable(); // Número de cuenta

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
