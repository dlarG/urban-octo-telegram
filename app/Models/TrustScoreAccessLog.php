<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustScoreAccessLog extends Model
{
    use HasFactory;

    protected $table = 'trust_score_access_log';

    protected $fillable = [
        'viewer_id', 'subject_id', 'rental_application_id', 'ip_address', 'viewed_at',
    ];

    protected $casts = ['viewed_at' => 'datetime'];

    public function viewer(): BelongsTo    { return $this->belongsTo(User::class, 'viewer_id'); }
    public function subject(): BelongsTo   { return $this->belongsTo(User::class, 'subject_id'); }
    public function application(): BelongsTo { return $this->belongsTo(RentalApplication::class, 'rental_application_id'); }
}