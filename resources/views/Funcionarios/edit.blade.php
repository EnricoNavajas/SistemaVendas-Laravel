@extends('layouts.app')

@section('content')
<style>
    .status-switch .form-check-input {
        background-color: #dc3545; 
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        width: 3em; 
        height: 1.5em;
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }

    .status-switch .form-check-input:checked {
        background-color: #198754; 
        border-color: #198754;
    }
    
    .status-label {
        font-weight: bold;
        margin-left: 10px;
        vertical-align: middle;
        user-select: none;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-pencil-square me-2"></i>Editar Funcionário: <strong>{{ $funcionario->nome }}</strong>
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

                    <form id="formEdicaoFuncionario" method="POST" action="{{ route('funcionarios.update', $funcionario->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nome" class="form-label fw-bold text-secondary">Nome Completo</label>
                                <input id="nome" type="text" class="form-control @error('nome') is-invalid @enderror" 
                                       name="nome" value="{{ old('nome', $funcionario->nome) }}" required autofocus>
                                @error('nome')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="cpf" class="form-label fw-bold text-secondary">CPF</label>
                                <input id="cpf" type="text" class="form-control @error('cpf') is-invalid @enderror" 
                                       name="cpf" value="{{ old('cpf', $funcionario->cpf) }}" 
                                       required maxlength="14" placeholder="000.000.000-00">
                                @error('cpf')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="p-3 bg-light rounded border">
                                    <h6 class="text-muted fw-bold mb-3"><i class="bi bi-lock-fill me-1"></i> Alterar Senha (Opcional)</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="senha" class="form-label small text-secondary">Nova Senha</label>
                                            <input id="senha" type="password" class="form-control @error('senha') is-invalid @enderror" 
                                                   name="senha" autocomplete="new-password" placeholder="Deixe em branco para manter">
                                            @error('senha')
                                                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="senha-confirm" class="form-label small text-secondary">Confirmar Nova Senha</label>
                                            <input id="senha-confirm" type="password" class="form-control" 
                                                   name="senha_confirmation" autocomplete="new-password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-4 pt-3 border-top">
                                <label class="form-label fw-bold text-secondary d-block mb-3">Status do Acesso</label>
                                
                                <div class="form-check form-switch status-switch d-flex align-items-center ps-0">
                                    <input type="hidden" name="ativo" value="0">
                                    
                                    <input class="form-check-input ms-0" type="checkbox" 
                                           id="ativoSwitch" name="ativo" value="1" 
                                           {{ $funcionario->ativo ? 'checked' : '' }}>
                                           
                                    <label class="form-check-label status-label" for="ativoSwitch" id="statusText">
                                        {{ $funcionario->ativo ? 'Acesso Ativo' : 'Acesso Bloqueado' }}
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle"></i> Funcionários bloqueados não conseguem fazer login no sistema.
                                </small>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4 me-2">
                                    <i class="bi bi-check-circle me-1"></i> Atualizar Funcionário
                                </button>
                                <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary px-4">
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
        function mascaraCPF(value) {
            value = value.replace(/\D/g, ''); 
            if (value.length > 11) value = value.slice(0, 11);
            
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            return value;
        }
        const cpfInput = document.getElementById('cpf');
        const form = document.getElementById('formEdicaoFuncionario');

        if (cpfInput) {
            cpfInput.value = mascaraCPF(cpfInput.value);

            cpfInput.addEventListener('input', function(e) {
                e.target.value = mascaraCPF(e.target.value);
            });
        }

        const switchBtn = document.getElementById('ativoSwitch');
        const statusText = document.getElementById('statusText');

        if(switchBtn && statusText){
             switchBtn.addEventListener('change', function() {
                if(this.checked) {
                    statusText.textContent = 'Acesso Ativo';
                    statusText.classList.remove('text-danger');
                    statusText.classList.add('text-success');
                } else {
                    statusText.textContent = 'Acesso Bloqueado';
                    statusText.classList.remove('text-success');
                    statusText.classList.add('text-danger');
                }
            });
            switchBtn.dispatchEvent(new Event('change'));
        }

        if (typeof Swal !== 'undefined') {
            if (form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault(); 
                    
                    Swal.fire({
                        title: 'Confirmar Atualização',
                        text: "Você tem certeza que deseja atualizar os dados deste funcionário?",
                        icon: 'question', 
                        showCancelButton: true,
                        confirmButtonColor: '#0B5ED7', 
                        cancelButtonColor: '#6c757d', 
                        confirmButtonText: 'Sim, atualizar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                           
                            if(cpfInput) cpfInput.value = cpfInput.value.replace(/\D/g, '');
                            this.submit(); 
                        }
                    });
                });
            }
        }
    });
</script>
@endpush