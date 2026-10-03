<?php
namespace App\Models;

use App\Enums\RenterType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenterProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'renter_type',
        'campus_id', 'major', 'year_level',
        'occupation', 'employer',
        'stay_duration',
        'budget_min', 'budget_max',
        'valid_id_path', 'valid_id_submitted_at',
        'trust_score_consent',
    ];

    protected $casts = [
        'renter_type'            => RenterType::class,
        'valid_id_submitted_at'  => 'datetime',
        'trust_score_consent'    => 'boolean',
        'budget_min'             => 'decimal:2',
        'budget_max'             => 'decimal:2',
    ];

    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
    public function campus(): BelongsTo    { return $this->belongsTo(Campus::class); }
}