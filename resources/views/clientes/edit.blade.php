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
                            <i class="bi bi-pencil-square me-2"></i>Editar Cliente: <strong>{{ $cliente->nome }}</strong>
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

                    <form id="formEdicaoCliente" method="POST" action="{{ route('clientes.update', $cliente->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nome" class="form-label fw-bold text-secondary">Nome Completo</label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" name="nome" value="{{ old('nome', $cliente->nome) }}" required autofocus>
                                @error('nome')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="cpf" class="form-label fw-bold text-secondary">CPF</label>
                                <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
                                       id="cpf" name="cpf" value="{{ old('cpf', $cliente->cpf) }}" 
                                       required maxlength="14" placeholder="000.000.000-00">
                                @error('cpf')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="telefone" class="form-label fw-bold text-secondary">Telefone</label>
                                <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                                       id="telefone" name="telefone" value="{{ old('telefone', $cliente->telefone) }}" 
                                       required maxlength="15" placeholder="(00) 00000-0000">
                                @error('telefone')
                                    <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-4 pt-3 border-top">
                                <label class="form-label fw-bold text-secondary d-block mb-3">Status do Cadastro</label>
                                
                                <div class="form-check form-switch status-switch d-flex align-items-center ps-0">
                                    <input type="hidden" name="ativo" value="0">
                                    
                                    <input class="form-check-input ms-0" type="checkbox" 
                                           id="ativoSwitch" name="ativo" value="1" 
                                           {{ $cliente->ativo ? 'checked' : '' }}>
                                           
                                    <label class="form-check-label status-label" for="ativoSwitch" id="statusText">
                                        {{ $cliente->ativo ? 'Cliente Ativo' : 'Cliente Inativo' }}
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle"></i> Clientes inativos não aparecem nas opções de nova venda.
                                </small>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4 me-2">
                                    <i class="bi bi-check-circle me-1"></i> Atualizar Cliente
                                </button>
                                <a href="{{ route('clientes.index') }}" class="btn btn-secondary px-4">
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

        function mascaraTelefone(value) {
            value = value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d)(\d{4})$/, '$1-$2');
            return value;
        }


        const cpfInput = document.getElementById('cpf');
        const telInput = document.getElementById('telefone');

       
        if (cpfInput) cpfInput.value = mascaraCPF(cpfInput.value);
        if (telInput) telInput.value = mascaraTelefone(telInput.value);

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

      
        const switchBtn = document.getElementById('ativoSwitch');
        const statusText = document.getElementById('statusText');

        if(switchBtn && statusText){
             switchBtn.addEventListener('change', function() {
                if(this.checked) {
                    statusText.textContent = 'Cliente Ativo';
                    statusText.classList.remove('text-danger');
                    statusText.classList.add('text-success');
                } else {
                    statusText.textContent = 'Cliente Inativo';
                    statusText.classList.remove('text-success');
                    statusText.classList.add('text-danger');
                }
            });
            switchBtn.dispatchEvent(new Event('change'));
        }

        if (typeof Swal !== 'undefined') {
            const formEdicaoCliente = document.getElementById('formEdicaoCliente');
            if (formEdicaoCliente) {
                formEdicaoCliente.addEventListener('submit', function(event) {
                    event.preventDefault(); 
                    Swal.fire({
                        title: 'Confirmar Atualização',
                        text: "Você tem certeza que deseja atualizar este cliente?",
                        icon: 'question', 
                        showCancelButton: true,
                        confirmButtonColor: '#0B5ED7', 
                        cancelButtonColor: '#6c757d', 
                        confirmButtonText: 'Sim, atualizar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cpfInput.value = cpfInput.value.replace(/\D/g, '');
                            telInput.value = telInput.value.replace(/\D/g, '');
                            this.submit(); 
                        }
                    });
                });
            }
        }
    });
</script>
@endpush