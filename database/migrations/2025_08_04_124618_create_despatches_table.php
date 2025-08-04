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
        Schema::create('despatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('sucursal_id')->constrained('sucursals');
            $table->foreignId('client_id')->constrained('clients');
            $table->string('tipoDoc'); // 09: Guía de Remisión Remitente
            $table->string('serie');
            $table->string('correlativo');
            $table->string('fechaEmision');
            $table->string('tipoMoneda');

            // Destinatario
            $table->string('tipoDocDestinatario');
            $table->string('numDocDestinatario');
            $table->string('rznSocialDestinatario');
            $table->string('direccionDestinatario');
            $table->string('ubigeoDestinatario')->nullable();

            // Transportista
            $table->string('tipoDocTransportista')->nullable();
            $table->string('numDocTransportista')->nullable();
            $table->string('rznSocialTransportista')->nullable();
            $table->string('placaVehiculo')->nullable();
            $table->string('codEstabDestino')->nullable();

            // Dirección de partida
            $table->string('direccionPartida');
            $table->string('ubigeoPartida')->nullable();

            // Dirección de llegada
            $table->string('direccionLlegada');
            $table->string('ubigeoLlegada')->nullable();

            // Fechas de traslado
            $table->string('fechaInicioTraslado');
            $table->string('fechaFinTraslado')->nullable();

            // Motivo de traslado
            $table->string('codMotivoTraslado'); // Catálogo 20
            $table->text('desMotivoTraslado');

            // Indicadores
            $table->boolean('indicadorTransbordo')->default(false);
            $table->decimal('pesoBrutoTotal', 12, 2)->nullable();
            $table->integer('numeroBultos')->nullable();
            $table->string('modalidadTraslado'); // Catálogo 18

            // Documentos relacionados
            $table->json('documentosRelacionados')->nullable();

            // Campos adicionales
            $table->string('observacion')->nullable();
            $table->json('legends')->nullable();

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
        Schema::dropIfExists('despatches');
    }
};
