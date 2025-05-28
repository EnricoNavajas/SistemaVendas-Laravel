@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Painel Principal') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h5 class="card-title">Bem-vindo(a) ao Sistema de Vendas!</h5>
                    <p class="card-text">Você está logado e pronto para começar.</p>
                    <p class="card-text">Utilize o menu de navegação para acessar as funcionalidades do sistema, como registrar novas vendas ou visualizar o histórico.</p>
                    <hr>
                    <a href="{{ route('vendas.index') }}" class="btn btn-primary">
                        <i class="bi bi-list-check me-1"></i> Ver Histórico de Vendas
                    </a>
                    <a href="{{ route('vendas.create') }}" class="btn btn-success ms-2">
                        <i class="bi bi-plus-circle me-1"></i> Registrar Nova Venda
                    </a>    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection