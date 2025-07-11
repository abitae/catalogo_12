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
        Schema::create('producto_acuerdo_marcos', function (Blueprint $table) {
            $table->id();
            $table->string('cod_acuerdo_marco');
            $table->string('ruc_proveedor');
            $table->string('razon_proveedor');
            $table->string('ruc_entidad');
            $table->string('razon_entidad');
            $table->string('unidad_ejecutora');
            $table->string('procedimiento');
            $table->string('tipo');
            $table->string('orden_electronica');
            $table->string('estado_orden_electronica');
            $table->string('link_documento')->nullable();
            $table->string('total_entrega');
            $table->string('num_doc_estado')->nullable();
            $table->string('orden_fisica');
            $table->string('fecha_doc_estado')->nullable();
            $table->string('fecha_estado_oc')->nullable();
            $table->string('sub_total_orden');
            $table->string('igv_orden');
            $table->string('total_orden');
            $table->string('orden_digital_fisica');
            $table->string('sustento_fisica');
            $table->dateTime('fecha_publicacion');
            $table->dateTime('fecha_aceptacion');
            $table->string('usuario_create_oc');
            $table->string('acuerdo_marco')->nullable();
            $table->string('ubigeo_proveedor');
            $table->string('direccion_proveedor');
            $table->string('monto_documento_estado');
            $table->string('catalogo');
            $table->string('categoria');
            $table->text('descripcion_ficha_producto');
            $table->string('marca_ficha_producto');
            $table->string('numero_parte');
            $table->string('link_ficha_producto');
            $table->string('monto_flete');
            $table->string('numero_entrega');
            $table->string('fecha_inicio')->nullable();
            $table->string('plazo_entrega')->nullable();
            $table->string('fecha_fin')->nullable();
            $table->string('cantidad');
            $table->string('entrega_afecto_igv')->nullable();
            $table->string('precio_unitario');
            $table->string('sub_total');
            $table->string('igv_entrega');
            $table->string('total_monto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_acuerdo_marcos');
    }
};
