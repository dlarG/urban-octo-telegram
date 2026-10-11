<?php
namespace App\Http\Controllers\Renter;

use App\Enums\DisputeStatus;
use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\TrustScoreEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrustScoreController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $score = $user->trustScore;
        if (! $score) {
            abort(500, 'Trust score row missing — integrity error.');
        }

        $events = TrustScoreEvent::where('user_id', $user->id)
            ->with(['tenancy.room.boardingHouse', 'payment'])
            ->latest()
            ->paginate(20);

        $disputes = Dispute::where('user_id', $user->id)
            ->with('event')
            ->latest()
            ->get();

        return view('renter.trust.show', compact('score', 'events', 'disputes'));
    }
}