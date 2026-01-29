@extends('layouts.app')

@section('content')
<style>
    .chaos-background {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100vh;
        z-index: -1; overflow: hidden;
    }
    .shard {
        position: absolute; background: #149ad1; opacity: 0;
    }
    .shard-1 {
        top: -50%; left: -50%; width: 100%; height: 150%;
        background: linear-gradient(45deg, #149ad1, #0d7aa6); transform: skewX(-20deg);
        animation: slamInLeft 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }
    .shard-2 {
        bottom: -50%; right: -50%; width: 100%; height: 150%;
        background: linear-gradient(45deg, #34a1ce, #149ad1); transform: skewX(-20deg);
        animation: slamInRight 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        animation-delay: 0.2s;
    }
    .shard-3 {
        top: 20%; left: -100%; width: 100%; height: 100px;
        background: rgba(255, 255, 255, 0.1);
        animation: slideHardLeft 1s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        animation-delay: 0.4s;
    }
    .shard-4 {
        top: -100%; right: 20%; width: 150px; height: 100%;
        background: rgba(0, 0, 0, 0.05);
        animation: slideHardTop 1s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        animation-delay: 0.5s;
    }
    @keyframes slamInLeft { to { left: -20%; opacity: 1; } }
    @keyframes slamInRight { to { right: -20%; opacity: 1; } }
    @keyframes slideHardLeft { to { left: 0; opacity: 1; } }
    @keyframes slideHardTop { to { top: 0; opacity: 1; } }

    .login-container-center {
        min-height: 80vh; display: flex; align-items: center; justify-content: center;
    }

    .card-entrance-wrapper {
        opacity: 0; transform: scale(0.5);
        animation: cardBoom 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        animation-delay: 1s;
    }

    @keyframes cardBoom { to { opacity: 1; transform: scale(1); } }

    .login-card {
        border: none; border-radius: 20px; background: #fff; overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-card:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
    }

</style>

@if($errors->any())
<style>
    .shard-1 { left: -20%; opacity: 1; animation: none; }
    .shard-2 { right: -20%; opacity: 1; animation: none; }
    .shard-3 { left: 0; opacity: 1; animation: none; }
    .shard-4 { top: 0; opacity: 1; animation: none; }
    
    .card-entrance-wrapper {
        opacity: 1; 
        transform: scale(1);
        animation: none;
    }
</style>
@endif

<div class="chaos-background">
    <div class="shard shard-1"></div>
    <div class="shard shard-2"></div>
    <div class="shard shard-3"></div>
    <div class="shard shard-4"></div>
</div>

<div class="container login-container-center">
    <div class="col-md-10 col-lg-8">
        
        <div class="card-entrance-wrapper">
            <div class="card login-card">
                <div class="row g-0">
                    
                    <div class="col-md-6 p-5 bg-white">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary">Bem-vindo ao Seu Sistema de Vendas!</h3>
                            <p class="text-muted">Acesse sua conta</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="cpf" class="form-label fw-bold text-secondary">CPF</label>
                                <input id="cpf" type="text" 
                                       class="form-control form-control-lg @error('cpf') is-invalid @enderror" 
                                       name="cpf" value="{{ old('cpf') }}" 
                                       required autocomplete="cpf" placeholder="000.000.000-00">
                                @error('cpf')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold text-secondary">Senha</label>
                                <input id="password" type="password" 
                                       class="form-control form-control-lg @error('senha') is-invalid @enderror" 
                                       name="senha" required placeholder="********">
                                @error('senha')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                    ENTRAR
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center bg-light">
                        <div class="p-4 text-center">
                            <img src="{{ asset('imgs/sualogo-removebg.png') }}" 
                                 alt="Logo" 
                                 class="img-fluid" 
                                 style="max-height: 250px;">
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cpfInput = document.getElementById('cpf');
        if(cpfInput) {
            cpfInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); 
                if (value.length > 11) value = value.slice(0, 11);
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                e.target.value = value;
            });
        }
    });
</script>
@endpush