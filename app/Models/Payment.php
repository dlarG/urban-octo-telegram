<?php
namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenancy_id', 'amount', 'due_date', 'paid_at',
        'status', 'reference', 'notes',
    ];

    protected $casts = [
        'status'   => PaymentStatus::class,
        'amount'   => 'decimal:2',
        'due_date' => 'date',
        'paid_at'  => 'date',
    ];

    public function tenancy(): BelongsTo { return $this->belongsTo(Tenancy::class); }

    public function isLate(): bool
    {
        return $this->status === PaymentStatus::Pending
            && $this->due_date
            && $this->due_date->isPast();
    }
}