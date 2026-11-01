@extends('layouts.renter')

@section('content')
    <h1 class="text-2xl font-semibold">My tenancies</h1>
    <p class="text-gray-600 mt-1">Your rental history on RenTahanan.</p>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif

    @if ($tenancies->isEmpty())
        <div class="mt-6 bg-white border rounded-lg p-8 text-center text-gray-500">
            No tenancies yet.
        </div>
    @else
        <div class="mt-6 space-y-3">
            @foreach ($tenancies as $t)
                <div class="bg-white border rounded-lg p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="font-medium">{{ $t->room->boardingHouse->name }}</h2>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $t->room->room_label }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $t->start_date->format('M d, Y') }}
                                @if ($t->completed_at) – {{ $t->completed_at->format('M d, Y') }} @endif
                            </p>
                        </div>
                        <span class="text-xs rounded-full px-2 py-0.5
                            {{ $t->status->value === 'active' ? 'bg-green-100 text-green-800' :
                               ($t->status->value === 'completed' ? 'bg-gray-100 text-gray-700' :
                               'bg-red-100 text-red-800') }}">
                            {{ $t->status->value }}
                        </span>
                    </div>

                    @if ($t->isCompleted() && ! $t->review)
                        <form method="POST" action="{{ route('renter.tenancies.review', $t) }}"
                              class="mt-4 border-t pt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Your rating</label>
                                <div class="mt-1 flex gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" required>
                                            <span class="text-2xl text-gray-300 peer-checked:text-yellow-500 hover:text-yellow-400">★</span>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <textarea name="comment" rows="2" maxlength="1000"
                                      placeholder="How was your stay? (optional)"
                                      class="block w-full rounded-md border-gray-300 text-sm"></textarea>
                            <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                Post review
                            </button>
                        </form>
                    @elseif ($t->review)
                        <div class="mt-4 border-t pt-4">
                            <div class="flex items-center gap-2 text-yellow-500">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $t->review->rating ? '★' : '☆' }}</span>
                                @endfor
                                <span class="text-sm text-gray-500 ml-1">Your review</span>
                            </div>
                            @if ($t->review->comment)
                                <p class="mt-2 text-sm text-gray-700 italic">"{{ $t->review->comment }}"</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $tenancies->links() }}</div>
    @endif
@endsection