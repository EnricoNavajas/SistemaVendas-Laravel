<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venda;
use App\Models\Cliente;
use App\Models\Produto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
       
        $vendasHoje = Venda::whereDate('created_at', Carbon::today())->count();
        $fatHoje    = Venda::whereDate('created_at', Carbon::today())->sum('total');

        $vendasMes  = Venda::whereMonth('created_at', Carbon::now()->month)->count();
        $fatMes     = Venda::whereMonth('created_at', Carbon::now()->month)->sum('total');

        $ticketMedio = $vendasMes > 0 ? ($fatMes / $vendasMes) : 0;

       
        $totalClientes = Cliente::count();

        $graficoVendas = Venda::select(
            DB::raw('sum(total) as total'), 
            DB::raw("DATE_FORMAT(created_at,'%m/%Y') as mes_ano")
        )
        ->groupBy('mes_ano')
        ->orderBy('created_at', 'ASC') 
        ->limit(6)
        ->get();

        $labelsGrafico = $graficoVendas->pluck('mes_ano');
        $dataGrafico   = $graficoVendas->pluck('total');

        $recents = Venda::with('cliente')->latest()->take(5)->get();
        
        $produtosBaixoEstoque = 0; 

        return view('home', compact(
            'vendasHoje', 'fatHoje', 
            'vendasMes', 'fatMes', 
            'ticketMedio', 'totalClientes',
            'labelsGrafico', 'dataGrafico',
            'recents', 'produtosBaixoEstoque'
        ));
    }
}
