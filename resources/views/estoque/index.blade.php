@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0 fw-bold text-primary">
                                <i class="bi bi-boxes me-2"></i>Gerenciamento Rápido de Estoque
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <form action="{{ route('estoque.index') }}" method="GET">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 bg-light" 
                                           placeholder="Buscar produto..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-primary" type="submit">Filtrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Produto</th>
                                    <th>Preço Venda</th>
                                    <th style="width: 200px;">Quantidade em Estoque</th>
                                    <th>Status Atual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($produtos as $produto)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $produto->nome }}</div>
                                        <small class="text-muted">ID: #{{ $produto->id }}</small>
                                    </td>
                                    
                                    <td class="text-secondary">
                                        R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                    </td>

                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                   class="form-control fw-bold text-center estoque-input"
                                                   data-id="{{ $produto->id }}"
                                                   data-original-value="{{ $produto->estoque }}"
                                                   value="{{ $produto->estoque }}"
                                                   min="0"
                                                   onkeypress="return event.charCode >= 48"> <span class="input-group-text bg-white">unid.</span>
                                        </div>
                                        <div id="feedback-{{ $produto->id }}" class="small mt-1" style="height: 20px;"></div>
                                    </td>
                                    
                                    <td id="status-cell-{{ $produto->id }}">
                                        @if($produto->estoque == 0)
                                            <span class="badge bg-danger rounded-pill">Esgotado</span>
                                        @elseif($produto->estoque < 5)
                                            <span class="badge bg-warning text-dark rounded-pill">Baixo</span>
                                        @else
                                            <span class="badge bg-success rounded-pill">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        Nenhum produto ativo encontrado.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center p-3 border-top">
                        {{ $produtos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
    });

    const inputs = document.querySelectorAll('.estoque-input');

    inputs.forEach(input => {
        
        input.addEventListener('focus', function() {
            if (this.value === '0') {
                this.value = '';
            }
        });

        input.addEventListener('input', function() {
            let val = this.value.replace(/[^0-9]/g, '');
            
            if (val.length > 1 && val.startsWith('0')) {
                val = parseInt(val, 10);
            }
            
            this.value = val;
        });

        input.addEventListener('keydown', function(e) {
            // Bloqueia sinal de menos (-), ponto (.), vírgula (,) e 'e' (exponencial)
            if (['-', '+', 'e', '.', ','].includes(e.key)) {
                e.preventDefault();
            }
        });

        input.addEventListener('blur', function() {
            // Se deixou vazio, assume 0
            if (this.value === '') {
                this.value = '0';
            }
            salvarEstoque(this);
        });

        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.blur(); 
            }
        });
    });

    function salvarEstoque(input) {
        const id = input.dataset.id;
        const novaQtd = input.value;
        const valorOriginal = input.dataset.originalValue;

        if (novaQtd === valorOriginal) return;

        // Feedback
        input.disabled = true;
        input.classList.add('bg-light'); 
        const feedbackDiv = document.getElementById(`feedback-${id}`);
        feedbackDiv.innerHTML = '<span class="text-primary"><i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i> Salvando...</span>';

        fetch(`/estoque/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ nova_quantidade: novaQtd })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.dataset.originalValue = novaQtd; 
                input.classList.remove('is-invalid');
                input.classList.add('is-valid'); 
                setTimeout(() => input.classList.remove('is-valid'), 2000);

                feedbackDiv.innerHTML = '<span class="text-success small fw-bold">Salvo!</span>';
                setTimeout(() => feedbackDiv.innerHTML = '', 2000);

                const statusCell = document.getElementById(`status-cell-${id}`);
                const qtd = parseInt(novaQtd);
                let novoBadge = '';

                if (qtd === 0) {
                    novoBadge = '<span class="badge bg-danger rounded-pill">Esgotado</span>';
                } else if (qtd < 5) {
                    novoBadge = '<span class="badge bg-warning text-dark rounded-pill">Baixo</span>';
                } else {
                    novoBadge = '<span class="badge bg-success rounded-pill">Normal</span>';
                }
                statusCell.innerHTML = novoBadge;
                Toast.fire({ icon: 'success', title: 'Estoque atualizado!' });
            } else {
                throw new Error('Erro na resposta');
            }
        })
        .catch(error => {
            console.error(error);
            input.value = valorOriginal; 
            input.classList.add('is-invalid'); 
            feedbackDiv.innerHTML = '<span class="text-danger small">Erro ao salvar!</span>';
            Toast.fire({ icon: 'error', title: 'Erro ao atualizar estoque.' });
        })
        .finally(() => {
            input.disabled = false;
            input.classList.remove('bg-light');
        });
    }
});
</script>
@endpush