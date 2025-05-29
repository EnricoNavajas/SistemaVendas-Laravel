<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;  

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'cpf',
        'telefone',
    ];

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class, 'cliente_id');
    }
}