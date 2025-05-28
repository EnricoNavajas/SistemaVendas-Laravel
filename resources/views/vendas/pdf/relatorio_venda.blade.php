<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório da Venda #{{ $venda->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        h1, h2, h3, h4 {
            margin-top: 0;
            margin-bottom: 0.5em;
            color: #222;
        }
        h1 {
            font-size: 24px;
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 18px;
            margin-top: 20px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-geral {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .info-bloco {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #fdfdfd;
            border: 1px solid #eaeaea;
            border-radius: 4px;
        }
        .info-bloco p {
            margin: 5px 0;
        }
        .info-bloco strong {
            display: inline-block;
            min-width: 120px;
        }
        .logo-empresa {
            max-width: 150px;
            max-height: 70px;
            margin-bottom: 10px;
        }
        .header-info {
            
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse; 
        }
        .header-info td {
            border: none; 
            padding: 0;
            vertical-align: top;
        }
        .empresa-details {
            width: 60%;
        }
        .relatorio-details {
            width: 40%;
            text-align: right;
        }
        .parcela-vencida {
            color: red;
            font-weight: bold;
        }
        .parcela-apagar {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-info">
            <tr>
                <td class="empresa-details">
                    @if(isset($logoPath) && $logoPath)
                        <img src="{{ $logoPath }}" alt="Logo da Empresa" class="logo-empresa">
                    @else
                        <h3>{{ $nomeEmpresa ?? 'Nome da Sua Empresa' }}</h3>
                    @endif
                    <p><strong>{{ $nomeEmpresa ?? 'Nome da Sua Empresa' }}</strong></p>
                    <p>{{ $enderecoEmpresa ?? 'Seu Endereço Completo, Cidade - UF' }}</p>
                    <p>{{ $telefoneEmpresa ?? 'Seu Telefone' }} | {{ $emailEmpresa ?? 'seu@email.com' }}</p>
                </td>
                <td class="relatorio-details">
                    <h3 style="margin-bottom: 0;">Relatório de Venda</h3>
                    <p style="margin-top: 0;"><strong>Venda ID:</strong> #{{ $venda->id }}</p>
                    <p><strong>Gerado em:</strong> {{ $dataGeracao ?? now()->format('d/m/Y H:i:s') }}</p>
                </td>
            </tr>
        </table>

        <h1>Detalhes da Venda #{{ $venda->id }}</h1>

        <div class="info-bloco">
            <p><strong>Data da Venda:</strong> {{ $venda->created_at->format('d/m/Y H:i:s') }}</p>
            <p><strong>Cliente:</strong> {{ $venda->cliente->nome ?? 'Não informado' }}</p>
            @if($venda->cliente && ($venda->cliente->cpf || $venda->cliente->cnpj) )
                <p><strong>CPF/CNPJ Cliente:</strong> {{ $venda->cliente->cpf ?? $venda->cliente->cnpj }}</p>
            @endif
            <p><strong>Funcionário Responsável:</strong> {{ $venda->funcionario->name ?? ($venda->funcionario->nome ?? 'Não informado') }}</p>
        </div>

        <h2>Itens da Venda</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produto</th>
                    <th class="text-center">Qtd.</th>
                    <th class="text-right">Preço Unit. (R$)</th>
                    <th class="text-right">Subtotal (R$)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($venda->itens as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->produto->nome ?? 'Produto não encontrado' }}</td>
                        <td class="text-center">{{ $item->quantidade }}</td>
                        <td class="text-right">{{ number_format($item->preco_unit, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Nenhum item encontrado para esta venda.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right total-geral">TOTAL DOS ITENS:</td>
                    <td class="text-right total-geral">{{ number_format($venda->total, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <h2>Detalhes do Pagamento</h2>
        <div class="info-bloco">
            <p><strong>Forma de Pagamento:</strong> {{ ucfirst($venda->forma_pagamento) }}</p>
            @if ($venda->forma_pagamento == 'parcelado' && $venda->numero_parcelas)
                <p><strong>Número de Parcelas:</strong> {{ $venda->numero_parcelas }}x</p>
            @endif
            <p class="total-geral" style="margin-top: 10px;"><strong>VALOR TOTAL DA VENDA: R$ {{ number_format($venda->total, 2, ',', '.') }}</strong></p>
        </div>

        @if ($venda->forma_pagamento == 'parcelado' && $venda->parcelas->isNotEmpty())
            <h2>Parcelamento</h2>
            <table>
                <thead>
                    <tr>
                        <th>Parcela</th>
                        <th>Data de Vencimento</th>
                        <th class="text-right">Valor (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($venda->parcelas as $parcela)
                        @php
                            $vencimento = \Carbon\Carbon::parse($parcela->vencimento);
                            $statusClasse = '';
                        @endphp
                        <tr class="{{ $statusClasse }}">
                            <td>{{ $parcela->numero }}ª</td>
                            <td>{{ $vencimento->format('d/m/Y') }}</td>
                            <td class="text-right">{{ number_format($parcela->valor, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="footer">
            <p>{{ $nomeEmpresa ?? 'Nome da Sua Empresa' }} - {{ $cnpjEmpresa ?? 'Seu CNPJ' }}</p>
            <p>Relatório gerado pelo sistema em {{ $dataGeracao ?? now()->format('d/m/Y H:i:s') }}.</p>
        </div>
    </div>
</body>
</html>
