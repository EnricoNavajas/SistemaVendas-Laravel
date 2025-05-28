<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Parcela extends Model
{
    use HasFactory;

    protected $table = 'parcelas';

    protected $fillable = [
        'venda_id',
        'numero',
        'valor',
        'vencimento',
    ];
    
    protected $casts = [
        'valor' => 'decimal:2',
        'vencimento' => 'date', 
        'numero' => 'integer',
    ];

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class, 'venda_id');
    }
}