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
        Schema::create('venda_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venda_id')
                  ->constrained('vendas')
                  ->onDelete('cascade'); 

            $table->foreignId('produto_id')
                  ->constrained('produtos')
                  ->onDelete('restrict'); 
                                         
            $table->integer('quantidade');
            $table->decimal('preco_unit', 10, 2);
            $table->decimal('subtotal', 10, 2);   
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda_itens');
    }
};