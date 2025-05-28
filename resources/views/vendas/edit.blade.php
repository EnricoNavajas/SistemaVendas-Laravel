@extends('layouts.app')




@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-10">
            <div class="card">
                <div class="card-header">{{ __('Editar Venda #') }}{{ $venda->id ?? 'N/A' }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vendas.update', $venda->id ?? 0) }}" id="formEditarVenda">
                        @csrf
                        @method('PUT')
                        <div class="row gx-3">
                            <div class="col-md-7 col-lg-8">
                                <div class="mb-3 row">
                                    <label for="busca_cliente_nome" class="col-lg-2 col-form-label text-lg-end">{{ __('Cliente') }}</label>
                                    <div class="col-lg-10 search-wrapper">
                                        <input type="text" class="form-control @error('cliente_id') is-invalid @enderror" id="busca_cliente_nome" placeholder="Digite nome ou CPF (Opcional)" autocomplete="off" value="{{ old('busca_cliente_nome', optional(isset($venda) ? $venda->cliente : null)->nome) }}">
                                        <input type="hidden" id="cliente_id" name="cliente_id" value="{{ old('cliente_id', optional(isset($venda) ? $venda->cliente : null)->id) }}">
                                        <div id="display_cliente_selecionado" class="mt-1 small text-muted">
                                            @if(isset($venda) && $venda->cliente)
                                                Cliente: <strong>{{ $venda->cliente->nome }}</strong> (CPF: {{ $venda->cliente->cpf ?? 'N/A' }})
                                            @endif
                                        </div>
                                        <div id="lista_resultados_clientes" class="list-group autocomplete-results position-absolute" style="display:none;"></div>
                                        @error('cliente_id')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <hr>
                                <div class="mb-3 row">
                                    <label for="busca_produto" class="col-lg-2 col-form-label text-lg-end">{{ __('Produto') }}</label>
                                    <div class="col-lg-10 search-wrapper">
                                        <input type="text" class="form-control" id="busca_produto" placeholder="Digite para buscar e adicionar novo produto" autocomplete="off">
                                        <div id="lista_resultados_produtos" class="list-group autocomplete-results position-absolute" style="display:none;"></div>
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
                                            @if(isset($venda) && $venda->itens && $venda->itens->count() > 0)
                                                @foreach($venda->itens as $item)
                                                    <tr data-item-id="{{ $item->produto_id ?? 'temp_id_' . $loop->index }}"
                                                        data-produto-nome="{{ optional(optional($item)->produto)->nome ?? 'Produto Inválido' }}"
                                                        data-produto-preco="{{ (float)(optional($item)->preco_unit ?? 0) }}"
                                                        data-produto-quantidade="{{ (int)(optional($item)->quantidade ?? 0) }}">
                                                        <td>
                                                            {{ optional(optional($item)->produto)->nome ?? 'Produto Inválido/Excluído' }}
                                                            <input type="hidden" name="itens[{{ $item->produto_id ?? 'temp_id_' . $loop->index }}][produto_id]" value="{{ $item->produto_id ?? '' }}">
                                                            <input type="hidden" name="itens[{{ $item->produto_id ?? 'temp_id_' . $loop->index }}][preco_unitario]" value="{{ (float)(optional($item)->preco_unit ?? 0) }}">
                                                        </td>
                                                        <td><input type="number" class="form-control form-control-sm item-quantidade" name="itens[{{ $item->produto_id ?? 'temp_id_' . $loop->index }}][quantidade]" value="{{ (int)(optional($item)->quantidade ?? 1) }}" min="1" style="width: 80px;"></td>
                                                        <td class="text-end item-preco-unit-display"></td>
                                                        <td class="text-end item-subtotal-display"></td>
                                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remover-item" data-remover-id="{{ $item->produto_id ?? 'temp_id_' . $loop->index }}" title="Remover Item">&times;</button></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                @php
                                    $_deveEsconderPlaceholder = (isset($venda) && $venda->itens && $venda->itens->isNotEmpty());
                                @endphp
                                <p class="text-center text-muted" id="placeholder_itens_venda" @if($_deveEsconderPlaceholder) style="display:none;" @endif>Nenhum produto adicionado à venda.</p>
                            </div>

                            <div class="col-md-5 col-lg-4 border-start ps-md-4">
                                @php
                                    $_formaPagamentoChecked = old('forma_pagamento', optional(isset($venda) ? $venda : null)->forma_pagamento ?? 'avista');
                                    $_actualNumeroParcelasExistentes = 0;
                                    if (isset($venda) && $venda->parcelas && $venda->parcelas->isNotEmpty()) {
                                        $_actualNumeroParcelasExistentes = $venda->parcelas->count();
                                    }
                                    $_numeroParcelasSelected = (int)old(
                                        'numero_parcelas_select',
                                        ($_formaPagamentoChecked == 'parcelado' && $_actualNumeroParcelasExistentes > 0)
                                            ? $_actualNumeroParcelasExistentes
                                            : (optional(isset($venda) ? $venda : null)->numero_parcelas ?? 1)
                                    );
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
                                        @if( $_formaPagamentoChecked == 'parcelado' && isset($venda) && $venda->parcelas && $venda->parcelas->isNotEmpty() )
                                            @foreach($venda->parcelas as $index => $parcela)
                                            <div class="row mb-2 gx-2 align-items-center parcela-item">
                                                <label class="col-sm-3 col-form-label col-form-label-sm text-sm-end">{{ $parcela->numero ?? ($index + 1) }}ª:</label>
                                                <div class="col-sm-4">
                                                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm valor-parcela" 
                                                           name="parcelas[{{ $parcela->numero ?? ($index + 1) }}][valor]" 
                                                           value="{{ number_format(optional($parcela)->valor ?? 0, 2, '.', '') }}" required>
                                                </div>
                                                <div class="col-sm-5">
                                                    <input type="date" class="form-control form-control-sm data-vencimento-parcela" 
                                                           name="parcelas[{{ $parcela->numero ?? ($index + 1) }}][vencimento]" 
                                                           value="{{ optional($parcela)->vencimento ? (\Carbon\Carbon::parse(optional($parcela)->vencimento)->format('Y-m-d')) : '' }}" required>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div id="aviso_soma_parcelas" class="mt-2 small text-danger" style="display:none;">
                                        A soma das parcelas não confere com o total da venda!
                                    </div>
                                </div>
                                <hr>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success btn-lg" id="btnAtualizarVendaComConfirmacao">
                                        <i class="bi bi-check-circle-fill me-2"></i>Atualizar Venda
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
    let inicializacaoCompleta = false; 

    function formatarMoeda(valor) {
        const numero = parseFloat(valor);
        if (isNaN(numero)) return 'R$ 0,00';
        return numero.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function escapeHtml(text) {
        if (typeof text !== 'string') return String(text === null || typeof text === 'undefined' ? '' : text);
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
        if (termo.length === 0) { buscarClientes(''); return; }
        if (termo.length < 1) { $('#lista_resultados_clientes').empty().hide(); return; }
        debounceTimerCliente = setTimeout(function() { buscarClientes(termo); }, 350);
    }).on('focus', function() {
        if ($(this).val().length === 0) { buscarClientes('');
        } else {
            if ($('#lista_resultados_clientes').children().length === 0) { buscarClientes($(this).val());
            } else { $('#lista_resultados_clientes').show(); }
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
                         resultadosDiv.append(`<span class="list-group-item disabled">Nenhum produto novo encontrado.</span>`);
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
        if (termo.length === 0) { buscarProdutos(''); return; } 
        if (termo.length < 1) { $('#lista_resultados_produtos').empty().hide(); return; }
        debounceTimerProduto = setTimeout(function() { buscarProdutos(termo); }, 350);
    }).on('focus', function() {
        if ($(this).val().length === 0) { buscarProdutos('');
        } else { 
            if ($('#lista_resultados_produtos').children().length === 0) { buscarProdutos($(this).val());
            } else { $('#lista_resultados_produtos').show(); }
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
        if (!itensVenda.find(item => String(item.id) === String(produtoSelecionado.id))) {
            adicionarProdutoNaTabela(produtoSelecionado); 
        } else {
            let $inputQtdExistente = $('#tabela_itens_venda tbody tr[data-item-id="' + produtoSelecionado.id + '"]').find('.item-quantidade');
            let qtdAtual = parseInt($inputQtdExistente.val()) || 0;
            $inputQtdExistente.val(qtdAtual + 1).trigger('change'); 
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
        let subtotal = (parseFloat(produto.quantidade) || 0) * (parseFloat(produto.preco) || 0);
        let novaLinhaHtml = `
            <tr data-item-id="${produto.id}"
                data-produto-nome="${escapeHtml(produto.nome)}"
                data-produto-preco="${parseFloat(produto.preco) || 0}"
                data-produto-quantidade="${parseInt(produto.quantidade) || 1}">
                <td>${escapeHtml(produto.nome)}
                    <input type="hidden" name="itens[${produto.id}][produto_id]" value="${produto.id}">
                    <input type="hidden" name="itens[${produto.id}][preco_unitario]" value="${parseFloat(produto.preco) || 0}">
                </td>
                <td><input type="number" class="form-control form-control-sm item-quantidade" name="itens[${produto.id}][quantidade]" value="${parseInt(produto.quantidade) || 1}" min="1" style="width: 80px;"></td>
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
        let itemId = String($linha.data('item-id')); 
        let itemArrayIndex = itensVenda.findIndex(p => String(p.id) === itemId);

        if (event.type === 'focusout' || event.type === 'change') { 
            if (isNaN(quantidade) || quantidade < 1) { $input.val(1); quantidade = 1; }
        } else { 
            if (isNaN(quantidade)) { quantidade = 0; } 
            else if (quantidade < 0 ) { $input.val(0); quantidade = 0;}
        }
        
        if (itemArrayIndex > -1) {
            itensVenda[itemArrayIndex].quantidade = quantidade;
            $linha.data('produto-quantidade', quantidade); 
            let precoUnitario = parseFloat(itensVenda[itemArrayIndex].preco) || 0;
            let subtotal = (quantidade || 0) * precoUnitario;
            $linha.find('.item-subtotal-display').text(formatarMoeda(subtotal));
        } else {
            console.warn("Item da tabela não encontrado no array itensVenda para atualização. ID:", itemId, "Array:", itensVenda);
        }
        calcularTotalVenda(); 
    });

    $('#tabela_itens_venda').on('click', '.btn-remover-item', function() {
        let itemId = String($(this).closest('tr').data('item-id'));
        itensVenda = itensVenda.filter(p => String(p.id) !== itemId); 
        $(this).closest('tr').remove();
        if (itensVenda.length === 0) {
            $('#placeholder_itens_venda').show();
        }
        calcularTotalVenda();
    });

    function calcularTotalVenda() {
        totalVenda = 0; 
        // console.log("Recalculando total. itensVenda no início:", JSON.parse(JSON.stringify(itensVenda)));

        itensVenda.forEach(function(item, index) { 
            let qtd = parseFloat(item.quantidade) || 0;
            let preco = parseFloat(item.preco) || 0;
            // console.log(`Item ${index} (ID: ${item.id}): Qtd_original=${item.quantidade}, Preco_original=${item.preco}, Qtd_parseada=${qtd}, Preco_parseado=${preco}, Subtotal_calculado=${qtd * preco}`);
            totalVenda += qtd * preco;
        });

        // console.log("Total da Venda recalculado:", totalVenda); 
        $('#display_total_venda').text(formatarMoeda(totalVenda));

        if (inicializacaoCompleta && radioParcelado.is(':checked')) {
            // Se o total da venda mudou E o pagamento é parcelado E a inicialização já ocorreu,
            // é preciso recalcular/redistribuir os valores das parcelas.
            gerarCamposParcelas(); 
        }
        atualizarEstadoBotaoFinalizar(); 
    }

    function atualizarEstadoBotaoFinalizar() {
        let desabilitar = itensVenda.length === 0 || !itensVenda.some(item => (parseInt(item.quantidade) || 0) > 0) || totalVenda <= 0;
        if (radioParcelado.is(':checked')) { 
            if (avisoSomaParcelasDiv.is(':visible')) { 
                desabilitar = true;
            }
            let numCamposParcela = $('#campos_parcelas_editaveis .parcela-item').length;
            if (totalVenda > 0 && numCamposParcela === 0) { 
                desabilitar = true;
            }
            $('#campos_parcelas_editaveis .parcela-item').each(function() {
                let valor = $(this).find('.valor-parcela').val();
                let data = $(this).find('.data-vencimento-parcela').val();
                if (!valor || parseFloat(valor) < 0.01 || !data) {
                    desabilitar = true; 
                }
            });
        }
        $('#btnAtualizarVendaComConfirmacao').prop('disabled', desabilitar);
    }

    const radioParcelado = $('#pag_parcelado'), radioAvista = $('#pag_avista'),
          detalhesParcelamentoDiv = $('#detalhes_parcelamento'), numeroParcelasSelect = $('#numero_parcelas'),
          camposParcelasDiv = $('#campos_parcelas_editaveis'), infoValorParcelasDiv = $('#info_valor_parcelas'),
          avisoSomaParcelasDiv = $('#aviso_soma_parcelas');

    function toggleParcelamento() {
        if (radioParcelado.is(':checked')) {
            detalhesParcelamentoDiv.show();
            if (inicializacaoCompleta) { // Só gera se não for a inicialização, ou se na inicialização não foram renderizadas pelo Blade
                // Se não há campos de parcelas e o total é > 0, ou se já há campos e o usuário está apenas trocando de "à vista" para "parcelado"
                 if ($('#campos_parcelas_editaveis .parcela-item').length === 0 && totalVenda > 0) {
                    gerarCamposParcelas();
                } else {
                     // Se já existem campos (renderizados pelo Blade ou JS), recalcula/verifica
                    verificarSomaParcelas(); // Para o caso de já ter campos e só estar re-exibindo
                    if (totalVenda > 0 && $('#campos_parcelas_editaveis .parcela-item').length === 0) {
                        gerarCamposParcelas(); // Se limpou e precisa gerar de novo
                    } else if (totalVenda > 0) {
                        // Apenas atualiza a info se já existem parcelas e o número está correto
                        let numParcelasAtual = $('#campos_parcelas_editaveis .parcela-item').length;
                        if (numParcelasAtual > 0) {
                             let valorParcelaBase = totalVenda / numParcelasAtual;
                             infoValorParcelasDiv.text(`${numParcelasAtual}x de ${formatarMoeda(valorParcelaBase)} (aprox.)`);
                        }
                    }
                }
            }
        } else { 
            detalhesParcelamentoDiv.hide();
            // Não limpar os campos aqui, pois o usuário pode voltar para parcelado e querer os valores anteriores.
            // Apenas esconde e o backend não processará as parcelas se forma_pagamento não for 'parcelado'.
            // camposParcelasDiv.empty(); 
            // infoValorParcelasDiv.empty();
            avisoSomaParcelasDiv.hide();
        }
        if (inicializacaoCompleta) atualizarEstadoBotaoFinalizar();
    }
    radioParcelado.add(radioAvista).on('change', toggleParcelamento);
    
    numeroParcelasSelect.on('change', function() {
        if (radioParcelado.is(':checked')) { 
            gerarCamposParcelas(); // Mudar o número de parcelas sempre recria os campos
        }
    });

    function gerarCamposParcelas() {
        if (!radioParcelado.is(':checked')) { // Segurança extra
            detalhesParcelamentoDiv.hide(); 
            // camposParcelasDiv.empty(); // Melhor não limpar aqui também, por consistência com toggleParcelamento
            // infoValorParcelasDiv.empty();
            avisoSomaParcelasDiv.hide();
            if(inicializacaoCompleta) atualizarEstadoBotaoFinalizar();
            return;
        }
        detalhesParcelamentoDiv.show(); // Garante que está visível

        if (totalVenda <= 0) {
            camposParcelasDiv.empty().html('<p class="text-muted small">Adicione itens à venda para definir parcelas.</p>');
            infoValorParcelasDiv.empty();
            avisoSomaParcelasDiv.hide();
            if(inicializacaoCompleta) atualizarEstadoBotaoFinalizar();
            return;
        }

        let numParcelas = parseInt(numeroParcelasSelect.val()) || 1;
        camposParcelasDiv.empty(); // Limpa apenas quando vai gerar novos campos
        let valorParcelaBase = totalVenda / numParcelas;
        infoValorParcelasDiv.text(`${numParcelas}x de ${formatarMoeda(valorParcelaBase)} (aprox.)`);

        let somaDistribuida = 0;
        let dataVencimentoBase = new Date(); 

        for (let i = 1; i <= numParcelas; i++) {
            let valorParcelaAtual;
            let dataVencimentoParcelaObj = new Date(dataVencimentoBase); 
            let currentMonth = dataVencimentoParcelaObj.getMonth();
            dataVencimentoParcelaObj.setMonth(currentMonth + i); 
            if (dataVencimentoParcelaObj.getMonth() !== (currentMonth + i) % 12) { 
                dataVencimentoParcelaObj.setDate(0); 
            }
            let dataVencimentoParcelaStr = dataVencimentoParcelaObj.toISOString().split('T')[0]; 

            if (i < numParcelas) {
                valorParcelaAtual = parseFloat(valorParcelaBase.toFixed(2));
                somaDistribuida += valorParcelaAtual;
            } else { 
                valorParcelaAtual = parseFloat((totalVenda - somaDistribuida).toFixed(2));
            }

            let campoParcela = `
                <div class="row mb-2 gx-2 align-items-center parcela-item">
                    <label class="col-sm-3 col-form-label col-form-label-sm text-sm-end">${i}ª:</label>
                    <div class="col-sm-4"><input type="number" step="0.01" min="0.01" class="form-control form-control-sm valor-parcela" name="parcelas[${i}][valor]" value="${(valorParcelaAtual > 0 ? valorParcelaAtual: 0.01).toFixed(2)}" required></div>
                    <div class="col-sm-5"><input type="date" class="form-control form-control-sm data-vencimento-parcela" name="parcelas[${i}][vencimento]" value="${dataVencimentoParcelaStr}" required></div>
                </div>`;
            camposParcelasDiv.append(campoParcela);
        }
        verificarSomaParcelas(); 
    }
    
    camposParcelasDiv.on('change keyup input', '.valor-parcela', function() {
        let numParcelas = parseInt(numeroParcelasSelect.val()) || 1;
        let inputsValor = camposParcelasDiv.find('.valor-parcela');
        let somaManuais = 0;
        // Ajuste para recalcular a última parcela apenas se houver mais de uma e não for a última sendo editada
        if (numParcelas > 1 && inputsValor.length === numParcelas && !$(this).is(inputsValor.last())) {
            inputsValor.each(function(index) {
                if (index < numParcelas - 1) { 
                    somaManuais += parseFloat($(this).val()) || 0;
                }
            });
            let valorUltimaParcela = totalVenda - somaManuais;
            valorUltimaParcela = Math.max(0.01, parseFloat(valorUltimaParcela.toFixed(2)));
            // Aplica ao último input de valor
            $(inputsValor[numParcelas - 1]).val(valorUltimaParcela.toFixed(2));
        }
        verificarSomaParcelas(); 
    });

    function verificarSomaParcelas() {
        if (!radioParcelado.is(':checked')) {
            avisoSomaParcelasDiv.hide();
            if (inicializacaoCompleta) atualizarEstadoBotaoFinalizar();
            return;
        }
        if (totalVenda <= 0 && $('#campos_parcelas_editaveis .parcela-item').length > 0) { 
             // Se o total é zero mas ainda tem campos de parcela (ex: usuário removeu todos os itens)
            avisoSomaParcelasDiv.text('O total da venda é R$0,00. As parcelas não são necessárias.').show();
            if (inicializacaoCompleta) atualizarEstadoBotaoFinalizar();
            return;
        }
         if (totalVenda <= 0) {
            avisoSomaParcelasDiv.hide();
            if (inicializacaoCompleta) atualizarEstadoBotaoFinalizar();
            return;
        }


        let somaAtualInputs = 0;
        let temParcelasParaSomar = false;
        $('#campos_parcelas_editaveis .valor-parcela').each(function() {
            somaAtualInputs += parseFloat($(this).val()) || 0;
            temParcelasParaSomar = true;
        });

        if (!temParcelasParaSomar && totalVenda > 0) { 
            avisoSomaParcelasDiv.text('Defina o número de parcelas.').show();
            if (inicializacaoCompleta) $('#btnAtualizarVendaComConfirmacao').prop('disabled', true); 
            return;
        }
        if (!temParcelasParaSomar) { 
            avisoSomaParcelasDiv.hide();
            if (inicializacaoCompleta) atualizarEstadoBotaoFinalizar();
            return;
        }

        somaAtualInputs = parseFloat(somaAtualInputs.toFixed(2));
        let totalVendaArredondado = parseFloat(totalVenda.toFixed(2));
        let numParcelasAtual = $('#campos_parcelas_editaveis .parcela-item').length;
        // A tolerância pode ser um pouco maior para evitar falsos positivos com arredondamentos múltiplos.
        let tolerancia = 0.01 * numParcelasAtual + 0.005; // Ajuste pequeno na tolerância

        if (Math.abs(somaAtualInputs - totalVendaArredondado) > tolerancia) {
            avisoSomaParcelasDiv.text(`Soma parcelas (${formatarMoeda(somaAtualInputs)}) difere do total (${formatarMoeda(totalVendaArredondado)})! (Dif: ${formatarMoeda(Math.abs(somaAtualInputs - totalVendaArredondado))})`).show();
        } else {
            avisoSomaParcelasDiv.hide();
        }
        if (inicializacaoCompleta) atualizarEstadoBotaoFinalizar();
    }

    function inicializarFormulario() {
        console.log("Inicializando formulário de edição...");
        itensVenda = []; 

        $('#tabela_itens_venda tbody tr').each(function(index, el) {
            const $row = $(el);
            const itemIdFromDOM = String($row.data('item-id')); 
            const item = {
                id: itemIdFromDOM,
                nome: $row.data('produto-nome'),
                preco: parseFloat($row.data('produto-preco')), 
                quantidade: parseInt($row.find('.item-quantidade').val()) 
            };
            if (item.id && typeof item.nome === 'string' && item.nome.trim() !== '' && !isNaN(item.preco) && !isNaN(item.quantidade)) {
                if (item.quantidade > 0) { 
                    itensVenda.push(item);
                    $row.find('.item-preco-unit-display').text(formatarMoeda(item.preco));
                    let subtotal = item.quantidade * item.preco;
                    $row.find('.item-subtotal-display').text(formatarMoeda(subtotal));
                }
            } else {
                // console.warn("Item com dados inválidos ou quantidade zero na tabela (NÃO adicionado ao array JS):", item, "Raw data:", $row.data());
            }
        });
        // console.log("Array itensVenda após leitura do DOM:", JSON.parse(JSON.stringify(itensVenda)) );

        if (itensVenda.length === 0 && $('#tabela_itens_venda tbody tr').length > 0) {
            // console.warn("Nenhum item válido foi encontrado na tabela para popular itensVenda, apesar de haver linhas na tabela.");
        }
        if (itensVenda.length === 0) {
            $('#placeholder_itens_venda').show();
        } else {
            $('#placeholder_itens_venda').hide();
        }

        calcularTotalVenda(); 
        // console.log("Total da Venda após cálculo inicial:", totalVenda);

        if (radioParcelado.is(':checked')) {
            detalhesParcelamentoDiv.show();
            let numParcelasRenderizadasPeloBlade = $('#campos_parcelas_editaveis .parcela-item').length;
            let numParcelasSelecionadoNoDropdown = parseInt(numeroParcelasSelect.val()) || 0;

            console.log("Modo parcelado (init). Total: " + totalVenda + 
                        ", Parcelas Renderizadas Blade: " + numParcelasRenderizadasPeloBlade +
                        ", Parcelas Select: " + numParcelasSelecionadoNoDropdown);

            if (numParcelasRenderizadasPeloBlade > 0 && numParcelasRenderizadasPeloBlade === numParcelasSelecionadoNoDropdown) {
                console.log('Usando parcelas pré-renderizadas pelo Blade. Verificando soma.');
                if (totalVenda > 0) {
                     let valorParcelaBase = totalVenda / numParcelasRenderizadasPeloBlade; // Recalcula info com base no total real dos itens
                     infoValorParcelasDiv.text(`${numParcelasRenderizadasPeloBlade}x de ${formatarMoeda(valorParcelaBase)} (aprox.)`);
                } else {
                    infoValorParcelasDiv.empty();
                }
                verificarSomaParcelas(); 
            } else if (totalVenda > 0) {
                console.log('Modo parcelado, mas sem parcelas pré-renderizadas ou contagem inconsistente. Gerando campos de parcela via JS (init).');
                gerarCamposParcelas();
            } else { 
                console.log('Total zero ou modo parcelado sem itens. Limpando/Gerando campos de parcela (init).');
                gerarCamposParcelas(); 
            }
        } else { 
            console.log("Modo à vista (init).");
            detalhesParcelamentoDiv.hide();
            camposParcelasDiv.empty(); // Se é à vista, não deve haver campos de parcela
            infoValorParcelasDiv.empty();
            avisoSomaParcelasDiv.hide();
        }

        inicializacaoCompleta = true; 
        console.log("Inicialização completa.");
        atualizarEstadoBotaoFinalizar(); 
    }

    inicializarFormulario(); 

    $('#btnAtualizarVendaComConfirmacao').on('click', function(e) {
        e.preventDefault();
        const $button = $(this); 
        if ($button.prop('disabled')) return;

        let errosValidacao = [];
        if (itensVenda.length === 0 || !itensVenda.some(item => (parseInt(item.quantidade) || 0) > 0)) {
            errosValidacao.push('Nenhum produto foi adicionado à venda.');
        } else if (totalVenda <=0 && itensVenda.length > 0) { // Se tem itens mas o total é zero (ex: todos com qtd 0)
             errosValidacao.push('O total da venda deve ser maior que zero se houver itens.');
        } else if (totalVenda < 0) { // Total negativo não faz sentido
            errosValidacao.push('O total da venda não pode ser negativo.');
        }


        if (radioParcelado.is(':checked')) {
            verificarSomaParcelas(); 
            if (avisoSomaParcelasDiv.is(':visible') && totalVenda > 0) { // Só considera erro de soma se o total for > 0
                errosValidacao.push(avisoSomaParcelasDiv.text()); 
            }
            let numCamposParcela = $('#campos_parcelas_editaveis .parcela-item').length;
            if (totalVenda > 0 && numCamposParcela === 0) {
                errosValidacao.push('Defina as parcelas para a venda.');
            } else if (numCamposParcela > 0) { 
                let parcelasValidas = true;
                $('#campos_parcelas_editaveis .parcela-item').each(function() {
                    let valor = $(this).find('.valor-parcela').val();
                    let data = $(this).find('.data-vencimento-parcela').val();
                    if (!valor || parseFloat(valor) < 0.01 || !data) {
                        parcelasValidas = false;
                    }
                });
                if (!parcelasValidas) {
                    errosValidacao.push('Todas as parcelas devem ter um valor e uma data de vencimento válida.');
                }
            }
        }

        if (errosValidacao.length > 0) {
            if(typeof Swal !== 'undefined') {
                Swal.fire('Atenção!', errosValidacao.join('<br>'), 'warning');
            } else {
                alert(errosValidacao.join('\n'));
            }
            return;
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Confirmar Atualização',
                text: "Você tem certeza que deseja atualizar esta venda?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Sim, atualizar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processando...');
                    $('#formEditarVenda').submit(); 
                }
            });
        } else {
            console.error("SweetAlert (Swal) is not defined. Using native confirm.");
            if(confirm("Você tem certeza que deseja atualizar esta venda? (SweetAlert não pôde ser exibido)")) {
                $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processando...');
                $('#formEditarVenda').submit();
            }
        }
    });
});
</script>
@endpush