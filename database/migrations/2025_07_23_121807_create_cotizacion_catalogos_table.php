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
        Schema::create('cotizacion_catalogos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_cotizacion')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('cliente_nombre');
            $table->string('cliente_email')->nullable();
            $table->string('cliente_telefono')->nullable();
            $table->text('observaciones')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('igv', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('estado', ['borrador', 'enviada', 'aprobada', 'rechazada'])->default('borrador');
            $table->date('fecha_cotizacion');
            $table->date('fecha_vencimiento')->nullable();
            $table->string('validez_dias')->default('30');
            $table->text('condiciones_pago')->nullable();
            $table->text('condiciones_entrega')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('line_id')->constrained('line_catalogos')->onDelete('cascade')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_catalogos');
    }
};
