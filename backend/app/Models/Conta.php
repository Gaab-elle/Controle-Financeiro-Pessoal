<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nome',
        'tipo',
        'saldo_inicial',
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com Lançamentos
     */
    public function lancamentos(): HasMany
    {
        return $this->hasMany(Lancamento::class);
    }

    /**
     * Calcula o saldo atual da conta
     */
    public function getSaldoAtualAttribute(): float
    {
        $saldo = $this->saldo_inicial ?? 0;
        
        // Carrega os lançamentos se ainda não foram carregados
        if (!$this->relationLoaded('lancamentos')) {
            $this->load('lancamentos');
        }
        
        foreach ($this->lancamentos as $lancamento) {
            if ($lancamento->tipo === 'entrada') {
                $saldo += $lancamento->valor;
            } else {
                $saldo -= $lancamento->valor;
            }
        }
        
        return round($saldo, 2);
    }
}

