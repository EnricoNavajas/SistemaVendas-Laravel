<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';
   
    protected $fillable = [
        'nome',
        'preco',
        'ativo',
        'estoque',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
    ];

   
    public function vendaItens(): HasMany
    {
        return $this->hasMany(VendaItem::class, 'produto_id');
    }
}