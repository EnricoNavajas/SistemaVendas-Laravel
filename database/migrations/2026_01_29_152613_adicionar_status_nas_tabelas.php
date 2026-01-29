<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('telefone'); 
        });

        Schema::table('funcionarios', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('senha');
        });

        Schema::table('produtos', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('preco');
            $table->integer('estoque')->default(0)->after('ativo');
        });

        Schema::table('vendas', function (Blueprint $table) {
            $table->string('status')->default('pendente')->after('total');
            
            $table->dateTime('data_aprovacao')->nullable()->after('status');
        });
    }

    public function down()
    {
        // Remove tudo se der rollback
        Schema::table('clientes', function (Blueprint $table) { $table->dropColumn('ativo'); });
        Schema::table('funcionarios', function (Blueprint $table) { $table->dropColumn('ativo'); });
        Schema::table('produtos', function (Blueprint $table) { $table->dropColumn(['ativo', 'estoque']); });
        Schema::table('vendas', function (Blueprint $table) { $table->dropColumn(['status', 'data_aprovacao']); });
    }
};