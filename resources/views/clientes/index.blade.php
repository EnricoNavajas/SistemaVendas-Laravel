@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Lista de Clientes') }}</span>
                        <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-person-plus-fill me-1"></i>Novo Cliente
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    @if($clientes->isEmpty())
                        <div class="alert alert-info" role="alert">
                            Nenhum cliente cadastrado ainda.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nome</th>
                                        <th scope="col">CPF</th>
                                        <th scope="col">Telefone</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clientes as $cliente)
                                    <tr>
                                        <th scope="row">{{ $cliente->id }}</th>
                                        <td>{{ $cliente->nome }}</td>
                                        <td>{{ $cliente->cpf }}</td>
                                        <td>{{ $cliente->telefone }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-sm btn-info me-1" title="Editar">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                           
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-cliente"
                                                    data-cliente-id="{{ $cliente->id }}"
                                                    data-cliente-nome="{{ $cliente->nome }}"
                                                    title="Excluir">
                                                <i class="bi bi-trash3"></i> Excluir
                                            </button>
                                           
                                            <form id="delete-form-cliente-{{ $cliente->id }}" action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" style="display: none;">
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
                            {{ $clientes->links() }}
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
      
    const deleteClienteButtons = document.querySelectorAll('.btn-delete-cliente'); 
    deleteClienteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const clienteId = this.dataset.clienteId;        
            const clienteNome = this.dataset.clienteNome;     
            const form = document.getElementById(`delete-form-cliente-${clienteId}`); 

            if (!form) {
                console.error('Formulário de exclusão não encontrado para o cliente ID:', clienteId);
                Swal.fire('Erro Interno!', 'Não foi possível encontrar o formulário de exclusão.', 'error');
                return;
            }

            Swal.fire({
                title: 'Confirmar Exclusão',
                text: `Tem certeza que deseja excluir o cliente "${clienteNome}"? Esta ação não poderá ser revertida!`, 
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