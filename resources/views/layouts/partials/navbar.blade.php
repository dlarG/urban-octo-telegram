{{--
    resources/views/layouts/partials/navbar.blade.php

    In layouts/dashboard.blade.php, replace the whole <header>...</header> block with:
        @include('layouts.partials.navbar')

    Slots it still uses: @yield('topbar'), @yield('topbar_actions'), @yield('user_menu')
--}}
@php
    $navUser    = auth()->user();
    $initials   = collect(preg_split('/\s+/', trim($navUser->name)))->filter()->take(2)
        ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->join('');
    $navFirst   = \Illuminate\Support\Str::of($navUser->name)->explode(' ')->first();
    $unread     = $navUser->unreadNotifications()->count();
    $recent     = $navUser->notifications()->latest()->take(5)->get();
    $notifRoute = match ($navUser->role->value) {
        'renter'   => 'renter.notifications',
        'landlord' => 'landlord.notifications',
        default    => null,
    };
@endphp

{{-- `menu` holds which dropdown is open ('notif' | 'user' | null), so only one is ever open. --}}
<header x-data="{ menu: null, scrolled: false }"
        @scroll.window.passive="scrolled = window.scrollY > 4"
        @keydown.escape.window="menu = null"
        @click.outside="menu = null"
        :class="scrolled ? 'shadow-sm' : ''"
        class="sticky top-0 z-30 border-b border-line bg-white/90 backdrop-blur transition-shadow motion-reduce:transition-none">

    <div class="flex h-16 items-center gap-2 px-4 sm:gap-3 sm:px-6 lg:px-10">

        {{-- Mobile: open sidebar --}}
        <button type="button" @click="sidebarOpen = true"
                class="-ml-2 grid h-10 w-10 flex-none place-items-center rounded-md text-bay hover:bg-mist lg:hidden"
                aria-label="Open menu" aria-controls="sidebar" :aria-expanded="sidebarOpen.toString()">
            <svg viewBox="0 0 24 24" class="ico"><use href="#l-menu"/></svg>
        </button>

        {{-- Left: page title / search, supplied by the role layout --}}
        <div class="min-w-0 flex-1">
            @yield('topbar')
        </div>

        {{-- Right: actions, notifications, account --}}
        <div class="flex flex-none items-center gap-1.5 sm:gap-2">

            @yield('topbar_actions')

            @if ($notifRoute)
                <span class="mx-0.5 hidden h-6 w-px bg-line sm:block" aria-hidden="true"></span>

                {{-- ============ Notifications ============ --}}
                <div class="relative">
                    <button type="button" @click="menu = menu === 'notif' ? null : 'notif'"
                            class="relative grid h-10 w-10 place-items-center rounded-full text-bay transition-colors hover:bg-mist"
                            :class="menu === 'notif' ? 'bg-mist' : ''"
                            aria-haspopup="true" aria-label="Notifications"
                            :aria-expanded="(menu === 'notif').toString()">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-bell"/></svg>
                        @if ($unread > 0)
                            <span class="absolute right-1 top-1 grid h-5 min-w-[1.25rem] place-items-center rounded-full bg-[var(--danger)] px-1 text-[0.65rem] font-bold text-white ring-2 ring-white">
                                <span class="sr-only">{{ $unread }} unread</span>
                                <span aria-hidden="true">{{ $unread > 9 ? '9+' : $unread }}</span>
                            </span>
                        @endif
                    </button>

                    <div x-show="menu === 'notif'" x-cloak
                         x-transition.origin.top.right.duration.150ms
                         class="fixed inset-x-3 top-[4.5rem] z-40 overflow-hidden rounded-xl border border-line bg-white shadow-xl sm:absolute sm:inset-x-auto sm:right-0 sm:top-full sm:mt-2 sm:w-[22rem]">

                        <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-bay">Notifications</span>
                                @if ($unread > 0)
                                    <span class="rounded-full bg-mist px-2 py-0.5 text-xs font-medium text-sea">{{ $unread }} new</span>
                                @endif
                            </div>
                            @if ($unread > 0)
                                <form method="POST" action="{{ route($notifRoute . '.read-all') }}">
                                    @csrf
                                    <button class="text-xs font-medium text-sea hover:underline">Mark all read</button>
                                </form>
                            @endif
                        </div>

                        @if ($recent->isEmpty())
                            <div class="px-6 py-10 text-center">
                                <span class="mx-auto grid h-11 w-11 place-items-center rounded-full bg-mist text-sea">
                                    <svg viewBox="0 0 24 24" class="ico"><use href="#l-bell"/></svg>
                                </span>
                                <p class="mt-3 text-sm font-semibold text-ink">You're all caught up</p>
                                <p class="mt-1 text-xs text-muted">New activity on your account will show up here.</p>
                            </div>
                        @else
                            <div class="max-h-[22rem] overflow-y-auto overscroll-contain">
                                @foreach ($recent as $n)
                                    @php $data = $n->data; $isUnread = is_null($n->read_at); @endphp
                                    <a href="{{ $data['url'] ?? route($notifRoute) }}"
                                       class="flex items-start gap-3 border-b border-line px-4 py-3 transition-colors last:border-0 hover:bg-mist/60 {{ $isUnread ? 'bg-[#F4FAF9]' : '' }}">
                                        <span class="mt-1.5 h-2 w-2 flex-none rounded-full {{ $isUnread ? 'bg-sea' : 'bg-transparent' }}" aria-hidden="true"></span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm {{ $isUnread ? 'font-semibold' : 'font-medium' }} text-bay">
                                                {{ $data['title'] ?? 'Notification' }}
                                            </p>
                                            @if (! empty($data['body']))
                                                <p class="mt-0.5 line-clamp-2 text-xs leading-snug text-muted">{{ $data['body'] }}</p>
                                            @endif
                                            <p class="mt-1 text-[11px] text-muted">{{ $n->created_at->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <a href="{{ route($notifRoute) }}"
                               class="block border-t border-line bg-mist/40 px-4 py-2.5 text-center text-xs font-medium text-sea hover:bg-mist hover:underline">
                                View all notifications
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ============ Account menu ============ --}}
            <div class="relative">
                <button type="button" @click="menu = menu === 'user' ? null : 'user'"
                        class="flex items-center gap-2.5 rounded-full py-1 pl-1 pr-1 transition-colors hover:bg-mist md:pr-3"
                        :class="menu === 'user' ? 'bg-mist' : ''"
                        aria-haspopup="menu" aria-label="Account menu"
                        :aria-expanded="(menu === 'user').toString()">
                    <span class="grid h-9 w-9 flex-none place-items-center rounded-full bg-bay text-sm font-semibold text-white" aria-hidden="true">{{ $initials }}</span>
                    <span class="hidden min-w-0 text-left leading-tight md:block">
                        <span class="block max-w-[9rem] truncate text-sm font-semibold text-bay">{{ $navFirst }}</span>
                        <span class="block text-xs text-muted">{{ $navUser->role->label() }}</span>
                    </span>
                    <svg viewBox="0 0 24 24" class="ico hidden text-muted transition-transform duration-150 motion-reduce:transition-none md:block"
                         :class="menu === 'user' ? 'rotate-180' : ''" style="width:1rem;height:1rem"><use href="#l-chevron-down"/></svg>
                </button>

                <div x-show="menu === 'user'" x-cloak
                     x-transition.origin.top.right.duration.150ms
                     class="absolute right-0 top-full z-40 mt-2 w-64 max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-line bg-white shadow-xl"
                     role="menu">
                    <div class="flex items-center gap-3 border-b border-line px-4 py-3.5">
                        <span class="grid h-10 w-10 flex-none place-items-center rounded-full bg-bay text-sm font-semibold text-white" aria-hidden="true">{{ $initials }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-bay">{{ $navUser->name }}</p>
                            <p class="truncate text-xs text-muted">{{ $navUser->email }}</p>
                        </div>
                    </div>
                    <div class="p-1.5">
                        @yield('user_menu')
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem" class="menu-item text-danger">
                                <svg viewBox="0 0 24 24" class="ico"><use href="#l-logout"/></svg>
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>