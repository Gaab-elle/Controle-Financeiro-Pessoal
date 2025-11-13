<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lancamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conta_id',
        'tipo',
        'descricao',
        'valor',
        'data',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data' => 'date',
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com Conta
     */
    public function conta(): BelongsTo
    {
        return $this->belongsTo(Conta::class);
    }
}

