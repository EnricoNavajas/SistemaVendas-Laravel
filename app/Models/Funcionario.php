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
}