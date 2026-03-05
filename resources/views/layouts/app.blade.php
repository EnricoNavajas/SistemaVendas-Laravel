<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistema de Vendas') }}</title>
    
    <link rel="icon" href="{{ asset('imgs/sualogo-removebg.png') }}" type="image/png">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        
        body { background-color: #f8f9fa; overflow-x: hidden; }
        #wrapper { display: flex; width: 100%; align-items: stretch; }

       
        #sidebar {
            min-width: 260px; max-width: 260px;
            background-color: #149ad1; color: #fff;
            transition: all 0.3s; min-height: 100vh;
        }
        #sidebar.active { margin-left: -260px; }
        
        .sidebar-header { padding: 20px; background: rgba(0,0,0,0.05); text-align: center; }
        
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a {
            padding: 15px 25px; font-size: 1.1em; display: block;
            color: white; text-decoration: none; transition: 0.3s;
            border-left: 5px solid transparent;
        }
        #sidebar ul li a:hover { background: rgba(255,255,255,0.2); border-left: 5px solid #fff; }
        #sidebar ul li a i { margin-right: 10px; }
        #sidebar ul li ul.collapse li a { padding-left: 60px; background: rgba(0,0,0,0.1); font-size: 0.9em; }

       
        #content { width: 100%; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar {
            background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 10px 20px; display: flex; justify-content: space-between; align-items: center;
        }

       
        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; }
            #sidebar.active { margin-left: 0; }
        }

        
        #checkbox { display: none; }

        .toggle {
            position: relative;
            width: 30px;      
            height: 30px;     
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;          
            transition-duration: .3s;
        }

        .bars {
            width: 100%;
            height: 3px;     
            background-color: #149ad1; 
            border-radius: 4px;
            transition-duration: .3s;
        }

        #checkbox:checked + .toggle #bar2 {
            transform: translateY(8px) rotate(60deg);
            margin-left: 0;
            transform-origin: right;
            transition-duration: .3s;
            z-index: 2;
        }

        #checkbox:checked + .toggle #bar1 {
            transform: translateY(16px) rotate(-60deg); 
            transition-duration: .3s;
            transform-origin: left;
            z-index: 1;
        }

        #checkbox:checked + .toggle {
            transform: rotate(-90deg);
        }
    </style>
</head>
<body>

    <div id="wrapper">

        @auth
        <nav id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('imgs/sualogo-removebg.png') }}" alt="Logo" class="img-fluid" style="max-height: 80px;">
            </div>

            <ul class="list-unstyled components">
                <li><a href="{{ url('/home') }}"><i class="bi bi-speedometer2"></i> DashBoard</a></li>
                
                <li>
                    <a href="#cadastrosSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="bi bi-folder-plus"></i> Cadastros
                    </a>
                    <ul class="collapse list-unstyled" id="cadastrosSubmenu">
                        <li><a href="{{ route('funcionarios.create') }}">Funcionário</a></li>
                        <li><a href="{{ route('clientes.create') }}">Cliente</a></li>
                        <li><a href="{{ route('produtos.create') }}">Produto</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('estoque.index') }}">
                        <i class="bi bi-boxes"></i> Estoque
                    </a>
                </li>

                <li>
                    <a href="#tabelasSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="bi bi-table"></i> Tabelas
                    </a>
                    <ul class="collapse list-unstyled" id="tabelasSubmenu">
                        <li><a href="{{ route('funcionarios.index') }}">Funcionários</a></li>
                        <li><a href="{{ route('clientes.index') }}">Clientes</a></li>
                        <li><a href="{{ route('produtos.index') }}">Produtos</a></li>
                        <li><a href="{{ route('vendas.index') }}">Vendas</a></li>
                    </ul>
                </li>

                <li>
                    <a href="#vendasSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="bi bi-cart-check-fill"></i> Vendas
                    </a>
                    <ul class="collapse list-unstyled" id="vendasSubmenu">
                        <li><a href="{{ route('vendas.create') }}">Nova Venda</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        @endauth

        <div id="content">

            @auth
            <nav class="top-navbar">
                <div class="d-flex align-items-center gap-3">
                    
                    <input id="checkbox" type="checkbox" checked>
                    
                    <label class="toggle" for="checkbox" id="sidebarCollapse">
                        <div id="bar1" class="bars"></div>
                        <div id="bar2" class="bars"></div>
                        <div id="bar3" class="bars"></div>
                    </label>

                    <img src="{{ asset('imgs/sualogo-removebg.png') }}" alt="Logo Small" style="height: 40px;">
                </div>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 border" style="width: 40px; height: 40px;">
                            <i class="bi bi-person-fill text-primary"></i>
                        </div>
                        <span class="fw-bold">{{ Auth::user()->nome }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a>
                        </li>
                    </ul>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </nav>
            @endauth

            <main class="p-4 w-100">
                @yield('content')
            </main>

        </div>
    </div>

    <script id="session-messages-json" type="application/json">
        {!! json_encode(['success' => session()->get('success'), 'error' => session()->get('error')]) !!}
    </script>

    <script>
        $(document).ready(function () {
            // Lógica do Sidebar
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });

            // SweetAlert
            const messagesScriptTag = document.getElementById('session-messages-json');
            if (messagesScriptTag && messagesScriptTag.textContent) {
                try {
                    const messages = JSON.parse(messagesScriptTag.textContent);
                    if (typeof Swal !== 'undefined') {
                        if (messages.success) Swal.fire({ title: 'Sucesso!', text: messages.success, icon: 'success', confirmButtonColor: '#3085d6' });
                        if (messages.error) Swal.fire({ title: 'Erro!', text: messages.error, icon: 'error', confirmButtonColor: '#d33' });
                    }
                } catch (e) { console.error(e); }
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>