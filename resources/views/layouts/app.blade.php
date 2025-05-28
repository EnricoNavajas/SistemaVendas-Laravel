<!doctype html>
<html lang="pt_br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Sistema de Vendas</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            
            <div class="container">
                <a class="navbar-brand" href="{{ url('/home') }}">{{ config('app.name', 'Laravel') }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth 
                            <li class="nav-item dropdown">
                                <a id="cadastroDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Cadastros</a>
                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="cadastroDropdown">
                                    <a class="dropdown-item" href="{{ route('funcionarios.create') }}">Cadastrar Funcionário</a>
                                    <a class="dropdown-item" href="{{ route('clientes.create') }}">Cadastrar Cliente</a>
                                    <a class="dropdown-item" href="{{ route('produtos.create') }}">Cadastrar Produto</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="tabelasDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tabelas</a>
                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="tabelasDropdown">
                                    <a class="dropdown-item" href="{{ route('funcionarios.index') }}">Listar Funcionários</a>
                                    <a class="dropdown-item" href="{{ route('clientes.index') }}">Listar Clientes</a>
                                    <a class="dropdown-item" href="{{ route('produtos.index') }}">Listar Produtos</a>
                                    <a class="dropdown-item" href="{{ route('vendas.index') }}">Listar Vendas</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="vendasDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Vendas</a>
                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="vendasDropdown">
                                    <a class="dropdown-item" href="{{ route('vendas.create') }}">Nova Venda</a>
                                </div>
                            </li>
                        @endauth
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            @endif
                        @else 
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>{{ Auth::user()->nome }}</a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

   
    <script id="session-messages-json" type="application/json">
        {!!
            json_encode([
                'success' => session()->get('success'), 
                'error' => session()->get('error'),     
            ])
        !!}
    </script>

   
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messagesScriptTag = document.getElementById('session-messages-json');
            let messages = null;

            if (messagesScriptTag && messagesScriptTag.textContent) {
                try {
                    messages = JSON.parse(messagesScriptTag.textContent);
                } catch (e) {
                    console.error('Erro ao parsear JSON das mensagens de sessão:', e, messagesScriptTag.textContent);
                }
            }

            if (messages) {
                if (typeof Swal === 'undefined') {
                    console.warn('SweetAlert2 (Swal) não está definido globalmente. Usando alerts nativos para feedback.');
                    if (messages.success) alert('Sucesso: ' + messages.success);
                    if (messages.error) alert('Erro: ' + messages.error);
                } else {
                    if (messages.success) {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: messages.success, 
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok'
                        });
                    }
                    if (messages.error) {
                        Swal.fire({
                            title: 'Erro!',
                            text: messages.error, 
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Ok'
                        });
                    }
                }
            }
        });
    </script>

    @stack('scripts') 
</body>
</html>