@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-box-seam-fill me-2"></i>Lista de Produtos</h5>
                        </div>
                        
                        <div class="col-md-4 my-2 my-md-0">
                            <form action="{{ route('produtos.index') }}" method="GET">
                                <input type="hidden" name="sort" value="{{ request('sort', 'nome') }}">
                                <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                                
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 bg-light" 
                                           placeholder="Pesquisar produto..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-primary" type="submit">Buscar</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('produtos.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Novo Produto
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($produtos->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-box2 fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Nenhum produto encontrado.</p>
                            @if(request('search'))
                                <a href="{{ route('produtos.index') }}" class="btn btn-sm btn-outline-secondary">Limpar Filtros</a>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4" style="width: 80px;">
                                            <a href="{{ route('produtos.index', ['sort' => 'id', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none fw-bold text-dark d-block">
                                                ID 
                                                @if(request('sort') == 'id')
                                                    <i class="bi bi-arrow-{{ request('direction', 'asc') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                                @else
                                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                                @endif
                                            </a>
                                        </th>

                                        <th>
                                            <a href="{{ route('produtos.index', ['sort' => 'nome', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none fw-bold text-dark d-block">
                                                Nome 
                                                @if(request('sort', 'nome') == 'nome')
                                                    <i class="bi bi-arrow-{{ request('direction', 'asc') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                                @else
                                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                                @endif
                                            </a>
                                        </th>

                                        <th>
                                            <a href="{{ route('produtos.index', ['sort' => 'preco', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none fw-bold text-dark d-block">
                                                Preço
                                                @if(request('sort') == 'preco')
                                                    <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                                @else
                                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                                @endif
                                            </a>
                                        </th>

                                        <th class="text-center">
                                            <a href="{{ route('produtos.index', ['sort' => 'estoque', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none fw-bold text-dark d-block">
                                                Estoque
                                                @if(request('sort') == 'estoque')
                                                    <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }} text-primary"></i>
                                                @else
                                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                                @endif
                                            </a>
                                        </th>
                                        
                                        <th class="text-center">Status</th>

                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produtos as $produto)
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">#{{ $produto->id }}</td>
                                        <td class="fw-bold text-dark">{{ $produto->nome }}</td>
                                        <td class="text-success fw-bold">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                                        
                                        <td class="text-center">
                                            @if($produto->estoque == 0)
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3">Esgotado</span>
                                            @elseif($produto->estoque < 5)
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3">
                                                    Baixo: {{ $produto->estoque }}
                                                </span>
                                            @else
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">
                                                    {{ $produto->estoque }} unid.
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td class="text-center">
                                            @if($produto->ativo)
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Ativo</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-danger rounded-pill px-3">Inativo</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-produto"
                                                        data-produto-id="{{ $produto->id }}"
                                                        data-produto-nome="{{ $produto->nome }}"
                                                        title="Excluir">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                            
                                            <form id="delete-form-produto-{{ $produto->id }}" action="{{ route('produtos.destroy', $produto->id) }}" method="POST" style="display: none;">
                                                @csrf @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end p-3 border-top">
                            {{ $produtos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.btn-delete-produto'); 
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                const produtoId = this.dataset.produtoId;
                const produtoNome = this.dataset.produtoNome;
                const form = document.getElementById(`delete-form-produto-${produtoId}`);

                Swal.fire({
                    title: 'Tem certeza?',
                    text: `Você vai excluir o produto "${produtoNome}".`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, excluir',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush