<?php

namespace App\Models\Pc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoAcuerdoMarco extends Model
{
    /** @use HasFactory<\Database\Factories\Pc\ProductoAcuerdoMarcoFactory> */
    use HasFactory;
    protected $fillable = [
        'cod_acuerdo_marco',
        'ruc_proveedor',
        'razon_proveedor',
        'ruc_entidad',
        'razon_entidad',
        'unidad_ejecutora',
        'procedimiento',
        'tipo',
        'orden_electronica',
        'estado_orden_electronica',
        'link_documento',
        'total_entrega',
        'num_doc_estado',
        'orden_fisica',
        'fecha_doc_estado',
        'fecha_estado_oc',
        'sub_total_orden',
        'igv_orden',
        'total_orden',
        'orden_digital_fisica',
        'sustento_fisica',
        'fecha_publicacion',
        'fecha_aceptacion',
        'usuario_create_oc',
        'acuerdo_marco',
        'ubigeo_proveedor',
        'direccion_proveedor',
        'monto_documento_estado',
        'catalogo',
        'categoria',
        'descripcion_ficha_producto',
        'marca_ficha_producto',
        'numero_parte',
        'link_ficha_producto',
        'monto_flete',
        'numero_entrega',
        'fecha_inicio',
        'plazo_entrega',
        'fecha_fin',
        'cantidad',
        'entrega_afecto_igv',
        'precio_unitario',
        'sub_total',
        'igv_entrega',
        'total_monto',
    ];
}
