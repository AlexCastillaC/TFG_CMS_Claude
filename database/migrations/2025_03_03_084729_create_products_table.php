<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_create_products_table.php
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('stand_id')->nullable()->constrained()->onDelete('set null');
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 8, 2);
        $table->integer('stock')->default(0);
        $table->string('image')->nullable();
        $table->string('category')->nullable();
        $table->boolean('is_available')->default(true);
        $table->timestamps();
        $table->softDeletes(); // Para mantener referencia en pedidos históricos
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
