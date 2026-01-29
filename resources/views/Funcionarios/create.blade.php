@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-person-badge-fill me-2"></i>Cadastrar Novo Funcionário
                        </h5>
                        <a href="{{ route('funcionarios.index') }}" class="btn btn-outline-secondary btn-sm">
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

                    <form id="formCadastroFuncionario" method="POST" action="{{ route('funcionarios.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nome" class="form-label fw-bold text-secondary">Nome Completo</label>
                                <input id="nome" type="text" class="form-control @error('nome') is-invalid @enderror" 
                                       name="nome" value="{{ old('nome') }}" required autofocus placeholder="Ex: Maria Souza">
                                @error('nome')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="cpf" class="form-label fw-bold text-secondary">CPF</label>
                                <input id="cpf" type="text" class="form-control @error('cpf') is-invalid @enderror" 
                                       name="cpf" value="{{ old('cpf') }}" required maxlength="14" placeholder="000.000.000-00">
                                @error('cpf')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="senha" class="form-label fw-bold text-secondary">Senha</label>
                                <input id="senha" type="password" class="form-control @error('senha') is-invalid @enderror" 
                                       name="senha" required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
                                @error('senha')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="senha-confirm" class="form-label fw-bold text-secondary">Confirmar Senha</label>
                                <input id="senha-confirm" type="password" class="form-control" 
                                       name="senha_confirmation" required autocomplete="new-password" placeholder="Repita a senha">
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4 me-2">
                                    <i class="bi bi-check-lg me-1"></i> Cadastrar Funcionário
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
        function mascaraCPF(value) {
            value = value.replace(/\D/g, ''); 
            if (value.length > 11) value = value.slice(0, 11); 
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            return value;
        }
        const cpfInput = document.getElementById('cpf');
        const form = document.getElementById('formCadastroFuncionario');

        if (cpfInput) {
            cpfInput.addEventListener('input', function(e) {
                e.target.value = mascaraCPF(e.target.value);
            });
        }
        if (form) {
            form.addEventListener('submit', function(event) {
                if (cpfInput) cpfInput.value = cpfInput.value.replace(/\D/g, '');
            });
        }
    });
</script>
@endpush