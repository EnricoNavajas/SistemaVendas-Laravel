<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; 

class ProdutoController extends Controller
{
        public function index()
    {
        $produtos = Produto::orderBy('created_at', 'desc')->paginate(10);
        return view('produtos.index', ['produtos' => $produtos]);
    }

   
    public function create()
    {
        return view('produtos.create');
    }

    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome'  => 'required|string|max:255|unique:produtos,nome',
            'preco' => 'required|numeric|min:0.01',
        ]);

        try {
            Produto::create($validatedData);
            return redirect()->route('produtos.index')
                             ->with('success', 'Produto cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao cadastrar produto: ' . $e->getMessage()); // Linha de log descomentada e ajustada
            return redirect()->route('produtos.create')
                             ->with('error', 'Erro ao cadastrar o produto. Tente novamente.');
        }
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
            'nome'  => [
                'required',
                'string',
                'max:255',
                Rule::unique('produtos')->ignore($produto->id), 
            ],
            'preco' => 'required|numeric|min:0.01',
        ]);

       
        try {
            $produto->update([ 
                'nome' => $validatedData['nome'],
                'preco' => $validatedData['preco'],
            ]);

            return redirect()->route('produtos.index')
                             ->with('success', "Produto '{$produto->nome}' atualizado com sucesso!");

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produto (ID: ' . $produto->id . '): ' . $e->getMessage());
            return redirect()->route('produtos.edit', $produto->id) 
                             ->with('error', 'Erro ao atualizar o produto. Tente novamente.');
        }
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