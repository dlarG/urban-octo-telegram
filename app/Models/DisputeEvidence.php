<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisputeEvidence extends Model
{
    use HasFactory;

    protected $fillable = ['dispute_id', 'path', 'original_name'];

    public function dispute(): BelongsTo { return $this->belongsTo(Dispute::class); }
}