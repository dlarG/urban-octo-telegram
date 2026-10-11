<?php
namespace App\Http\Controllers\Renter;

use App\Enums\DisputeStatus;
use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\DisputeEvidence;
use App\Models\TrustScoreEvent;
use App\Services\StorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function __construct(protected StorageService $storage) {}

    public function store(Request $request, TrustScoreEvent $event): RedirectResponse
    {
        // Renter can only dispute their own events
        abort_unless($event->user_id === $request->user()->id, 403);

        // Only negative events can be disputed
        abort_if($event->delta >= 0, 403, 'Only negative events can be disputed.');

        $data = $request->validate([
            'reason'   => ['required', 'string', 'max:1000'],
            'evidence' => ['nullable', 'array', 'max:5'],
            'evidence.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $dispute = Dispute::create([
            'user_id'              => $request->user()->id,
            'trust_score_event_id' => $event->id,
            'reason'               => $data['reason'],
            'status'               => DisputeStatus::Open,
        ]);

        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                DisputeEvidence::create([
                    'dispute_id'    => $dispute->id,
                    'path'          => $this->storage->put($file, 'disputes/'.$dispute->id),
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return back()->with('status', 'Dispute filed. An admin will review it.');
    }
}