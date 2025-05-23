<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_create_provider_sales_table.php
public function up()
{
    Schema::create('provider_sales', function (Blueprint $table) {
        $table->id();
        $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
        $table->decimal('total', 10, 2);
        $table->enum('status', ['pendiente', 'procesando', 'enviado', 'entregado', 'cancelado'])->default('pendiente');
        $table->string('payment_method');
        $table->enum('payment_status', ['pendiente', 'pagado', 'reembolsado'])->default('pendiente');
        $table->timestamps();
        $table->softDeletes(); // Para mantener historial de transacciones
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_sales');
    }
};
