<?php

namespace App\Http\Controllers;

use App\Models\Cliente; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
   
    public function index()
    {
        $clientes = Cliente::orderBy('nome', 'asc')->paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    
    public function create()
    {
        return view('clientes.create');
    }

    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome'      => 'required|string|max:255',
            'cpf'       => 'required|string|max:14|unique:clientes,cpf', 
            'telefone'  => 'required|string|max:20', 
        ]);
       

        try {
            Cliente::create($validatedData);
            return redirect()->route('clientes.index')
                             ->with('success', 'Cliente cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao cadastrar cliente: ' . $e->getMessage());
            return redirect()->route('clientes.create')
                             ->with('error', 'Erro ao cadastrar o cliente. Verifique os dados e tente novamente.');
        }
    }

   
    public function show(Cliente $cliente)
    {
        return "Detalhes do Cliente: " . $cliente->nome . " (a ser implementado)";
    }

    
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validatedData = $request->validate([
            'nome'      => 'required|string|max:255',
            'cpf'       => ['required', 'string', 'max:14', Rule::unique('clientes')->ignore($cliente->id)],
            'telefone'  => 'required|string|max:20',
        ]);

        try {
            $cliente->update($validatedData);
            return redirect()->route('clientes.index')
                             ->with('success', "Cliente '{$cliente->nome}' atualizado com sucesso!");
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar cliente (ID: ' . $cliente->id . '): ' . $e->getMessage());
            return redirect()->route('clientes.edit', $cliente->id)
                             ->with('error', 'Erro ao atualizar o cliente. Verifique os dados e tente novamente.');
        }
    }

    
    public function destroy(Cliente $cliente)
    {
        try {
            $nomeCliente = $cliente->nome;
            $cliente->delete();
            return redirect()->route('clientes.index')
                             ->with('success', "Cliente '{$nomeCliente}' excluído com sucesso!");
        } catch (\Exception $e) {
            Log::error('Erro ao excluir cliente (ID: ' . $cliente->id . '): ' . $e->getMessage());
            return redirect()->route('clientes.index')
                             ->with('error', 'Erro ao excluir o cliente. Tente novamente.');
        }
    }

 public function buscar(Request $request)
    {
        $termo = $request->input('termo');
        $query = Cliente::query()->select('id', 'nome', 'cpf');

        if (!empty($termo)) {
           
            $query->where(function ($q) use ($termo) {
                $q->where('nome', 'LIKE', "%{$termo}%")
                  ->orWhere('cpf', 'LIKE', "%{$termo}%");
            });
        } else {
            $query->orderBy('updated_at', 'desc');
        }
        $clientes = $query->take(5)->get(); 

        return response()->json($clientes);
    }

}