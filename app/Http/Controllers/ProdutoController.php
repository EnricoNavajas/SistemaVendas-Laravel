<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; 

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort') ?: 'id'; 
        $direction = $request->input('direction') ?: 'asc';

        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $query = Produto::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nome', 'LIKE', "%{$search}%");
        }

        $produtos = $query->orderBy($sort, $direction)->paginate(10);

        $produtos->appends([
            'search' => $request->search,
            'sort' => $sort,
            'direction' => $direction
        ]);

        return view('produtos.index', compact('produtos'));
    }

   
    public function create()
    {
        return view('produtos.create');
    }

    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome'  => 'required|string|max:255|unique:produtos,nome',
            'preco'   => 'required|string',
            'estoque' => 'required|integer|min:0',
        ]);

        $validatedData['ativo'] = true;

       Produto::create($validatedData);

        return redirect()->route('produtos.index')
                         ->with('success', 'Produto cadastrado com sucesso!');
    }

    public function show(Produto $produto)
    {  
        return "Detalhes do Produto: " . $produto->nome . " (método show a ser implementado, se necessário)";
    }

   
    public function edit(Produto $produto) 
    {
        
        return view('produtos.edit', compact('produto'));
    }

       public function update(Request $request, Produto $produto)
    {
        $validatedData = $request->validate([
            'nome'    => 'required|string|max:255',
            'preco'   => 'required|string',
            'estoque' => 'required|integer|min:0',
            'ativo'   => 'boolean',
        ]);
        
        $produto->update($validatedData);

        return redirect()->route('produtos.index')
                         ->with('success', 'Produto atualizado com sucesso!');
    }

   
    public function destroy(Produto $produto)
    {
        try {
            $nomeProduto = $produto->nome;
            $produto->delete();
            return redirect()->route('produtos.index')
                             ->with('success', "Produto '{$nomeProduto}' excluído com sucesso!");
        } catch (\Exception $e) {
            Log::error('Erro ao excluir produto (ID: ' . $produto->id . '): ' . $e->getMessage()); 
            return redirect()->route('produtos.index')
                             ->with('error', 'Erro ao excluir o produto. Tente novamente.');
        }
    }

    
public function buscar(Request $request) {
    $termo = $request->input('termo');
    $query = \App\Models\Produto::query()->select('id', 'nome', 'preco'); 

    if (!empty($termo)) {
        $query->where('nome', 'LIKE', "%{$termo}%");
       
    } else {
      
        $query->orderBy('updated_at', 'desc'); 
    }
    
    $resultados = $query->take(5)->get();
    return response()->json($resultados);
}
    

}