@extends('layouts.app')

@section('content')
<style>
    .status-switch .form-check-input {
        background-color: #dc3545;
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        width: 3em; height: 1.5em; cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }
    .status-switch .form-check-input:checked {
        background-color: #198754; border-color: #198754;
    }
    .status-label {
        font-weight: bold; margin-left: 10px; vertical-align: middle; user-select: none;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-pencil-square me-2"></i>Editar Produto: <strong>{{ $produto->nome }}</strong>
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

                    <form id="formEdicaoProduto" method="POST" action="{{ route('produtos.update', $produto->id) }}">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="estoque" value="{{ $produto->estoque }}">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nome" class="form-label fw-bold text-secondary">Nome do Produto</label>
                                <input id="nome" type="text" class="form-control @error('nome') is-invalid @enderror" 
                                       name="nome" value="{{ old('nome', $produto->nome) }}" required autofocus>
                                @error('nome')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="preco" class="form-label fw-bold text-secondary">Preço (R$)</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input id="preco" type="text" class="form-control @error('preco') is-invalid @enderror" 
                                           name="preco" value="{{ old('preco', $produto->preco_formatado) }}" required>
                                </div>
                                @error('preco')
                                    <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Estoque Atual</label>
                                <div class="p-2 bg-light border rounded text-center">
                                    <span class="fw-bold fs-5 {{ $produto->estoque > 0 ? 'text-dark' : 'text-danger' }}">
                                        {{ $produto->estoque }} unidades
                                    </span>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-lock-fill"></i> Gerencie o estoque na tela de Estoque.  
                                </small>
                            </div>

                            <div class="col-md-12 mt-4 pt-3 border-top">
                                <label class="form-label fw-bold text-secondary d-block mb-3">Disponibilidade</label>
                                
                                <div class="form-check form-switch status-switch d-flex align-items-center ps-0">
                                    <input type="hidden" name="ativo" value="0">
                                    <input class="form-check-input ms-0" type="checkbox" 
                                           id="ativoSwitch" name="ativo" value="1" 
                                           {{ $produto->ativo ? 'checked' : '' }}>
                                           
                                    <label class="form-check-label status-label" for="ativoSwitch" id="statusText">
                                        {{ $produto->ativo ? 'Produto Ativo' : 'Produto Inativo' }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4 me-2">
                                    <i class="bi bi-check-circle me-1"></i> Atualizar Produto
                                </button>
                                <a href="{{ route('produtos.index') }}" class="btn btn-secondary px-4">
                                    Cancelar
                                </a>
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

        const switchBtn = document.getElementById('ativoSwitch');
        const statusText = document.getElementById('statusText');
        if(switchBtn && statusText){
             switchBtn.addEventListener('change', function() {
                if(this.checked) {
                    statusText.textContent = 'Produto Ativo';
                    statusText.classList.remove('text-danger');
                    statusText.classList.add('text-success');
                } else {
                    statusText.textContent = 'Produto Inativo';
                    statusText.classList.remove('text-success');
                    statusText.classList.add('text-danger');
                }
            });
            switchBtn.dispatchEvent(new Event('change'));
        }

        const formEdicao = document.getElementById('formEdicaoProduto');
        if (typeof Swal !== 'undefined' && formEdicao) {
            formEdicao.addEventListener('submit', function(event) {
                event.preventDefault(); 
                Swal.fire({
                    title: 'Confirmar Atualização',
                    text: "Confirma os novos dados deste produto?",
                    icon: 'question', 
                    showCancelButton: true,
                    confirmButtonColor: '#0B5ED7', 
                    cancelButtonColor: '#6c757d', 
                    confirmButtonText: 'Sim, atualizar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); 
                    }
                });
            });
        }
    });
</script>
@endpush