<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 
use Illuminate\Database\Eloquent\Relations\HasMany;   

class Venda extends Model
{
    use HasFactory;

    protected $table = 'vendas';

    
    protected $fillable = [
        'funcionario_id',
        'cliente_id',
        'forma_pagamento',
        'total',
        'status',           
        'data_aprovacao'    
    ];

    
    protected $casts = [
        'total' => 'decimal:2',
    ];

    
    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    
    public function itens(): HasMany 
    {
        return $this->hasMany(VendaItem::class, 'venda_id');
    }

        public function parcelas(): HasMany
    {
        return $this->hasMany(Parcela::class, 'venda_id');
    }

    
}