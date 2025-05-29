@extends('layouts.app')

@push('styles')
<style>
   

</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-10"> 
            <div class="card">
                
                <div class="card-header">{{ __('Nova Venda') }}</div> 
                <div class="card-body">
                   
                    <form method="POST" action="{{ route('vendas.store') }}" id="formNovaVenda">
                        @csrf
                        <div class="row gx-3"> 
                            <div class="col-md-7 col-lg-8">
                                <div class="mb-3 row">
                                    <label for="busca_cliente_nome" class="col-lg-2 col-form-label text-lg-end">{{ __('Cliente') }}</label>
                                    <div class="col-lg-10 search-wrapper"> 
                                        <input type="text" class="form-control @error('cliente_id') is-invalid @enderror" id="busca_cliente_nome" placeholder="Digite nome ou CPF (Opcional)" autocomplete="off">
                                        <input type="hidden" id="cliente_id" name="cliente_id" value="{{ old('cliente_id') }}">
                                        <div id="display_cliente_selecionado" class="mt-1 small text-muted"></div>
                                        
                                        <div id="lista_resultados_clientes" class="list-group autocomplete-results position-absolute" style="display:none;">
                                           
                                        </div>
                                        @error('cliente_id')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <hr>
                                <div class="mb-3 row">
                                    <label for="busca_produto" class="col-lg-2 col-form-label text-lg-end">{{ __('Produto') }}</label>
                                    <div class="col-lg-10 search-wrapper">
                                        <input type="text" class="form-control" id="busca_produto" placeholder="Digite para buscar produto" autocomplete="off">
                                        <div id="lista_resultados_produtos" class="list-group autocomplete-results position-absolute" style="display:none;">

                                        </div>
                                    </div>
                                </div>
                                <h5 class="mt-4">Itens da Venda</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="tabela_itens_venda">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th style="width: 80px;">Qtd.</th>
                                                <th style="width: 130px;" class="text-end">Preço Unit.</th>
                                                <th style="width: 130px;" class="text-end">Subtotal</th>
                                                <th style="width: 60px;" class="text-center">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-center text-muted" id="placeholder_itens_venda">Nenhum produto adicionado à venda.</p>
                            </div>

                            <div class="col-md-5 col-lg-4 border-start ps-md-4">
                               
                                @php
                                    $_formaPagamentoChecked = old('forma_pagamento', 'avista'); 
                                    $_numeroParcelasSelected = (int)old('numero_parcelas_select', 1); 
                                @endphp

                                <h4>Total da Venda</h4>
                                <h2 id="display_total_venda" class="text-success fw-bold">R$ 0,00</h2>
                                <hr>
                                <h5>Forma de Pagamento</h5>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="forma_pagamento" id="pag_avista" value="avista" @if($_formaPagamentoChecked == 'avista') checked @endif>
                                        <label class="form-check-label" for="pag_avista">À vista</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="forma_pagamento" id="pag_parcelado" value="parcelado" @if($_formaPagamentoChecked == 'parcelado') checked @endif>
                                        <label class="form-check-label" for="pag_parcelado">Parcelado</label>
                                    </div>
                                </div>
                                
                                <div id="detalhes_parcelamento" class="mt-3" @if($_formaPagamentoChecked != 'parcelado') style="display: none;" @endif>
                                    <h5>Parcelamento</h5>
                                    <div class="mb-2">
                                        <label for="numero_parcelas" class="form-label">Número de Parcelas (1-6):</label>
                                        <select class="form-select form-select-sm" id="numero_parcelas" name="numero_parcelas_select">
                                            @for ($i = 1; $i <= 6; $i++)
                                                <option value="{{ $i }}" @if($_numeroParcelasSelected == $i) selected @endif>{{ $i }}x</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div id="info_valor_parcelas" class="mb-3 small text-muted"></div>
                                    <div id="campos_parcelas_editaveis">
                                        @if(old('forma_pagamento') == 'parcelado' && is_array(old('parcelas')))
                                            @foreach(old('parcelas') as $index => $parcelaData)
                                                @if(isset($parcelaData['valor']) && isset($parcelaData['vencimento']))
                                                <div class="row mb-2 gx-2 align-items-center parcela-item">
                                                    <label class="col-sm-3 col-form-label col-form-label-sm text-sm-end">{{ $index }}ª:</label>
                                                    <div class="col-sm-4">
                                                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm valor-parcela" 
                                                               name="parcelas[{{ $index }}][valor]" 
                                                               value="{{ old('parcelas.'.$index.'.valor') }}" required>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="date" class="form-control form-control-sm data-vencimento-parcela" 
                                                               name="parcelas[{{ $index }}][vencimento]" 
                                                               value="{{ old('parcelas.'.$index.'.vencimento') }}" required>
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <div id="aviso_soma_parcelas" class="mt-2 small text-danger" style="display:none;">
                                        A soma das parcelas não confere com o total da venda!
                                    </div>
                                </div>
                                <hr>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg" id="btnFinalizarVenda">
                                        <i class="bi bi-check-circle-fill me-2"></i>Finalizar Venda
                                    </button>
                                </div>
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
$(document).ready(function() {
    let itensVenda = [];
    let totalVenda = 0;
    let debounceTimerCliente;
    let debounceTimerProduto;

    function formatarMoeda(valor) {
        if (isNaN(parseFloat(valor))) return 'R$ 0,00';
        return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function escapeHtml(text) {
        if (typeof text !== 'string') return String(text || '');
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function buscarClientes(termo) {
        const resultadosDiv = $('#lista_resultados_clientes');
        $.ajax({
            url: "{{ route('clientes.buscar') }}", type: 'GET', data: { termo: termo }, dataType: 'json',
            success: function(clientes) {
                resultadosDiv.empty().show();
                if (clientes && clientes.length > 0) {
                    $.each(clientes, function(index, cliente) {
                        resultadosDiv.append(
                            $('<a href="#" class="list-group-item list-group-item-action item-cliente-busca"></a>')
                            .data({'cliente-id': cliente.id, 'cliente-nome': cliente.nome, 'cliente-cpf': cliente.cpf})
                            .html(`${escapeHtml(cliente.nome)} <small class="text-muted">(CPF: ${escapeHtml(cliente.cpf || 'N/A')})</small>`)
                        );
                    });
                } else {
                    resultadosDiv.append(`<span class="list-group-item disabled">Nenhum cliente encontrado ${termo ? 'para "' + escapeHtml(termo) + '"' : ''}.</span>`);
                }
            },
            error: function(jqXHR) {
                console.error('[CLIENTE] Erro AJAX:', jqXHR.status, jqXHR.responseText);
                resultadosDiv.empty().show().append('<span class="list-group-item text-danger">Erro ao buscar clientes.</span>');
            }
        });
    }

    $('#busca_cliente_nome').on('keyup', function() {
        clearTimeout(debounceTimerCliente);
        const termo = $(this).val();
        $('#cliente_id').val(''); 
        $('#display_cliente_selecionado').html(''); 
        if (termo.length === 0) { 
            buscarClientes(''); 
            return;
        }
        if (termo.length < 1) { 
             $('#lista_resultados_clientes').empty().hide(); return;
        }
        debounceTimerCliente = setTimeout(function() {
            buscarClientes(termo);
        }, 350);
    }).on('focus', function() {
        if ($(this).val().length === 0) { 
            buscarClientes('');
        } else { 
            if ($('#lista_resultados_clientes').children().length === 0) {
                buscarClientes($(this).val()); 
            } else {
                 $('#lista_resultados_clientes').show(); 
            }
        }
    });

    $(document).on('click', '.item-cliente-busca', function(e) {
        e.preventDefault();
        $('#cliente_id').val($(this).data('cliente-id'));
        let nomeCliente = $(this).data('cliente-nome');
        $('#busca_cliente_nome').val(nomeCliente);
        $('#display_cliente_selecionado').html(`Cliente: <strong>${escapeHtml(nomeCliente)}</strong> (CPF: ${escapeHtml($(this).data('cliente-cpf') || 'N/A')})`);
        $('#lista_resultados_clientes').empty().hide();
    });

    function buscarProdutos(termo) {
        const resultadosDiv = $('#lista_resultados_produtos');
        $.ajax({
            url: "{{ route('produtos.buscar') }}", type: "GET", data: { termo: termo }, dataType: 'json',
            success: function(produtos) {
                resultadosDiv.empty().show();
                if (produtos && produtos.length > 0) {
                    let algumProdutoNovoParaMostrar = false;
                    $.each(produtos, function(i, produto) {
                        if ($('#tabela_itens_venda tbody tr[data-item-id="' + produto.id + '"]').length === 0) {
                             resultadosDiv.append(
                                $('<a href="#" class="list-group-item list-group-item-action item-produto-busca"></a>')
                                .data({'produto-id': produto.id, 'produto-nome': produto.nome, 'produto-preco': produto.preco})
                                .html(`${escapeHtml(produto.nome)} <span class="float-end fw-bold">${formatarMoeda(produto.preco)}</span>`)
                            );
                            algumProdutoNovoParaMostrar = true;
                        }
                    });
                    if (!algumProdutoNovoParaMostrar && resultadosDiv.children().length === 0 && termo.length > 0) {
                         resultadosDiv.append(`<span class="list-group-item disabled">Todos produtos para "${escapeHtml(termo)}" já na venda ou não há novos.</span>`);
                    } else if (resultadosDiv.children().length === 0) {
                         resultadosDiv.append(`<span class="list-group-item disabled">Nenhum produto encontrado.</span>`);
                    }
                } else {
                    resultadosDiv.append(`<span class="list-group-item disabled">Nenhum produto encontrado ${termo ? 'para "' + escapeHtml(termo) + '"' : ''}.</span>`);
                }
            },
            error: function(jqXHR) {
                console.error('[PRODUTO] Erro AJAX:', jqXHR.status, jqXHR.responseText);
                resultadosDiv.empty().show().append('<span class="list-group-item text-danger">Erro ao buscar produtos.</span>');
            }
        });
    }

    $('#busca_produto').on('keyup', function() {
        clearTimeout(debounceTimerProduto);
        const termo = $(this).val();
         if (termo.length === 0) { 
            buscarProdutos('');
            return;
        }
        if (termo.length < 1) {
             $('#lista_resultados_produtos').empty().hide(); return;
        }
        debounceTimerProduto = setTimeout(function() {
            buscarProdutos(termo);
        }, 350);
    }).on('focus', function() {
        if ($(this).val().length === 0) { 
            buscarProdutos('');
        } else {
            if ($('#lista_resultados_produtos').children().length === 0) {
                buscarProdutos($(this).val());
            } else {
                 $('#lista_resultados_produtos').show();
            }
        }
    });

    $(document).on('click', '.item-produto-busca', function(e) {
        e.preventDefault();
        let produtoSelecionado = {
            id: $(this).data('produto-id'),
            nome: $(this).data('produto-nome'),
            preco: parseFloat($(this).data('produto-preco')),
            quantidade: 1
        };
        if (!itensVenda.find(item => item.id === produtoSelecionado.id)) {
            adicionarProdutoNaTabela(produtoSelecionado);
        } else {
            $('#tabela_itens_venda tbody tr[data-item-id="' + produtoSelecionado.id + '"]').find('.item-quantidade').focus().select();
        }
        $('#busca_produto').val('');
        $('#lista_resultados_produtos').empty().hide();
    });
    
   
    $(document).on('click', function(event) {
        if (!$(event.target).is('#busca_cliente_nome') && $(event.target).closest('#lista_resultados_clientes').length === 0) {
            $('#lista_resultados_clientes').empty().hide();
        }
        if (!$(event.target).is('#busca_produto') && $(event.target).closest('#lista_resultados_produtos').length === 0) {
            $('#lista_resultados_produtos').empty().hide();
        }
    });
    
    function adicionarProdutoNaTabela(produto) {
        itensVenda.push(produto); 
        let subtotal = produto.quantidade * produto.preco;
        let novaLinhaHtml = `
            <tr data-item-id="${produto.id}">
                <td>${escapeHtml(produto.nome)}<input type="hidden" name="itens[${produto.id}][produto_id]" value="${produto.id}"><input type="hidden" name="itens[${produto.id}][preco_unitario]" value="${produto.preco}"></td>
                <td><input type="number" class="form-control form-control-sm item-quantidade" name="itens[${produto.id}][quantidade]" value="${produto.quantidade}" min="1" style="width: 80px;"></td>
                <td class="text-end item-preco-unit-display">${formatarMoeda(produto.preco)}</td>
                <td class="text-end item-subtotal-display">${formatarMoeda(subtotal)}</td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remover-item" data-remover-id="${produto.id}" title="Remover Item">&times;</button></td>
            </tr>`;
        $('#tabela_itens_venda tbody').append(novaLinhaHtml);
        $('#placeholder_itens_venda').hide();
        calcularTotalVenda();
    }

    $('#tabela_itens_venda').on('change keyup input focusout', '.item-quantidade', function(event) {
        let $input = $(this);
        let $linha = $input.closest('tr');
        let quantidade = parseInt($input.val());
        let itemId = $linha.data('item-id');
        let itemArrayIndex = itensVenda.findIndex(p => p.id === itemId);

        if (event.type === 'focusout' || event.type === 'change' ) {
            if (isNaN(quantidade) || quantidade < 1) { $input.val(1); quantidade = 1; }
        } else { 
            if (isNaN(quantidade)) { quantidade = 0; } 
            else if (quantidade < 0 ) { $input.val(0); quantidade = 0;}
        }
        
        if (itemArrayIndex > -1) {
            itensVenda[itemArrayIndex].quantidade = (quantidade < 1 && (event.type === 'keyup' || event.type === 'input')) ? 0 : quantidade;
            let precoUnitario = itensVenda[itemArrayIndex].preco;
            let subtotal = (itensVenda[itemArrayIndex].quantidade || 0) * precoUnitario;
            $linha.find('.item-subtotal-display').text(formatarMoeda(subtotal));
        }
        calcularTotalVenda();
    });

    $('#tabela_itens_venda').on('click', '.btn-remover-item', function() {
        let itemId = $(this).closest('tr').data('item-id');
        itensVenda = itensVenda.filter(p => p.id !== itemId); 
        $(this).closest('tr').remove(); 
        if (itensVenda.length === 0) { $('#placeholder_itens_venda').show(); }
        calcularTotalVenda();
    });

    function calcularTotalVenda() {
        totalVenda = 0;
        itensVenda.forEach(item => { totalVenda += (item.quantidade || 0) * item.preco; });
        $('#display_total_venda').text(formatarMoeda(totalVenda));
        if ($('#pag_parcelado').is(':checked')) { gerarCamposParcelas(); }
        $('#btnFinalizarVenda').prop('disabled', itensVenda.length === 0 || totalVenda <= 0);
    }

    const radioParcelado = $('#pag_parcelado'), radioAvista = $('#pag_avista'), 
          detalhesParcelamentoDiv = $('#detalhes_parcelamento'), numeroParcelasSelect = $('#numero_parcelas'), 
          camposParcelasDiv = $('#campos_parcelas_editaveis'), infoValorParcelasDiv = $('#info_valor_parcelas'), 
          avisoSomaParcelasDiv = $('#aviso_soma_parcelas');

    function toggleParcelamento() {
        if (radioParcelado.is(':checked')) {
            detalhesParcelamentoDiv.show(); gerarCamposParcelas();
        } else {
            detalhesParcelamentoDiv.hide(); camposParcelasDiv.empty(); infoValorParcelasDiv.empty(); avisoSomaParcelasDiv.hide();
            $('#btnFinalizarVenda').prop('disabled', itensVenda.length === 0 || totalVenda <= 0);
        }
    }
    radioParcelado.add(radioAvista).on('change', toggleParcelamento);
    numeroParcelasSelect.on('change', gerarCamposParcelas);

    function gerarCamposParcelas() {
        if (!radioParcelado.is(':checked') || totalVenda <= 0) {
            camposParcelasDiv.empty(); infoValorParcelasDiv.empty(); avisoSomaParcelasDiv.hide();
            $('#btnFinalizarVenda').prop('disabled', itensVenda.length === 0 || totalVenda <=0); return;
        }
        let numParcelas = parseInt(numeroParcelasSelect.val());
        let valorParcelaBase = totalVenda / numParcelas;
        camposParcelasDiv.empty();
        infoValorParcelasDiv.text(`${numParcelas}x de ${formatarMoeda(valorParcelaBase)} (aprox.)`);
        let somaCalculadaParcelas = 0;
        let dataVencimentoBase = new Date(); 

        for (let i = 1; i <= numParcelas; i++) {
            let dataVencimentoParcela = new Date(dataVencimentoBase);
            dataVencimentoParcela.setMonth(dataVencimentoBase.getMonth() + i);
            let valorParcelaAtual = (i < numParcelas) ? parseFloat(valorParcelaBase.toFixed(2)) : parseFloat((totalVenda - somaCalculadaParcelas).toFixed(2));
            if (i < numParcelas) somaCalculadaParcelas += valorParcelaAtual;
            
            let campoParcela = `
                <div class="row mb-2 gx-2 align-items-center parcela-item">
                    <label class="col-sm-3 col-form-label col-form-label-sm text-sm-end">${i}ª:</label>
                    <div class="col-sm-4"><input type="number" step="0.01" min="0.01" class="form-control form-control-sm valor-parcela" name="parcelas[${i}][valor]" value="${valorParcelaAtual.toFixed(2)}" required></div>
                    <div class="col-sm-5"><input type="date" class="form-control form-control-sm data-vencimento-parcela" name="parcelas[${i}][vencimento]" value="${dataVencimentoParcela.toISOString().split('T')[0]}" required></div>
                </div>`;
            camposParcelasDiv.append(campoParcela);
        }
        verificarSomaParcelas(); 
    }
    
    camposParcelasDiv.on('change keyup input', '.valor-parcela', function() {
        let numParcelas = parseInt(numeroParcelasSelect.val());
        let inputsValor = camposParcelasDiv.find('.valor-parcela');
        let somaManuais = 0; 
        let indiceEditado = inputsValor.index(this);

        if (indiceEditado < numParcelas -1 && numParcelas > 1) { 
            inputsValor.each(function(index) {
                if (index < numParcelas - 1) { somaManuais += parseFloat($(this).val()) || 0;}
            });
            let valorUltimaParcela = totalVenda - somaManuais;
            $(inputsValor[numParcelas - 1]).val(valorUltimaParcela.toFixed(2));
        }
        verificarSomaParcelas();
    });

    function verificarSomaParcelas() {
        if (!radioParcelado.is(':checked')) {
            avisoSomaParcelasDiv.hide();
            $('#btnFinalizarVenda').prop('disabled', itensVenda.length === 0 || totalVenda <=0); return;
        }
        let somaAtualInputs = 0;
        $('.valor-parcela').each(function() { somaAtualInputs += parseFloat($(this).val()) || 0; });
        somaAtualInputs = parseFloat(somaAtualInputs.toFixed(2));
        let totalVendaArredondado = parseFloat(totalVenda.toFixed(2));
        let numParcelas = parseInt(numeroParcelasSelect.val());
        let tolerancia = 0.01 * numParcelas; 

        if (Math.abs(somaAtualInputs - totalVendaArredondado) > tolerancia) { 
            avisoSomaParcelasDiv.text(`Soma parcelas (${formatarMoeda(somaAtualInputs)}) difere do total (${formatarMoeda(totalVendaArredondado)})! (Dif: ${formatarMoeda(somaAtualInputs - totalVendaArredondado)})`).show();
            $('#btnFinalizarVenda').prop('disabled', true);
        } else {
            avisoSomaParcelasDiv.hide();
            $('#btnFinalizarVenda').prop('disabled', itensVenda.length === 0 || totalVenda <=0);
        }
    }
    
    $('#formNovaVenda').on('submit', function(e) {
        if (itensVenda.length === 0) {
            e.preventDefault(); 
            if(typeof Swal !== 'undefined') Swal.fire('Atenção!', 'Nenhum produto foi adicionado à venda.', 'warning');
            else alert('Nenhum produto foi adicionado à venda.');
            return false;
        }
        if ($('#pag_parcelado').is(':checked')) {
            verificarSomaParcelas(); 
            if (avisoSomaParcelasDiv.is(':visible')) {
                e.preventDefault(); 
                if(typeof Swal !== 'undefined') Swal.fire('Atenção!', 'A soma das parcelas não confere com o total da venda. Ajuste os valores.', 'warning');
                else alert('A soma das parcelas não confere com o total da venda. Ajuste os valores.');
                return false;
            }
            let parcelasValidas = true;
            $('#campos_parcelas_editaveis .parcela-item').each(function() {
                let valor = $(this).find('.valor-parcela').val();
                let data = $(this).find('.data-vencimento-parcela').val();
                if (valor === "" || parseFloat(valor) <= 0.001 || data === "") { parcelasValidas = false; }
            });
            if (!parcelasValidas) {
                e.preventDefault();
                if(typeof Swal !== 'undefined') Swal.fire('Atenção!', 'Todas as parcelas devem ter um valor e uma data de vencimento válida.', 'warning');
                else alert('Todas as parcelas devem ter um valor e uma data de vencimento válida.');
                return false;
            }
        }
        $('#btnFinalizarVenda').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processando...');
    });

    toggleParcelamento(); 
    $('#btnFinalizarVenda').prop('disabled', itensVenda.length === 0);
});
</script>
@endpush