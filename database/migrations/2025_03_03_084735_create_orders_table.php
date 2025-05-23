<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_create_orders_table.php
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pendiente', 'procesando', 'enviado', 'entregado', 'cancelado'])->default('pendiente');
            $table->string('payment_method');
            $table->enum('payment_status', ['pendiente', 'pagado', 'reembolsado'])->default('pendiente');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('document');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Para mantener historial de compras
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
