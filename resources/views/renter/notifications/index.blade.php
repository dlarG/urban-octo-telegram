@extends('layouts.renter')

@section('content')
    <div class="max-w-3xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Notifications</h1>
            @if ($notifications->isNotEmpty())
                <form method="POST" action="{{ route('renter.notifications.read-all') }}">
                    @csrf
                    <button class="text-sm text-indigo-600 hover:underline">Mark all as read</button>
                </form>
            @endif
        </div>

        @if ($notifications->isEmpty())
            <div class="mt-6 bg-white border rounded-lg p-8 text-center text-gray-500">
                No notifications yet.
            </div>
        @else
            <div class="mt-6 bg-white border rounded-lg divide-y">
                @foreach ($notifications as $n)
                    @php $data = $n->data; $unread = is_null($n->read_at); @endphp
                    <a href="{{ $data['url'] ?? '#' }}"
                       class="block p-4 hover:bg-gray-50 {{ $unread ? 'bg-indigo-50/40' : '' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-medium">{{ $data['title'] ?? 'Notification' }}</div>
                                <p class="text-sm text-gray-600 mt-0.5">{{ $data['body'] ?? '' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                            </div>
                            @if ($unread)
                                <span class="h-2 w-2 rounded-full bg-indigo-500 mt-2"></span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $notifications->links() }}</div>
        @endif
    </div>
@endsection