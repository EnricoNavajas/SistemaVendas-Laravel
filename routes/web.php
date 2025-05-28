<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginFuncionarioController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
})->name('raiz');


Route::get('login', [LoginFuncionarioController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginFuncionarioController::class, 'login']);
Route::post('logout', [LoginFuncionarioController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');
   
    Route::get('/produtos/buscar', [ProdutoController::class, 'buscar'])->name('produtos.buscar');
    Route::get('/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');

    Route::resource('produtos', ProdutoController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('funcionarios', FuncionarioController::class);
     
    Route::get('/vendas/nova', [VendaController::class, 'create'])->name('vendas.create');
    Route::post('/vendas', [VendaController::class, 'store'])->name('vendas.store');
    Route::get('/vendas', [VendaController::class, 'index'])->name('vendas.index');
    Route::get('/vendas/{venda}/edit', [VendaController::class, 'edit'])->name('vendas.edit');
    Route::put('/vendas/{venda}', [VendaController::class, 'update'])->name('vendas.update');
    Route::delete('/vendas/{venda}', [VendaController::class, 'destroy'])->name('vendas.destroy');

    Route::get('vendas/{venda}/pdf', [VendaController::class, 'gerarPdf'])->name('vendas.gerarPdf');
   
});