<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'lat', 'lng'];
    protected $casts = ['lat' => 'decimal:7', 'lng' => 'decimal:7'];

    public function renterProfiles(): HasMany { return $this->hasMany(RenterProfile::class); }

    // Haversine distance helper (km). Usage: Campus::distanceKm($lat, $lng, $lat2, $lng2)
    public static function distanceKm(float $a1, float $o1, float $a2, float $o2): float
    {
        $r = 6371.0;
        $dLat = deg2rad($a2 - $a1);
        $dLon = deg2rad($o2 - $o1);
        $x = sin($dLat / 2) ** 2
           + cos(deg2rad($a1)) * cos(deg2rad($a2)) * sin($dLon / 2) ** 2;
        return $r * 2 * asin(sqrt($x));
    }
}