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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            // Infomacion de usuario
            $table->foreignId('user_id')->constrained('users');
            // Información del Emisor
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('sucursal_id')->constrained('sucursals');

            // Información del Cliente
            $table->foreignId('client_id')->constrained('clients');

            // Datos del Documento
            $table->string('tipoDoc', 2); // 01-Factura, 03-Boleta
            $table->string('tipoOperacion', 4); // Catálogo 51
            $table->string('serie', 4);
            $table->string('correlativo', 8);
            $table->date('fechaEmision');
            $table->date('fechaVencimiento')->nullable();
            $table->string('formaPago_moneda', 3)->default('PEN');
            $table->string('formaPago_tipo', 2); // Catálogo 59
            $table->string('tipoMoneda', 3)->default('PEN');
            $table->string('estado_pago_invoice')->nullable(); // Cancelado, Por pagar

            // Totales
            $table->decimal('mtoOperGravadas', 12, 2)->default(0); // Operaciones gravadas
            $table->decimal('mtoOperInafectas', 12, 2)->default(0); // Operaciones inafectas
            $table->decimal('mtoOperExoneradas', 12, 2)->default(0); // Operaciones exoneradas
            $table->decimal('mtoOperGratuitas', 12, 2)->default(0); // Operaciones gratuitas
            $table->decimal('mtoIGV', 12, 2)->default(0); // IGV
            $table->decimal('mtoIGVGratuitas', 12, 2)->default(0); // IGV de operaciones gratuitas
            $table->decimal('totalImpuestos', 12, 2)->default(0); // Total impuestos
            $table->decimal('valorVenta', 12, 2)->default(0); // Valor venta
            $table->decimal('subTotal', 12, 2)->default(0); // Subtotal
            $table->decimal('mtoImpVenta', 12, 2)->default(0); // Monto impuesto venta
            $table->string('monto_letras', 500); // Monto en letras

            // Detracción
            $table->string('codBienDetraccion', 3)->nullable(); // Catálogo 54
            $table->string('codMedioPago', 2)->nullable(); // Catálogo 59
            $table->string('ctaBanco', 20)->nullable(); // Número de cuenta
            $table->decimal('setPercent', 5, 2)->nullable(); // Porcentaje detracción
            $table->decimal('setMount', 12, 2)->nullable(); // Monto detracción

            // Percepción
            $table->string('codReg', 2)->nullable(); // Base percepción
            $table->decimal('porcentajePer', 5, 2)->nullable(); // Monto percepción
            $table->decimal('mtoBasePer', 12, 2)->nullable(); // Total percepción
            $table->decimal('mtoPer', 12, 2)->nullable(); // Total percepción
            $table->decimal('mtoTotalPer', 12, 2)->nullable(); // Total percepción

            // Retención
            $table->string('codRegRet', 2)->nullable(); // // Catalog. 53
            $table->decimal('mtoBaseRet', 12, 2)->nullable(); // Total retención
            $table->decimal('factorRet', 12, 2)->nullable(); // Total retención
            $table->decimal('mtoRet', 12, 2)->nullable(); // Total retención

            // Tipo de Venta
            $table->enum('tipoVenta', ['contado', 'credito'])->default('contado'); // Contado o Crédito
            $table->json('cuotas')->nullable(); // Cuotas con monto y fecha de pago

            // Descuentos Globales
            $table->decimal('descuentos_mto', 12, 2)->nullable(); // Monto descuentos

            // Cargos
            $table->decimal('cargos_mto', 12, 2)->nullable(); // Monto cargos

            // Anticipos
            $table->decimal('anticipos_mto', 12, 2)->nullable(); // Monto anticipos

            // Otros campos
            $table->text('observacion')->nullable();
            $table->json('legends')->nullable(); // Leyendas (Catálogo 52)
            $table->json('guias')->nullable(); // Guías de remisión relacionadas
            $table->json('relDocs')->nullable(); // Documentos relacionados
            $table->json('anticipos')->nullable(); // Anticipos
            $table->json('descuentos')->nullable(); // Descuentos globales
            $table->json('cargos')->nullable(); // Cargos
            $table->json('tributos')->nullable(); // Tributos adicionales
            $table->string('note_reference', 100)->nullable();

            // Campos de SUNAT
            $table->string('xml_path')->nullable();
            $table->string('xml_hash')->nullable();
            $table->string('cdr_description')->nullable();
            $table->string('cdr_code')->nullable();
            $table->text('cdr_note')->nullable();
            $table->string('cdr_path')->nullable();
            $table->string('errorCode')->nullable();
            $table->text('errorMessage')->nullable();

            // Campos de exportación
            $table->json('exportacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
