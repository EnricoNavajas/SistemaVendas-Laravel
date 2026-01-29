<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EstoqueController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort') ?: 'id';
        $direction = $request->input('direction') ?: 'asc';
        
        $query = Produto::query();

        if ($request->filled('search')) {
            $query->where('nome', 'LIKE', "%{$request->search}%");
        }

        $produtos = $query->where('ativo', true)
                          ->orderBy($sort, $direction)
                          ->paginate(10); 

        $produtos->appends([
            'search' => $request->search,
            'sort' => $sort,
            'direction' => $direction
        ]);

        return view('estoque.index', compact('produtos'));
    }

    public function atualizar(Request $request, Produto $produto)
    {
        $request->validate([
            'nova_quantidade' => 'required|integer|min:0'
        ]);

        try {
            $novoEstoque = $request->nova_quantidade;
            
            $produto->estoque = $novoEstoque;
            $produto->save();

            return response()->json([
                'success' => true, 
                'message' => "Estoque de '{$produto->nome}' atualizado para {$novoEstoque}."
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao atualizar estoque AJAX: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Erro ao salvar.'
            ], 500);
        }
    }
}