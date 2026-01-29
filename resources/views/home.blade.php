@extends('layouts.app')

@section('content')
<style>
    .card-dashboard {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #fff;
        overflow: hidden;
    }
    .card-dashboard:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .icon-box {
        width: 50px; height: 50px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .bg-gradient-primary { background: linear-gradient(45deg, #149ad1, #0d7aa6); color: white; }
    .bg-gradient-success { background: linear-gradient(45deg, #28a745, #218838); color: white; }
    .bg-gradient-warning { background: linear-gradient(45deg, #ffc107, #e0a800); color: white; }
    .bg-gradient-info    { background: linear-gradient(45deg, #17a2b8, #138496); color: white; }
</style>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Dashboard</h4>
            <span class="text-muted">Visão geral do sistema</span>
        </div>
        <div class="d-none d-md-block">
            <button class="btn btn-sm btn-white border shadow-sm">
                <i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::now()->format('d/m/Y') }}
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-dashboard h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-gradient-primary me-3">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold">Faturamento Hoje</small>
                        <h4 class="fw-bold mb-0 text-dark">R$ {{ number_format($fatHoje, 2, ',', '.') }}</h4>
                        <small class="text-primary fw-bold" style="font-size: 0.8em;">
                            {{ $vendasHoje }} vendas hoje
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-dashboard h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-gradient-success me-3">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold">Vendas este Mês</small>
                        <h4 class="fw-bold mb-0 text-dark">R$ {{ number_format($fatMes, 2, ',', '.') }}</h4>
                        <small class="text-success" style="font-size: 0.8em;">{{ $vendasMes }} vendas totais</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-dashboard h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-gradient-info me-3">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold">Ticket Médio</small>
                        <h4 class="fw-bold mb-0 text-dark">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-dashboard h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-gradient-warning me-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold">Clientes Ativos</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalClientes }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card card-dashboard h-100">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between">
                    <h6 class="fw-bold mb-0">Evolução de Vendas</h6>
                    <small class="text-muted">Últimos 6 meses</small>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" style="height: 300px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-dashboard h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h6 class="fw-bold mb-0">Últimas Vendas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recents as $venda)
                            <div class="list-group-item border-0 d-flex justify-content-between align-items-center py-3 px-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3 text-primary">
                                        <i class="bi bi-cart"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark">Venda #{{ $venda->id }}</span>
                                        <small class="text-muted" style="font-size: 0.8em;">
                                            {{ $venda->created_at->format('d/m - H:i') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="d-block fw-bold text-dark">R$ {{ number_format($venda->total, 2, ',', '.') }}</span>
                                    
                                    {{-- LÓGICA DE CORES --}}
                                    @if($venda->forma_pagamento == 'avista')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill" style="font-size: 0.7em;">
                                            À vista
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill" style="font-size: 0.7em;">
                                            Parcelado
                                        </span>
                                    @endif

                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 mb-2"></i><br>
                                Nenhuma venda recente
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <a href="{{ route('vendas.index') }}" class="text-decoration-none fw-bold" style="font-size: 0.9rem;">Ver Histórico Completo</a>
                </div>
            </div>
        </div>

    </div>
    
    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card card-dashboard">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h6 class="fw-bold mb-0">Produtos Mais Vendidos (Exemplo Visual)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th>Produto</th>
                                    <th>Preço</th>
                                    <th>Vendas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-bottom">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-2"><i class="bi bi-box"></i></div>
                                            <span class="fw-bold">Exemplo: Teclado Mecânico</span>
                                        </div>
                                    </td>
                                    <td>R$ 150,00</td>
                                    <td>42</td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success">Em Estoque</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-2"><i class="bi bi-mouse"></i></div>
                                            <span class="fw-bold">Exemplo: Mouse Gamer</span>
                                        </div>
                                    </td>
                                    <td>R$ 89,90</td>
                                    <td>28</td>
                                    <td><span class="badge bg-warning bg-opacity-10 text-warning">Poucas Unidades</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Dados vindos do PHP
        const labels = {!! json_encode($labelsGrafico) !!};
        const dataValues = {!! json_encode($dataGrafico) !!};

        const displayLabels = labels.length ? labels : ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'];
        const displayData = labels.length ? dataValues : [1200, 1900, 3000, 500, 2000, 3500];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: displayLabels,
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: displayData,
                    borderColor: '#149ad1',
                    backgroundColor: 'rgba(20, 154, 209, 0.05)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#149ad1',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f0f0f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush