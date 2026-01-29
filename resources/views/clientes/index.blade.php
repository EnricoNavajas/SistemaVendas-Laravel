@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-people-fill me-2"></i>Lista de Clientes</h5>
                        </div>
                        
                        <div class="col-md-4 my-2 my-md-0">
                            <form action="{{ route('clientes.index') }}" method="GET">
                                <input type="hidden" name="sort" value="{{ request('sort', 'id') }}">
                                <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                                
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 bg-light" 
                                           placeholder="Pesquisar..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-primary" type="submit">Buscar</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                                <i class="bi bi-person-plus-fill me-1"></i> Novo Cliente
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($clientes->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-emoji-frown fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Nenhum cliente encontrado.</p>
                            @if(request('search'))
                                <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-secondary">Limpar Filtros</a>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4" style="width: 80px;">
                                            <a href="{{ route('clientes.index', ['sort' => 'id', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none fw-bold text-dark d-block">
                                                ID 
                                                @if(request('sort', 'id') == 'id')
                                                    <i class="bi bi-arrow-{{ request('direction', 'asc') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                                @else
                                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                                @endif
                                            </a>
                                        </th>

                                        <th>
                                            <a href="{{ route('clientes.index', ['sort' => 'nome', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none fw-bold text-dark d-block">
                                                Nome 
                                                @if(request('sort') == 'nome')
                                                    <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                                @else
                                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                                @endif
                                            </a>
                                        </th>

                                        <th>
                                            <a href="{{ route('clientes.index', ['sort' => 'cpf', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none fw-bold text-dark d-block">
                                                CPF
                                                @if(request('sort') == 'cpf')
                                                    <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                                @else
                                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                                @endif
                                            </a>
                                        </th>

                                        <th>Telefone</th>
                                        
                                        <th class="text-center">
                                            <a href="{{ route('clientes.index', ['sort' => 'ativo', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none fw-bold text-dark d-block">
                                                Status
                                                @if(request('sort') == 'ativo')
                                                    <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                                @else
                                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                                @endif
                                            </a>
                                        </th>

                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clientes as $cliente)
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">#{{ $cliente->id }}</td>
                                        <td class="fw-bold text-dark">{{ $cliente->nome }}</td>
                                        <td class="text-secondary">{{ $cliente->cpf }}</td>
                                        <td class="text-secondary">{{ $cliente->telefone }}</td>
                                        
                                        <td class="text-center">
                                            @if($cliente->ativo)
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Ativo</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Inativo</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-cliente"
                                                        data-cliente-id="{{ $cliente->id }}"
                                                        data-cliente-nome="{{ $cliente->nome }}"
                                                        title="Excluir">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                            <form id="delete-form-cliente-{{ $cliente->id }}" action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" style="display: none;">
                                                @csrf @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end p-3 border-top">
                            {{ $clientes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
