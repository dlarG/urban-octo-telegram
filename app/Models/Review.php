<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenancy_id', 'renter_id', 'boarding_house_id',
        'rating', 'comment',
    ];

    protected $casts = ['rating' => 'integer'];

    public function tenancy(): BelongsTo        { return $this->belongsTo(Tenancy::class); }
    public function renter(): BelongsTo         { return $this->belongsTo(User::class, 'renter_id'); }
    public function boardingHouse(): BelongsTo  { return $this->belongsTo(BoardingHouse::class); }
}