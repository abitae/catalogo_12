<?php

namespace Database\Factories\Pc;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pc\ProductoAcuerdoMarco>
 */
class ProductoAcuerdoMarcoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marcas = ['HP', 'Dell', 'Lenovo', 'Cisco', 'Microsoft', 'Adobe', 'Oracle', 'SAP'];
        $categorias = ['Hardware', 'Software', 'Servicios', 'Equipos de Red', 'Periféricos', 'Licencias'];
        $procedimientos = ['Licitación Pública', 'Adjudicación Directa', 'Concurso Público'];
        $tipos = ['Bienes', 'Servicios', 'Obras'];
        $estados = ['Activo', 'Pendiente', 'Finalizado', 'Cancelado'];

        return [
            'cod_acuerdo_marco' => $this->faker->randomElement(['EXT-CE-2024-11', 'AM-2024-001', 'AM-2024-002', 'AM-2024-003']),
            'ruc_proveedor' => $this->faker->numerify('20########'),
            'razon_proveedor' => $this->faker->company(),
            'ruc_entidad' => $this->faker->numerify('20########'),
            'razon_entidad' => $this->faker->company(),
            'unidad_ejecutora' => $this->faker->department(),
            'procedimiento' => $this->faker->randomElement($procedimientos),
            'tipo' => $this->faker->randomElement($tipos),
            'orden_electronica' => $this->faker->numerify('OE-####-####'),
            'estado_orden_electronica' => $this->faker->randomElement($estados),
            'link_documento' => $this->faker->url(),
            'total_entrega' => $this->faker->randomFloat(2, 1000, 50000),
            'num_doc_estado' => $this->faker->numerify('DOC-####'),
            'orden_fisica' => $this->faker->numerify('OF-####'),
            'fecha_doc_estado' => $this->faker->date(),
            'fecha_estado_oc' => $this->faker->date(),
            'sub_total_orden' => $this->faker->randomFloat(2, 800, 40000),
            'igv_orden' => $this->faker->randomFloat(2, 200, 10000),
            'total_orden' => $this->faker->randomFloat(2, 1000, 50000),
            'orden_digital_fisica' => $this->faker->randomElement(['Digital', 'Física', 'Mixta']),
            'sustento_fisica' => $this->faker->sentence(),
            'fecha_publicacion' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'fecha_aceptacion' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'usuario_create_oc' => $this->faker->name(),
            'acuerdo_marco' => $this->faker->sentence(3),
            'ubigeo_proveedor' => $this->faker->numerify('15####'),
            'direccion_proveedor' => $this->faker->address(),
            'monto_documento_estado' => $this->faker->randomFloat(2, 1000, 50000),
            'catalogo' => $this->faker->word(),
            'categoria' => $this->faker->randomElement($categorias),
            'descripcion_ficha_producto' => $this->faker->sentence(10),
            'marca_ficha_producto' => $this->faker->randomElement($marcas),
            'numero_parte' => $this->faker->numerify('PART-####'),
            'link_ficha_producto' => $this->faker->url(),
            'monto_flete' => $this->faker->randomFloat(2, 50, 500),
            'numero_entrega' => $this->faker->numerify('ENT-###'),
            'fecha_inicio' => $this->faker->date(),
            'plazo_entrega' => $this->faker->numberBetween(1, 90),
            'fecha_fin' => $this->faker->date(),
            'cantidad' => $this->faker->numberBetween(1, 100),
            'entrega_afecto_igv' => $this->faker->randomFloat(2, 800, 40000),
            'precio_unitario' => $this->faker->randomFloat(2, 100, 5000),
            'sub_total' => $this->faker->randomFloat(2, 800, 40000),
            'igv_entrega' => $this->faker->randomFloat(2, 200, 10000),
            'total_monto' => $this->faker->randomFloat(2, 1000, 50000),
        ];
    }
}
