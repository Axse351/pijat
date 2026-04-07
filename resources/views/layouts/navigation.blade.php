<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- LEFT SIDE --}}
            <div class="flex">

                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                {{-- Desktop Navigation --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    @auth
                        @if (in_array(Auth::user()->role, ['admin', 'kasir']))

                            {{-- BOOKING --}}
                            <x-nav-link :href="route('admin.bookings.index')" :active="request()->routeIs('admin.bookings.*')">
                                Booking
                                @php $pending = \App\Models\Booking::where('status','scheduled')->count(); @endphp
                                @if ($pending > 0)
                                    <span
                                        class="ms-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold text-white bg-amber-500 rounded-full">
                                        {{ $pending }}
                                    </span>
                                @endif
                            </x-nav-link>

                            {{-- PEMBAYARAN --}}
                            <x-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                                Pembayaran
                            </x-nav-link>

                            {{-- COA DROPDOWN (admin + kasir) --}}
                            <div class="hidden sm:flex sm:items-center" x-data="{ openCoa: false }"
                                @click.outside="openCoa = false">
                                <div class="relative">
                                    <button @click="openCoa = !openCoa"
                                        class="inline-flex items-center gap-1 px-1 pt-1 border-b-2 text-sm font-medium h-16 transition"
                                        :class="openCoa
                                            ||
                                            {{ request()->routeIs('admin.atk-purchases.*') ||
                                            request()->routeIs('admin.atk-items.*') ||
                                            request()->routeIs('admin.atk-categories.*')
                                                ? 'true'
                                                : 'false' }} ?
                                            'border-indigo-500 text-gray-900 dark:text-gray-100' :
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                                        Pengeluaran
                                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openCoa }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    {{-- Dropdown Panel --}}
                                    <div x-show="openCoa" x-transition
                                        class="absolute left-0 top-full mt-1 w-52 bg-white dark:bg-gray-700 border rounded-lg shadow-lg z-50"
                                        style="display:none;">

                                        {{-- Divider label --}}
                                        <div
                                            class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                            Transaksi
                                        </div>
                                        {{-- <a href="{{ route('admin.atk-purchases.index') }}"
                                            class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.atk-purchases.*') ? 'bg-indigo-100 text-indigo-600 font-semibold' : '' }}">
                                            Catat Pengeluaran
                                        </a> --}}

                                        @if (Auth::user()->role === 'admin')
                                            <div
                                                class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider border-t border-gray-100 dark:border-gray-600 mt-1">
                                                Master Data
                                            </div>
                                            @if (Route::has('admin.atk-categories.index'))
                                                <a href="{{ route('admin.atk-categories.index') }}"
                                                    class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.atk-categories.*') ? 'bg-indigo-100 text-indigo-600 font-semibold' : '' }}">
                                                    Kategori Pengeluaran
                                                </a>
                                            @endif
                                            @if (Route::has('admin.atk-items.index'))
                                                <a href="{{ route('admin.atk-items.index') }}"
                                                    class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.atk-items.*') ? 'bg-indigo-100 text-indigo-600 font-semibold' : '' }}">
                                                    Item Pengeluaran
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- KEHADIRAN --}}
                            <x-nav-link :href="route('admin.attendances.index')" :active="request()->routeIs('admin.attendances.*')">
                                Kehadiran
                            </x-nav-link>

                            {{-- JADWAL --}}
                            <x-nav-link :href="route('admin.schedules.index')" :active="request()->routeIs('admin.schedules.*')">
                                Jadwal
                            </x-nav-link>

                            {{-- LAPORAN --}}
                            <x-nav-link :href="route('admin.laporan.index')" :active="request()->routeIs('admin.laporan.*')">
                                Laporan
                            </x-nav-link>

                            {{-- KOMISI (admin only) --}}
                            @if (Auth::user()->role === 'admin')
                                <x-nav-link :href="route('admin.commissions.index')" :active="request()->routeIs('admin.commissions.*')">
                                    Komisi
                                    @php $unpaidCommissions = \App\Models\Commission::where('is_paid', false)->count(); @endphp
                                    @if ($unpaidCommissions > 0)
                                        <span
                                            class="ms-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                            {{ $unpaidCommissions }}
                                        </span>
                                    @endif
                                </x-nav-link>
                            @endif

                            {{-- MASTER DATA DROPDOWN (admin only) --}}
                            @if (Auth::user()->role === 'admin')
                                <div class="hidden sm:flex sm:items-center" x-data="{ openMaster: false }"
                                    @click.outside="openMaster = false">
                                    <div class="relative">
                                        <button @click="openMaster = !openMaster"
                                            class="inline-flex items-center gap-1 px-1 pt-1 border-b-2 text-sm font-medium h-16 transition"
                                            :class="openMaster
                                                ||
                                                {{ request()->routeIs('admin.services.*') ||
                                                request()->routeIs('admin.therapists.*') ||
                                                request()->routeIs('admin.customers.*') ||
                                                request()->routeIs('admin.memberships.*') ||
                                                request()->routeIs('admin.customer-memberships.*') ||
                                                request()->routeIs('admin.promos.*') ||
                                                request()->routeIs('admin.programs.*') ||
                                                request()->routeIs('admin.barang.*')
                                                    ? 'true'
                                                    : 'false' }} ?
                                                'border-indigo-500 text-gray-900 dark:text-gray-100' :
                                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                                            Master Data
                                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMaster }"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        {{-- Dropdown Panel --}}
                                        <div x-show="openMaster" x-transition
                                            class="absolute left-0 top-full mt-1 w-56 bg-white dark:bg-gray-700 border rounded-lg shadow-lg z-50"
                                            style="display:none;">

                                            <a href="{{ route('admin.services.index') }}"
                                                class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.services.*') ? 'bg-indigo-100 text-indigo-600 font-semibold' : '' }}">
                                                Layanan
                                            </a>
                                            <a href="{{ route('admin.therapists.index') }}"
                                                class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.therapists.*') ? 'bg-indigo-100 text-indigo-600 font-semibold' : '' }}">
                                                Terapis
                                            </a>
                                            <a href="{{ route('admin.customers.index') }}"
                                                class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.customers.*') ? 'bg-indigo-100 text-indigo-600 font-semibold' : '' }}">
                                                Pelanggan
                                            </a>
                                            <a href="{{ route('admin.memberships.index') }}"
                                                class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.memberships.*') ? 'bg-indigo-100 text-indigo-600 font-semibold' : '' }}">
                                                Membership
                                            </a>
                                            <a href="{{ route('admin.promos.index') }}"
                                                class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.promos.*') ? 'bg-indigo-100 text-indigo-600 font-semibold' : '' }}">
                                                Promo
                                            </a>
                                            <a href="{{ route('admin.programs.index') }}"
                                                class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.programs.*') ? 'bg-indigo-100 text-indigo-600 font-semibold' : '' }}">
                                                Program
                                            </a>
                                            <a href="{{ route('admin.barang.index') }}"
                                                class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.barang.*') ? 'bg-indigo-100 text-indigo-600 font-semibold' : '' }}">
                                                Barang
                                            </a>

                                            @if (isset($customer))
                                                <a href="{{ route('admin.customers.membership.index', $customer->id) }}"
                                                    class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    Customer Membership
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @elseif (Auth::user()->role === 'therapist')
                            {{-- ========================================================================== --}}
                            {{-- THERAPIST NAVIGATION --}}
                            {{-- ========================================================================== --}}

                            {{-- PENGAJUAN IZIN --}}
                            <x-nav-link :href="route('terapis.leaves.index')" :active="request()->routeIs('terapis.leaves.*')">
                                Pengajuan Izin
                            </x-nav-link>

                        @endif
                    @endauth
                </div>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 transition">
                            <div>{{ Auth::user()->name }}</div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>

            @auth
                @if (in_array(Auth::user()->role, ['admin', 'kasir']))

                    <x-responsive-nav-link :href="route('admin.bookings.index')" :active="request()->routeIs('admin.bookings.*')">
                        Booking
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                        Pembayaran
                    </x-responsive-nav-link>

                    {{-- COA MOBILE --}}
                    <div class="px-4 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase">Pengeluaran</div>
                    {{-- <x-responsive-nav-link :href="route('admin.atk-purchases.index')" :active="request()->routeIs('admin.atk-purchases.*')">
                        Catat Pengeluaran
                    </x-responsive-nav-link> --}}

                    @if (Auth::user()->role === 'admin')
                        @if (Route::has('admin.atk-categories.index'))
                            <x-responsive-nav-link :href="route('admin.atk-categories.index')" :active="request()->routeIs('admin.atk-categories.*')">
                                Kategori Pengeluaran
                            </x-responsive-nav-link>
                        @endif
                        @if (Route::has('admin.atk-items.index'))
                            <x-responsive-nav-link :href="route('admin.atk-items.index')" :active="request()->routeIs('admin.atk-items.*')">
                                Item Pengeluaran
                            </x-responsive-nav-link>
                        @endif
                    @endif

                    <x-responsive-nav-link :href="route('admin.attendances.index')" :active="request()->routeIs('admin.attendances.*')">
                        Kehadiran
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.schedules.index')" :active="request()->routeIs('admin.schedules.*')">
                        Jadwal
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.laporan.index')" :active="request()->routeIs('admin.laporan.*')">
                        Laporan
                    </x-responsive-nav-link>

                    @if (Auth::user()->role === 'admin')
                        <div class="px-4 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase">Master Data</div>
                        <x-responsive-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')">Layanan</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.therapists.index')" :active="request()->routeIs('admin.therapists.*')">Terapis</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">Pelanggan</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.memberships.index')" :active="request()->routeIs('admin.memberships.*')">Membership</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.promos.index')" :active="request()->routeIs('admin.promos.*')">Promo</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.programs.index')" :active="request()->routeIs('admin.programs.*')">Program</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.barang.index')" :active="request()->routeIs('admin.barang.*')">Barang</x-responsive-nav-link>
                    @endif
                @elseif (Auth::user()->role === 'therapist')
                    {{-- ========================================================================== --}}
                    {{-- THERAPIST MOBILE NAVIGATION --}}
                    {{-- ========================================================================== --}}

                    <x-responsive-nav-link :href="route('terapis.leaves.index')" :active="request()->routeIs('terapis.leaves.*')">
                        Pengajuan Izin
                    </x-responsive-nav-link>

                @endif
            @endauth

        </div>
    </div>
</nav>
