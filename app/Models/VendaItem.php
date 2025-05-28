<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo

class VendaItem extends Model
{
    use HasFactory;

    protected $table = 'venda_itens';

    public $timestamps = false;

    protected $fillable = [
        'venda_id',
        'produto_id',
        'quantidade',
        'preco_unit',
        'subtotal',
    ];
 
    protected $casts = [
        'preco_unit' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantidade' => 'integer',
    ];

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class, 'venda_id');
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}