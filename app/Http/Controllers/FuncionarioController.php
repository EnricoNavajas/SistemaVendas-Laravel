<?php

namespace App\Http\Controllers;

use App\Models\Funcionario; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class FuncionarioController extends Controller
{
    
    public function index(Request $request)
    {
        $sort = $request->input('sort') ?: 'id';
        $direction = $request->input('direction') ?: 'asc';

        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $query = Funcionario::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'LIKE', "%{$search}%")
                  ->orWhere('cpf', 'LIKE', "%{$search}%");
            });
        }

        $funcionarios = $query->orderBy($sort, $direction)->paginate(10);

        $funcionarios->appends([
            'search' => $request->search,
            'sort' => $sort,
            'direction' => $direction
        ]);

        return view('funcionarios.index', compact('funcionarios'));
    }

   
    public function create()
    {
        return view('funcionarios.create');
    }

    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome'  => 'required|string|max:255',
            'cpf'   => 'required|string|max:14|unique:funcionarios,cpf', 
            'senha' => 'required|string|min:8|confirmed', 
        ]);

        try {
            Funcionario::create([
                'nome' => $validatedData['nome'],
                'cpf' => $validatedData['cpf'],
                'senha' => Hash::make($validatedData['senha']), 
            ]);
            return redirect()->route('funcionarios.index')
                             ->with('success', 'Funcionário cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao cadastrar funcionário: ' . $e->getMessage());
            return redirect()->route('funcionarios.create')
                             ->with('error', 'Erro ao cadastrar o funcionário. Verifique os dados e tente novamente.');
        }
    }


    public function show(Funcionario $funcionario)
    {
       
        return "Detalhes do Funcionário: " . $funcionario->nome . " (a ser implementado, se necessário)";
    }

   
    public function edit(Funcionario $funcionario)
    {
        return view('funcionarios.edit', compact('funcionario'));
    }

    
    public function update(Request $request, Funcionario $funcionario)
    {
        $validatedData = $request->validate([
            'nome'  => 'required|string|max:255',
            'cpf'   => ['required', 'string', 'max:14', Rule::unique('funcionarios')->ignore($funcionario->id)],
            'senha' => 'nullable|string|min:8|confirmed', 
            'ativo' => 'boolean',
        ]);

        try {
            $updateData = [
                'nome' => $validatedData['nome'],
                'cpf' => $validatedData['cpf'],
                'ativo' => $validatedData['ativo'] ?? $funcionario->ativo,
            ];

            if (!empty($validatedData['senha'])) {
                $updateData['senha'] = Hash::make($validatedData['senha']);
            }

            $funcionario->update($updateData);

            return redirect()->route('funcionarios.index')
                             ->with('success', "Dados do funcionário '{$funcionario->nome}' atualizados com sucesso!");
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar funcionário (ID: ' . $funcionario->id . '): ' . $e->getMessage());
            return redirect()->route('funcionarios.edit', $funcionario->id)
                             ->with('error', 'Erro ao atualizar o funcionário. Verifique os dados e tente novamente.');
        }
    }

      public function destroy(Funcionario $funcionario)
    {
      
        if (Auth::id() == $funcionario->id) {
            return redirect()->route('funcionarios.index')
                             ->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        try {
            $nomeFuncionario = $funcionario->nome;
            $funcionario->delete();
            return redirect()->route('funcionarios.index')
                             ->with('success', "Funcionário '{$nomeFuncionario}' excluído com sucesso!");
        } catch (\Exception $e) {
            Log::error('Erro ao excluir funcionário (ID: ' . $funcionario->id . '): ' . $e->getMessage());
            return redirect()->route('funcionarios.index')
                             ->with('error', 'Erro ao excluir o funcionário. Tente novamente.');
        }
    }
}