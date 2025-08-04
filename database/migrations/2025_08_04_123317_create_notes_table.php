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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('sucursal_id')->constrained('sucursals');
            $table->foreignId('client_id')->constrained('clients');
            $table->string('tipoDoc'); // 07: Nota de Crédito, 08: Nota de Débito
            $table->string('tipoOperacion');
            $table->string('serie');
            $table->string('correlativo');
            $table->string('fechaEmision');
            $table->string('formaPago_moneda');
            $table->string('formaPago_tipo');
            $table->string('tipoMoneda');

            // Documento que modifica
            $table->string('tipoDocModifica'); // 01: Factura, 03: Boleta
            $table->string('serieModifica');
            $table->string('correlativoModifica');
            $table->string('fechaEmisionModifica');
            $table->string('tipoMonedaModifica');

            // Motivo de la nota
            $table->string('codMotivo'); // Catálogo 09 para NC, Catálogo 10 para ND
            $table->text('desMotivo');

            // Totales
            $table->decimal('mtoOperGravadas', 12, 2);
            $table->decimal('mtoIGV', 12, 2);
            $table->decimal('totalImpuestos', 12, 2);
            $table->decimal('valorVenta', 12, 2);
            $table->decimal('subTotal', 12, 2);
            $table->decimal('mtoImpVenta', 12, 2);
            $table->string('monto_letras');

            // Campos adicionales
            $table->string('observacion')->nullable();
            $table->json('legends')->nullable();
            $table->string('note_reference')->nullable();

            // Archivos y respuestas SUNAT
            $table->string('xml_path')->nullable();
            $table->string('xml_hash')->nullable();
            $table->string('cdr_description')->nullable();
            $table->string('cdr_code')->nullable();
            $table->text('cdr_note')->nullable();
            $table->string('cdr_path')->nullable();
            $table->string('errorCode')->nullable();
            $table->text('errorMessage')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
