@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Lista de Produtos') }}</span>
                        <a href="{{ route('produtos.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Novo Produto
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($produtos->isEmpty())
                        <div class="alert alert-info" role="alert">
                            Nenhum produto cadastrado ainda.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nome</th>
                                        <th scope="col">Preço (R$)</th>
                                        <th scope="col">Cadastrado em</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produtos as $produto)
                                    <tr>
                                        <th scope="row">{{ $produto->id }}</th>
                                        <td>{{ $produto->nome }}</td>
                                        <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                                        <td>{{ $produto->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-sm btn-info me-1" title="Editar">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-produto"
                                                    data-produto-id="{{ $produto->id }}"
                                                    data-produto-nome="{{ $produto->nome }}"
                                                    title="Excluir">
                                                <i class="bi bi-trash3"></i> Excluir
                                            </button>
                                            <form id="delete-form-{{ $produto->id }}" action="{{ route('produtos.destroy', $produto->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-center">
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
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 (Swal) não está definido. Verifique a instalação e importação.');
        return;
    }
    
    const deleteButtons = document.querySelectorAll('.btn-delete-produto');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const produtoId = this.dataset.produtoId;
            const produtoNome = this.dataset.produtoNome;
            const form = document.getElementById(`delete-form-${produtoId}`);

            if (!form) {
                console.error('Formulário de exclusão não encontrado para o produto ID:', produtoId);
                Swal.fire('Erro Interno!', 'Não foi possível encontrar o formulário de exclusão.', 'error');
                return;
            }

            Swal.fire({
                title: 'Confirmar Exclusão',
                text: `Tem certeza que deseja excluir o produto "${produtoNome}"? Esta ação não poderá ser revertida!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
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