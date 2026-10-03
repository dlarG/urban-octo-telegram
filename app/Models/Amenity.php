<?php
namespace App\Models;

use App\Enums\AmenityCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'icon_key'];
    protected $casts = ['category' => AmenityCategory::class];

    public function boardingHouses(): BelongsToMany
    {
        return $this->belongsToMany(BoardingHouse::class, 'boarding_house_amenities')->withTimestamps();
    }
}