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
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('sucursal_id')->constrained('sucursals');
            $table->foreignId('client_id')->constrained('clients');
            $table->string('tipoDoc');
            $table->string('tipoOperacion');
            $table->string('serie');
            $table->string('correlativo');
            $table->string('fechaEmision');
            $table->string('formaPago_moneda');
            $table->string('formaPago_tipo');
            $table->string('tipoMoneda');
            $table->decimal('mtoOperGravadas', 8, 2);
            $table->decimal('mtoIGV', 8, 2);
            $table->decimal('totalImpuestos', 8, 2);
            $table->decimal('valorVenta', 8, 2);
            $table->decimal('subTotal', 8, 2);
            $table->decimal('mtoImpVenta', 8, 2);
            $table->string('monto_letras');//monto en letras
            $table->string('codBienDetraccion')->nullable();//catalogo 54
            $table->string('codMedioPago')->nullable();//catalogo 59
            $table->string('ctaBanco')->nullable();//numero de cuenta
            $table->decimal('setPercent', 8, 2)->nullable();//porcentaje detraccion
            $table->decimal('setMount', 8, 2)->nullable();//monto detraccion
            $table->string('observacion')->nullable();
            $table->json('legends')->nullable();
            $table->string('note_reference')->nullable();
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
        Schema::dropIfExists('invoices');
    }
};
