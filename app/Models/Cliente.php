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
        'ativo',
    ];

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class, 'cliente_id');
    }
     public function setCpfAttribute($value)
    {
        // Remove tudo que não for número antes de salvar
        $this->attributes['cpf'] = preg_replace('/[^0-9]/', '', $value);
    }

    public function setTelefoneAttribute($value)
    {
        // Remove tudo que não for número antes de salvar
        $this->attributes['telefone'] = preg_replace('/[^0-9]/', '', $value);
    }

    public function getCpfAttribute($value)
    {
        if (!$value) return null;

        // Se já tiver traço (-), significa que já está formatado, então não mexe.
        if (strpos($value, '-') !== false) {
            return $value;
        }
        // Se for só número, aplica a máscara
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $value);
    }

    public function getTelefoneAttribute($value)
    {
        if (!$value) return null;

        // Verifica se já tem o parêntese '('. Se tiver, retorna sem mexer.
        if (strpos($value, '(') !== false) {
            return $value;
        }
        $numeros = preg_replace("/[^0-9]/", "", $value);
        $tam = strlen($numeros);
        // Formata Celular (11 dígitos)
        if ($tam === 11) {
            return preg_replace("/(\d{2})(\d{5})(\d{4})/", "(\$1) \$2-\$3", $numeros);
        }
        // Formata Fixo (10 dígitos)
        if ($tam === 10) {
            return preg_replace("/(\d{2})(\d{4})(\d{4})/", "(\$1) \$2-\$3", $numeros);
        }

        return $value;
    }
}