<?php
namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenancyController extends Controller
{
    public function index(Request $request): View
    {
        $tenancies = $request->user()
            ->tenancies()
            ->with(['room.boardingHouse', 'review'])
            ->latest()
            ->paginate(12);

        return view('renter.tenancies.index', compact('tenancies'));
    }
}