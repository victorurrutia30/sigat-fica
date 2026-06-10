<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfirmacionSeccionConsolidado extends Model
{
    use HasFactory;

    protected $table = 'confirmaciones_seccion_consolidado';

    protected $fillable = [
        'consolidado_id',
        'seccion_id',
        'confirmado_por',
        'confirmado_en',
    ];

    protected function casts(): array
    {
        return [
            'confirmado_en' => 'datetime',
        ];
    }

    public function consolidado(): BelongsTo
    {
        return $this->belongsTo(Consolidado::class, 'consolidado_id');
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function confirmadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmado_por');
    }
}
