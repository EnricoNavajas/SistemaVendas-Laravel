<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Venda;       
use App\Models\VendaItem;  
use App\Models\Parcela;     
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;   
use Illuminate\Support\Facades\Log;  
use Illuminate\Support\Facades\Validator; 
use Barryvdh\DomPDF\Facade\Pdf;

class VendaController extends Controller
{
     public function index()
    {
        $vendas = Venda::with(['funcionario', 'cliente'])
                        ->orderBy('created_at', 'desc')  
                        ->paginate(10); 

        return view('vendas.index', compact('vendas'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('vendas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
       
        Log::info('Dados recebidos para nova venda:', $request->all());

        $dadosValidados = $request->validate([
            'cliente_id'          => 'nullable|exists:clientes,id',
            'forma_pagamento'     => 'required|in:avista,parcelado',
            'itens'               => 'required|array|min:1',
            'itens.*.produto_id'  => 'required|exists:produtos,id',
            'itens.*.quantidade'  => 'required|integer|min:1',
            'itens.*.preco_unitario' => 'required|numeric|min:0.01',
            'parcelas'            => 'required_if:forma_pagamento,parcelado|array|min:1',
            'parcelas.*.valor'    => 'required_if:forma_pagamento,parcelado|numeric|min:0.01',
            'parcelas.*.vencimento' => 'required_if:forma_pagamento,parcelado|date|after_or_equal:today',
        ], [
            'itens.required' => 'A venda deve conter pelo menos um item.',
            'itens.min' => 'A venda deve conter pelo menos um item.',
            'itens.*.produto_id.required' => 'Um ID de produto é obrigatório para cada item.',
            'itens.*.produto_id.exists' => 'Um dos produtos selecionados é inválido.',
            'itens.*.quantidade.required' => 'A quantidade é obrigatória para cada item.',
            'itens.*.quantidade.min' => 'A quantidade de cada item deve ser pelo menos 1.',
            'itens.*.preco_unitario.required' => 'O preço unitário é obrigatório para cada item.',
            'parcelas.required_if' => 'As parcelas são obrigatórias para pagamento parcelado.',
            'parcelas.*.valor.required_if' => 'O valor de cada parcela é obrigatório.',
            'parcelas.*.vencimento.required_if' => 'A data de vencimento de cada parcela é obrigatória.',
            'parcelas.*.vencimento.after_or_equal' => 'A data de vencimento da parcela não pode ser retroativa.'
        ]);

        
        DB::beginTransaction();

        try {
            $totalVendaCalculado = 0;
            $itensParaSalvar = [];

            foreach ($dadosValidados['itens'] as $itemInput) {
                $produto = Produto::find($itemInput['produto_id']);
                if (!$produto) { 
                    throw new \Exception("Produto com ID {$itemInput['produto_id']} não encontrado.");
                }

                $quantidade = (int)$itemInput['quantidade'];
                
                $precoUnitario = (float)$itemInput['preco_unitario'];
                $subtotalItem = $quantidade * $precoUnitario;
                $totalVendaCalculado += $subtotalItem;

                $itensParaSalvar[] = [
                    'produto_id'   => $produto->id,
                    'quantidade'   => $quantidade,
                    'preco_unit' => $precoUnitario, 
                    'subtotal'     => $subtotalItem,
                ];
            }
            $totalVendaCalculado = round($totalVendaCalculado, 2);

           
            $venda = Venda::create([
                'funcionario_id'  => Auth::id(), 
                'cliente_id'      => $dadosValidados['cliente_id'] ?? null,
                'forma_pagamento' => $dadosValidados['forma_pagamento'],
                'total'           => $totalVendaCalculado,
            ]);

           
            foreach ($itensParaSalvar as $itemData) {
                $venda->itens()->create($itemData);
            }

            
            if ($dadosValidados['forma_pagamento'] === 'parcelado') {
                if (empty($dadosValidados['parcelas'])) {
                    throw new \Exception("Detalhes das parcelas não fornecidos para venda parcelada.");
                }

                $somaParcelas = 0;
                $numeroParcelaContador = 0; 

                $parcelasInput = $request->input('parcelas'); 

                foreach ($parcelasInput as $numero => $parcelaData) {
                    if (!isset($parcelaData['valor']) || !isset($parcelaData['vencimento'])) continue; 

                    $valorParcela = round((float)$parcelaData['valor'], 2);
                    $somaParcelas += $valorParcela;
                    $numeroParcelaContador++;

                    $venda->parcelas()->create([ 
                        'numero'     => $numeroParcelaContador,
                        'valor'      => $valorParcela,
                        'vencimento' => $parcelaData['vencimento'],
                    ]);
                }
                $somaParcelas = round($somaParcelas, 2);

               
                if (abs($somaParcelas - $totalVendaCalculado) > 0.01 * count($parcelasInput)) { 
                    throw new \Exception("A soma das parcelas (R$ {$somaParcelas}) não confere com o total da venda (R$ {$totalVendaCalculado}).");
                }
            }

            DB::commit();

            return redirect()->route('vendas.index')->with('success', 'Venda registrada com sucesso! ID da Venda: ' . $venda->id);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Erro de validação ao salvar venda: ' . $e->getMessage(), $e->errors());
            return redirect()->back()->withInput()->withErrors($e->errors())->with('error', 'Erro de validação ao salvar a venda.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro geral ao salvar venda: ' . $e->getMessage());
            return redirect()->route('vendas.create')->withInput()->with('error', 'Ocorreu um erro ao registrar a venda: ' . $e->getMessage());
        }
    }

   public function edit($id)
    {
        
        $venda = Venda::with([
            'cliente',
            'itens.produto', 
            'parcelas' => function ($query) {
                $query->orderBy('numero', 'asc'); 
            }
        ])->findOrFail($id); 
        return view('vendas.edit', compact('venda'));
    }
    
    public function update(Request $request, Venda $venda) 
    {
        Log::info('Dados recebidos para ATUALIZAR venda ID: ' . $venda->id, $request->all());

        $dadosValidados = $request->validate([
            'cliente_id'         => 'nullable|exists:clientes,id',
            'forma_pagamento'    => 'required|in:avista,parcelado',
            'itens'              => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.preco_unitario' => 'required|numeric|min:0.01',
            'parcelas'                 => 'required_if:forma_pagamento,parcelado|array',
            'parcelas.*.valor'       => 'required_if:forma_pagamento,parcelado|numeric|min:0.01',
            'parcelas.*.vencimento'  => 'required_if:forma_pagamento,parcelado|date|after_or_equal:today',
            'numero_parcelas_select' => 'required_if:forma_pagamento,parcelado|integer|min:1|max:6'
        ], [
            
           'itens.required' => 'A venda deve conter pelo menos um item.',
            'itens.min' => 'A venda deve conter pelo menos um item.',
            'itens.*.produto_id.required' => 'Um ID de produto é obrigatório para cada item.',
            'itens.*.produto_id.exists' => 'Um dos produtos selecionados é inválido.',
            'itens.*.quantidade.required' => 'A quantidade é obrigatória para cada item.',
            'itens.*.quantidade.min' => 'A quantidade de cada item deve ser pelo menos 1.',
            'itens.*.preco_unitario.required' => 'O preço unitário é obrigatório para cada item.',
            'parcelas.required_if' => 'As parcelas são obrigatórias para pagamento parcelado.',
            'parcelas.*.valor.required_if' => 'O valor de cada parcela é obrigatório.',
            'parcelas.*.vencimento.required_if' => 'A data de vencimento de cada parcela é obrigatória.',
            'parcelas.*.vencimento.after_or_equal' => 'A data de vencimento da parcela não pode ser retroativa.'
           
        ]);

        DB::beginTransaction();

        try {
           
            $totalVendaCalculado = 0;
            $itensParaSalvar = [];

            foreach ($dadosValidados['itens'] as $itemInput) {
                $quantidade = (int)$itemInput['quantidade'];
                $precoUnitario = (float)$itemInput['preco_unitario'];
                $subtotalItem = $quantidade * $precoUnitario;
                $totalVendaCalculado += $subtotalItem;

                $itensParaSalvar[] = [
                    'produto_id' => $itemInput['produto_id'],
                    'quantidade' => $quantidade,
                    'preco_unit' => $precoUnitario,
                    'subtotal'   => $subtotalItem,
                ];
            }
            $totalVendaCalculado = round($totalVendaCalculado, 2);

           
            $vendaDataUpdate = [
                'cliente_id'      => $dadosValidados['cliente_id'] ?? null,
                'forma_pagamento' => $dadosValidados['forma_pagamento'],
                'total'           => $totalVendaCalculado,
              
            ];
            
            if ($dadosValidados['forma_pagamento'] === 'parcelado') {
                 $vendaDataUpdate['numero_parcelas'] = (int)$dadosValidados['numero_parcelas_select'];
            } else {
                $vendaDataUpdate['numero_parcelas'] = null; 
            }
            $venda->update($vendaDataUpdate);
           
            $venda->itens()->delete();
            foreach ($itensParaSalvar as $itemData) {
                $venda->itens()->create($itemData); 
            }

            $venda->parcelas()->delete(); 

            if ($dadosValidados['forma_pagamento'] === 'parcelado') {
                $parcelasInput = $request->input('parcelas');
                 if (empty($parcelasInput) && (int)$dadosValidados['numero_parcelas_select'] > 0) { 
                    throw new \Exception("Detalhes das parcelas não fornecidos para venda parcelada.");
                }

                $somaParcelas = 0;
                if (!empty($parcelasInput)) { 
                    foreach ($parcelasInput as $numeroDaParcela => $parcelaData) {
                        if (!isset($parcelaData['valor']) || !isset($parcelaData['vencimento'])) {
                            Log::warning("Dados de parcela incompletos para ATUALIZAR venda ID {$venda->id}, parcela prevista #{$numeroDaParcela}", $parcelaData);
                            continue;
                        }
                        $valorParcela = round((float)$parcelaData['valor'], 2);
                        $somaParcelas += $valorParcela;

                        $venda->parcelas()->create([
                            'numero'     => (int)$numeroDaParcela,
                            'valor'      => $valorParcela,
                            'vencimento' => $parcelaData['vencimento'],
                        ]);
                    }
                }
                $somaParcelas = round($somaParcelas, 2);
                $numeroDeParcelasProcessadas = is_array($parcelasInput) ? count($parcelasInput) : 0;

                if ($numeroDeParcelasProcessadas > 0 && $totalVendaCalculado > 0 && abs($somaParcelas - $totalVendaCalculado) > (0.01 * $numeroDeParcelasProcessadas + 0.001)) {
                    DB::rollBack();
                    Log::error("Soma de parcelas ({$somaParcelas}) diverge do total da venda ({$totalVendaCalculado}) para ATUALIZAR venda ID {$venda->id}.");
                    throw new \Exception("A soma das parcelas (R$ {$somaParcelas}) não confere com o total da venda (R$ {$totalVendaCalculado}). Verifique os valores das parcelas.");
                } else if ($totalVendaCalculado > 0 && $numeroDeParcelasProcessadas == 0 && (int)$dadosValidados['numero_parcelas_select'] > 0) {
                    
                    DB::rollBack();
                    Log::error("Nenhuma parcela processada para ATUALIZAR venda ID {$venda->id} embora esperadas.");
                    throw new \Exception("As parcelas são esperadas para esta forma de pagamento, mas não foram fornecidas corretamente.");
                }

            }

            DB::commit();
            return redirect()->route('vendas.index')->with('success', 'Venda #' . $venda->id . ' atualizada com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Erro de validação ao ATUALIZAR venda ID ' . $venda->id . ': ' . $e->getMessage(), ['errors' => $e->errors(), 'input' => $request->all()]);
            return redirect()->back()->withInput()->withErrors($e->errors())->with('error', 'Erro de validação ao atualizar a venda.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro geral ao ATUALIZAR venda ID ' . $venda->id . ': ' . $e->getMessage(), ['input' => $request->all(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('vendas.edit', $venda->id)->withInput()->with('error', 'Ocorreu um erro ao atualizar a venda: ' . $e->getMessage());
        }
    }

     public function destroy(Venda $venda)
    {
       
        try {
            $vendaId = $venda->id;
            $venda->delete(); 

            return redirect()->route('vendas.index')
                             ->with('success', "Venda #{$vendaId} excluída com sucesso!");
        } catch (\Exception $e) {
            Log::error("Erro ao excluir venda #{$venda->id}: " . $e->getMessage());
            return redirect()->route('vendas.index')
                             ->with('error', 'Erro ao excluir a venda. Tente novamente.');
        }
    }

     public function gerarPdf(Venda $venda)
    {
        $venda->load([
            'cliente',
            'funcionario', 
            'itens.produto',
            'parcelas' => function ($query) {
                $query->orderBy('numero', 'asc');
            }
        ]);
        
        $nomeArquivo = 'venda_' . $venda->id . '_' . now()->format('Y-m-d') . '.pdf';
       
    $logoPath = null;
    $caminhoRelativoLogo = 'imgs/sualogo.png'; 

    if (file_exists(public_path($caminhoRelativoLogo))) {
        $logoPath = public_path($caminhoRelativoLogo);
    } else {
        Log::warning('Arquivo de logo não encontrado em: ' . public_path($caminhoRelativoLogo));
    }

    $dadosParaPdf = [
        'venda' => $venda,
        'dataGeracao' => now()->format('d/m/Y H:i:s'),
        'nomeEmpresa' => 'Nome da Sua Empresa', 
        'enderecoEmpresa' => 'Seu Endereço Completo, Cidade - UF', 
        'telefoneEmpresa' => 'Seu Telefone', 
        'emailEmpresa' => 'seu@email.com', 
        'cnpjEmpresa' => 'Seu CNPJ', 
        'logoPath' => $logoPath, 
    ];

        $pdf = Pdf::loadView('vendas.pdf.relatorio_venda', $dadosParaPdf);
        return $pdf->download($nomeArquivo);
    }
}