<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Audit extends Model
{
    protected $fillable = [
        'user_id',
        'ship_id',
        'reference_number',
        'status',
        'score',
        'consultant',
        'consultant_position',
        'submitted_at',
        'notes',
        'pcro_name',
        'pcro_position',
        'pco_name',
        'pco_position',
        'pic_name',
        'pic_position',
        'port_from',
        'port_to',
        'date_from',
        'date_to',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'date_from'    => 'date',
        'date_to'      => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ship(): BelongsTo
    {
        return $this->belongsTo(Ship::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AnswerIaudit::class, 'audit_id');
    }

    public function generateReferenceNumber(): string
    {
        return 'AUD-' . strtoupper(uniqid()) . '-' . $this->id;
    }
}
