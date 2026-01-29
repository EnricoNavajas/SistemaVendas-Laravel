@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-person-plus-fill me-2"></i>Cadastrar Novo Cliente
                        </h5>
                        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary btn-sm">
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

                    <form id="formCadastroCliente" method="POST" action="{{ route('clientes.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nome" class="form-label fw-bold text-secondary">Nome Completo</label>
                                <input id="nome" type="text" class="form-control @error('nome') is-invalid @enderror" 
                                       name="nome" value="{{ old('nome') }}" required autofocus placeholder="Ex: João da Silva">
                                @error('nome')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="cpf" class="form-label fw-bold text-secondary">CPF</label>
                                <input id="cpf" type="text" class="form-control @error('cpf') is-invalid @enderror" 
                                       name="cpf" value="{{ old('cpf') }}" required maxlength="14" placeholder="000.000.000-00">
                                @error('cpf')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="telefone" class="form-label fw-bold text-secondary">Telefone</label>
                                <input id="telefone" type="text" class="form-control @error('telefone') is-invalid @enderror" 
                                       name="telefone" value="{{ old('telefone') }}" required maxlength="15" placeholder="(00) 00000-0000">
                                @error('telefone')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4 me-2">
                                    <i class="bi bi-check-lg me-1"></i> Cadastrar Cliente
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
        
        // --- FUNÇÕES DE MÁSCARA (VISUAL) ---
        function mascaraCPF(value) {
            value = value.replace(/\D/g, ''); 
            if (value.length > 11) value = value.slice(0, 11);
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            return value;
        }

        function mascaraTelefone(value) {
            value = value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d)(\d{4})$/, '$1-$2');
            return value;
        }

        // --- APLICAÇÃO DA MÁSCARA ENQUANTO DIGITA ---
        const cpfInput = document.getElementById('cpf');
        const telInput = document.getElementById('telefone');
        const form = document.getElementById('formCadastroCliente');

        if (cpfInput) {
            cpfInput.addEventListener('input', function(e) {
                e.target.value = mascaraCPF(e.target.value);
            });
        }

        if (telInput) {
            telInput.addEventListener('input', function(e) {
                e.target.value = mascaraTelefone(e.target.value);
            });
        }

        if (form) {
            form.addEventListener('submit', function(event) {
                if (cpfInput) cpfInput.value = cpfInput.value.replace(/\D/g, '');
                if (telInput) telInput.value = telInput.value.replace(/\D/g, '');
            });
        }
    });
</script>
@endpush