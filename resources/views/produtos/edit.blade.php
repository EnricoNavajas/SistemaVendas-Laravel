@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Editar Produto') }}: <strong>{{ $produto->nome }}</strong></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('produtos.update', $produto->id) }}" id="formEdicaoProduto">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="nome" class="col-md-4 col-form-label text-md-end">{{ __('Nome do Produto') }}</label>
                            <div class="col-md-6">
                                <input id="nome" type="text" class="form-control @error('nome') is-invalid @enderror" name="nome" value="{{ old('nome', $produto->nome) }}" required autocomplete="nome" autofocus>
                                @error('nome')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="preco" class="col-md-4 col-form-label text-md-end">{{ __('Preço (R$)') }}</label>
                            <div class="col-md-6">
                                <input id="preco" type="number" step="0.01" min="0.01" class="form-control @error('preco') is-invalid @enderror" name="preco" value="{{ old('preco', $produto->preco) }}" required autocomplete="preco">
                                @error('preco')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-success me-2">
                                    {{ __('Atualizar Produto') }}
                                </button>
                                <a href="{{ route('produtos.index') }}" class="btn btn-secondary"> 
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


