@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Editar Funcionário') }}: <strong>{{ $funcionario->nome }}</strong></div>

                <div class="card-body">

                    <form method="POST" action="{{ route('funcionarios.update', $funcionario->id) }}" id="formEdicaoFuncionario">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="nome" class="col-md-4 col-form-label text-md-end">{{ __('Nome Completo') }}</label>
                            <div class="col-md-6">
                                <input id="nome" type="text" class="form-control @error('nome') is-invalid @enderror" name="nome" value="{{ old('nome', $funcionario->nome) }}" required autofocus>
                                @error('nome')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="cpf" class="col-md-4 col-form-label text-md-end">{{ __('CPF') }}</label>
                            <div class="col-md-6">
                                <input id="cpf" type="text" class="form-control @error('cpf') is-invalid @enderror" name="cpf" value="{{ old('cpf', $funcionario->cpf) }}" required>
                                @error('cpf')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <p class="text-muted">Deixe os campos de senha em branco se não desejar alterá-la.</p>

                        <div class="row mb-3">
                            <label for="senha" class="col-md-4 col-form-label text-md-end">{{ __('Nova Senha') }}</label>
                            <div class="col-md-6">
                                <input id="senha" type="password" class="form-control @error('senha') is-invalid @enderror" name="senha" autocomplete="new-password">
                                @error('senha')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="senha-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirmar Nova Senha') }}</label>
                            <div class="col-md-6">
                                <input id="senha-confirm" type="password" class="form-control" name="senha_confirmation" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-success me-2">
                                    {{ __('Atualizar Funcionário') }}
                                </button>
                                <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">
                                    {{ __('Cancelar') }}
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
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 (Swal) não está definido. Verifique a importação em app.js.');
        return;
    }

    const formEdicaoFuncionario = document.getElementById('formEdicaoFuncionario');
    if (formEdicaoFuncionario) {
        formEdicaoFuncionario.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Confirmar Atualização',
                text: "Você tem certeza que deseja atualizar os dados deste funcionário?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
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