<?php
namespace App\Models;

use App\Enums\DisputeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'trust_score_event_id', 'reason',
        'status', 'resolved_by', 'resolved_at', 'resolution_note',
    ];

    protected $casts = [
        'status'      => DisputeStatus::class,
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo            { return $this->belongsTo(User::class); }
    public function event(): BelongsTo           { return $this->belongsTo(TrustScoreEvent::class, 'trust_score_event_id'); }
    public function resolver(): BelongsTo        { return $this->belongsTo(User::class, 'resolved_by'); }
    public function evidence(): HasMany          { return $this->hasMany(DisputeEvidence::class); }
}