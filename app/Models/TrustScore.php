<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrustScore extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'score', 'last_event_at'];
    protected $casts = [
        'score'         => 'decimal:2',
        'last_event_at' => 'datetime',
    ];

    public function user(): BelongsTo              { return $this->belongsTo(User::class); }
    public function events(): HasMany              { return $this->hasMany(TrustScoreEvent::class, 'user_id', 'user_id'); }
}