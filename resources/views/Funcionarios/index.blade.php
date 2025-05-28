@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Lista de Funcionários') }}</span>
                        <a href="{{ route('funcionarios.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-person-plus-fill me-1"></i>Novo Funcionário
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($funcionarios->isEmpty())
                        <div class="alert alert-info" role="alert">
                            Nenhum funcionário cadastrado ainda.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nome</th>
                                        <th scope="col">CPF</th>
                                        <th scope="col">Cadastrado em</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($funcionarios as $funcionario)
                                    <tr>
                                        <th scope="row">{{ $funcionario->id }}</th>
                                        <td>{{ $funcionario->nome }}</td>
                                        <td>{{ $funcionario->cpf }}</td>
                                        <td>{{ $funcionario->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('funcionarios.edit', $funcionario->id) }}" class="btn btn-sm btn-info me-1" title="Editar">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-funcionario"
                                                    data-funcionario-id="{{ $funcionario->id }}"
                                                    data-funcionario-nome="{{ $funcionario->nome }}"
                                                    title="Excluir">
                                                <i class="bi bi-trash3"></i> Excluir
                                            </button>
                                            <form id="delete-form-funcionario-{{ $funcionario->id }}" action="{{ route('funcionarios.destroy', $funcionario->id) }}" method="POST" style="display: none;">
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
                            {{ $funcionarios->links() }}
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
        console.error('SweetAlert2 (Swal) não está definido. Verifique a importação em app.js.');
        return;
    }

    const deleteFuncionarioButtons = document.querySelectorAll('.btn-delete-funcionario');
    deleteFuncionarioButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const funcionarioId = this.dataset.funcionarioId;
            const funcionarioNome = this.dataset.funcionarioNome;
            const form = document.getElementById(`delete-form-funcionario-${funcionarioId}`);

            if (!form) {
                Swal.fire('Erro Interno!', 'Formulário de exclusão não encontrado.', 'error');
                return;
            }

            Swal.fire({
                title: 'Confirmar Exclusão',
                text: `Tem certeza que deseja excluir o funcionário "${funcionarioNome}"? Esta ação não poderá ser revertida!`,
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