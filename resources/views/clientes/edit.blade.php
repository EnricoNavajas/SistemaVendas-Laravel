@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Editar Cliente') }}: <strong>{{ $cliente->nome }}</strong></div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('clientes.update', $cliente->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="nome" class="col-md-4 col-form-label text-md-end">{{ __('Nome Completo') }}</label>
                            <div class="col-md-6">
                                <input id="nome" type="text" class="form-control @error('nome') is-invalid @enderror" name="nome" value="{{ old('nome', $cliente->nome) }}" required autofocus>
                                @error('nome')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="cpf" class="col-md-4 col-form-label text-md-end">{{ __('CPF') }}</label>
                            <div class="col-md-6">
                                <input id="cpf" type="text" class="form-control @error('cpf') is-invalid @enderror" name="cpf" value="{{ old('cpf', $cliente->cpf) }}" required>
                                @error('cpf')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="telefone" class="col-md-4 col-form-label text-md-end">{{ __('Telefone') }}</label>
                            <div class="col-md-6">
                                <input id="telefone" type="text" class="form-control @error('telefone') is-invalid @enderror" name="telefone" value="{{ old('telefone', $cliente->telefone) }}" required>
                                @error('telefone')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-success me-2">
                                    {{ __('Atualizar Cliente') }}
                                </button>
                                <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
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
        console.error('SweetAlert2 (Swal) não está definido. Verifique a instalação, importação no app.js e se o `npm run dev` está funcionando.');
        return; 
    }
    const formEdicaoProduto = document.getElementById('formEdicaoProduto');
    if (formEdicaoProduto) {
        formEdicaoProduto.addEventListener('submit', function(event) {
            event.preventDefault(); 

            Swal.fire({
                title: 'Confirmar Atualização',
                text: "Você tem certeza que deseja atualizar este produto?",
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