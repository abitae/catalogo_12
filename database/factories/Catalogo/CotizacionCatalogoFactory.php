<?php

namespace Database\Factories\Catalogo;

use App\Models\Catalogo\CotizacionCatalogo;
use App\Models\Shared\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalogo\CotizacionCatalogo>
 */
class CotizacionCatalogoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CotizacionCatalogo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
                public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first() ?? Customer::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'codigo_cotizacion' => (new CotizacionCatalogo())->generarCodigo(),
            'customer_id' => $customer->id,
            'cliente_nombre' => $customer->rznSocial ?: $this->faker->company(),
            'cliente_email' => $customer->email ?: $this->faker->email(),
            'cliente_telefono' => $this->faker->phoneNumber(),
            'observaciones' => $this->faker->optional()->paragraph(),
            'subtotal' => $this->faker->randomFloat(2, 100, 10000),
            'igv' => $this->faker->randomFloat(2, 18, 1800),
            'total' => $this->faker->randomFloat(2, 118, 11800),
            'estado' => $this->faker->randomElement(['borrador', 'enviada', 'aprobada', 'rechazada']),
            'fecha_cotizacion' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'fecha_vencimiento' => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
            'validez_dias' => '15',
            'condiciones_pago' => $this->faker->optional()->sentence(),
            'condiciones_entrega' => $this->faker->optional()->sentence(),
            'user_id' => $user->id,
        ];
    }
}
