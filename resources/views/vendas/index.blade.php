@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12"> {{-- Aumentei para col-md-12 para melhor acomodar a nova coluna --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Histórico de Vendas') }}</span>
                        <a href="{{ route('vendas.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Nova Venda
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($vendas->isEmpty())
                        <div class="alert alert-info" role="alert">
                            Nenhuma venda registrada ainda.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Data</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Funcionário</th>
                                        <th scope="col" class="text-end">Total (R$)</th>
                                        <th scope="col">Pagamento</th>
                                        <th scope="col" class="text-center">Ações</th>
                                        <th scope="col" class="text-center">Relatório</th> {{-- Nova Coluna --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vendas as $venda)
                                    <tr>
                                        <th scope="row">{{ $venda->id }}</th>
                                        <td>{{ $venda->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $venda->cliente->nome ?? 'N/A' }}</td>
                                        <td>{{ $venda->funcionario->nome ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($venda->total, 2, ',', '.') }}</td>
                                        <td>{{ ucfirst($venda->forma_pagamento) }}</td>
                                        <td class="text-center" style="min-width: 160px;"> {{-- Aumentei um pouco o min-width --}}
                                            <a href="{{ route('vendas.edit', $venda->id) }}" class="btn btn-sm btn-info me-1" title="Editar Venda">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-venda"
                                                    data-venda-id="{{ $venda->id }}"
                                                    data-venda-info="Venda #{{ $venda->id }} de {{ $venda->created_at->format('d/m/Y') }}"
                                                    title="Excluir Venda">
                                                <i class="bi bi-trash3"></i> Excluir
                                            </button>
                                            <form id="delete-form-venda-{{ $venda->id }}" action="{{ route('vendas.destroy', $venda->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                        <td class="text-center"> {{-- Novo Botão PDF --}}
                                            {{-- A rota 'vendas.gerarPdf' é um exemplo, precisaremos criá-la --}}
                                            <a href="{{ route('vendas.gerarPdf', $venda->id) }}" class="btn btn-sm btn-secondary" title="Gerar PDF da Venda" target="_blank">
                                                <i class="bi bi-file-earmark-pdf"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $vendas->links() }}
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
    const deleteVendaButtons = document.querySelectorAll('.btn-delete-venda');
    deleteVendaButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const vendaId = this.dataset.vendaId;
            const vendaInfo = this.dataset.vendaInfo;
            const form = document.getElementById(`delete-form-venda-${vendaId}`);

            if (typeof Swal === 'undefined') {
                if (confirm(`Tem certeza que deseja excluir a ${vendaInfo}? Esta ação não poderá ser revertida!`)) {
                    form.submit();
                }
                return;
            }

            if (!form) {
                Swal.fire('Erro Interno!', 'Formulário de exclusão não encontrado.', 'error');
                return;
            }

            Swal.fire({
                title: 'Confirmar Exclusão',
                text: `Tem certeza que deseja excluir a ${vendaInfo}? Itens e parcelas associados também serão removidos!`,
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