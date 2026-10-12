<?php
namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Tenancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Tenancy $tenancy): RedirectResponse
    {
        $this->authorize('create', [Review::class, $tenancy]);

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'tenancy_id'        => $tenancy->id,
            'renter_id'         => $request->user()->id,
            'boarding_house_id' => $tenancy->room->boarding_house_id,
            'rating'            => $data['rating'],
            'comment'           => $data['comment'] ?? null,
        ]);

        return back()->with('status', 'Thanks for the review!');
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $review->update($data);

        return back()->with('status', 'Review updated.');
    }
}