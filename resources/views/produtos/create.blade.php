@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-box-seam me-2"></i>Novo Produto
                        </h5>
                        <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('produtos.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nome" class="form-label fw-bold text-secondary">Nome do Produto</label>
                                <input id="nome" type="text" class="form-control @error('nome') is-invalid @enderror" 
                                       name="nome" value="{{ old('nome') }}" required  placeholder="Ex: Teclado Mecânico">
                                @error('nome')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="preco" class="form-label fw-bold text-secondary">Preço de Venda</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input id="preco" type="text" class="form-control @error('preco') is-invalid @enderror" 
                                           name="preco" value="{{ old('preco') }}" required placeholder="0,00">
                                </div>
                                @error('preco')
                                    <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="estoque" class="form-label fw-bold text-secondary">Estoque Inicial</label>
                                <input id="estoque" type="number" class="form-control @error('estoque') is-invalid @enderror" 
                                       name="estoque" value="{{ old('estoque', 0) }}" required min="0">
                                <small class="text-muted">Quantidade disponível para venda imediata.</small>
                                @error('estoque')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4 me-2">
                                    <i class="bi bi-check-lg me-1"></i> Cadastrar Produto
                                </button>
                                <button type="reset" class="btn btn-warning px-4 text-white">
                                    <i class="bi bi-eraser me-1"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputPreco = document.getElementById('preco');
        
        if(inputPreco) {
            inputPreco.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); 
                
                value = (Number(value) / 100).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                
                e.target.value = value;
            });
        }
    });
</script>
@endpush