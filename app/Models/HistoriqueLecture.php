<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueLecture extends Model
{
    use HasFactory;

    protected $table = 'historique_lectures';

    protected $fillable = ['user_id', 'memoire_id', 'date_lecture'];

    protected $casts = [
        'date_lecture' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function memoire(): BelongsTo
    {
        return $this->belongsTo(Memoire::class);
    }
}
