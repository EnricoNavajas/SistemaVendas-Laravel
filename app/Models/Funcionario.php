<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class Funcionario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'funcionarios';

    
    protected $fillable = [
        'nome',
        'cpf',
        'senha', 
        'ativo',
    ];

    
    protected $hidden = [
        'senha',
        'remember_token',
    ];

    
    protected $casts = [
        'senha' => 'hashed',
    ];

    public function getAuthPassword(): string
    {
        return $this->senha;
    }

        public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class, 'funcionario_id');
    }

    public function setCpfAttribute($value)
    {
        // Salva apenas números no banco
        $this->attributes['cpf'] = preg_replace('/[^0-9]/', '', $value);
    }

    public function getCpfAttribute($value)
    {
        if (!$value) return null;

        // Se já tiver traço, retorna como está (evita bugar se já formatado)
        if (strpos($value, '-') !== false) {
            return $value;
        }
        // Aplica a máscara 000.000.000-00
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $value);
    }
}