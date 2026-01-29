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
        'preco' => 'float',
        'estoque' => 'integer',
        'ativo' => 'boolean',
    ];

   
    public function vendaItens(): HasMany
    {
        return $this->hasMany(VendaItem::class, 'produto_id');
    }

    public function setPrecoAttribute($value)
    {
        // Se o valor vier vazio, salva 0
        if (empty($value)) {
            $this->attributes['preco'] = 0;
            return;
        }
        // Remove R$, pontos e troca vírgula por ponto
        $source = ['.', ','];
        $replace = ['', '.'];
        $value = str_replace($source, $replace, $value);

        $this->attributes['preco'] = $value;
    }

    // Converte "1200.50" para "1.200,50" (Para exibir nos inputs de edição)
    public function getPrecoFormatadoAttribute()
    {
        return number_format($this->preco, 2, ',', '.');
    }
}