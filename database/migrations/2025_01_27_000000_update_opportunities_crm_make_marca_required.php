<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('opportunities_crm', function (Blueprint $table) {
            // Hacer marca_id obligatorio
            $table->foreignId('marca_id')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('opportunities_crm', function (Blueprint $table) {
            // Revertir marca_id a nullable
            $table->foreignId('marca_id')->nullable()->change();
        });
    }
};
