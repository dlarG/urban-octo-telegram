@extends('layouts.landlord')

@section('content')
    <a href="{{ route('landlord.tenancies.index') }}" class="text-sm text-indigo-600 hover:underline">
        ← Tenancies
    </a>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-3 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
            <ul class="space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif

    <div class="mt-4 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">{{ $tenancy->renter->name }}</h1>
            <p class="text-gray-600 mt-1">
                {{ $tenancy->room->room_label }} · {{ $tenancy->room->boardingHouse->name }}
            </p>
        </div>

        @if ($tenancy->isActive())
            <form method="POST" action="{{ route('landlord.tenancies.end', $tenancy) }}" class="space-y-2">
                @csrf
                <input type="text" name="reason" placeholder="Reason (required)" required
                    class="w-full rounded-md border-gray-300 text-sm">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="checkout_compliant" value="1" checked>
                    <span>Checkout compliant (+2)</span>
                </label>
                <button class="w-full rounded-md border border-red-200 text-red-700 px-3 py-1.5 text-sm hover:bg-red-50">
                    End tenancy
                </button>
            </form>
        @endif
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3 text-sm">
        <div class="bg-white border rounded-lg p-4">
            <div class="text-gray-500">Start date</div>
            <div class="font-medium mt-1">{{ $tenancy->start_date->format('M d, Y') }}</div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="text-gray-500">Monthly rent</div>
            <div class="font-medium mt-1">₱{{ number_format($tenancy->monthly_rent, 2) }}</div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="text-gray-500">Status</div>
            <div class="font-medium mt-1">{{ $tenancy->status->value }}</div>
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-lg font-medium">Payments</h2>
        <p class="text-sm text-gray-500 mt-1">
            Recording a payment fires a trust event for the renter.
        </p>

        @if ($tenancy->payments->isNotEmpty())
            <div class="mt-3 bg-white border rounded-lg divide-y text-sm">
                @foreach ($tenancy->payments->sortByDesc('due_date') as $p)
                    <div class="p-3 flex items-center justify-between gap-3">
                        <div>
                            <div class="font-medium">₱{{ number_format($p->amount, 2) }}</div>
                            <div class="text-xs text-gray-500">
                                Due {{ $p->due_date->format('M d, Y') }}
                                @if ($p->paid_at) · Paid {{ $p->paid_at->format('M d, Y') }} @endif
                            </div>
                        </div>
                        <span class="text-xs rounded-full px-2 py-0.5
                            {{ $p->status->value === 'paid' ? 'bg-green-100 text-green-800' :
                            ($p->status->value === 'late' ? 'bg-red-100 text-red-800' :
                            'bg-gray-100 text-gray-700') }}">
                            {{ $p->status->value }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($tenancy->isActive())
            <form method="POST" action="{{ route('landlord.tenancies.payments.store', $tenancy) }}"
                class="mt-4 bg-white border rounded-lg p-4 space-y-3">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount (₱)</label>
                        <input type="number" step="0.01" name="amount" required
                            value="{{ $tenancy->monthly_rent }}"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Due date</label>
                        <input type="date" name="due_date" required
                            value="{{ now()->toDateString() }}"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="on_time" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                            <option value="1">Paid on time (+2)</option>
                            <option value="0">Paid late (−5)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <input type="text" name="reference" placeholder="Reference (optional, e.g. GCash ref)"
                        class="block w-full rounded-md border-gray-300 text-sm">
                </div>
                <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Record payment
                </button>
            </form>
        @endif
    </div>
@endsection