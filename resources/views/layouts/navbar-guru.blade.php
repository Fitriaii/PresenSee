<header class="relative z-30 py-2 bg-white border-b border-gray-200 shadow-sm">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Mobile hamburger -->
            <button
                class="inline-flex items-center justify-center p-2 text-purple-600 transition-colors duration-200 bg-purple-100 rounded-md hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 md:hidden"
                @click="toggleSideMenu"
                aria-label="Menu"
            >
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <!-- Logo dan Judul -->
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <img
                        src="{{ asset('images/logo-smp.png') }}"
                        alt="Logo SMP Islamiyah Widodaren"
                        class="object-cover w-12 h-12 "
                    >
                </div>
                <div class="hidden sm:block">
                    <h1 class="text-xl font-bold tracking-tight text-gray-900 font-heading">
                        SMP Islamiyah Widodaren
                    </h1>
                    <p class="text-sm font-medium text-gray-600">Sistem Presensi Sekolah</p>
                </div>
                <div class="block sm:hidden">
                    <h1 class="text-lg font-bold text-gray-900 font-heading">
                        SMP Islamiyah
                    </h1>
                </div>
            </div>

            <!-- Profile Section -->
            <div class="relative ml-3">
                <div>
                    <button
                        type="button"
                        class="relative flex items-center px-3 py-2 space-x-3 text-sm transition-all duration-200 ease-in-out bg-white rounded-xl "
                        @click="toggleProfileMenu"
                        @keydown.escape="closeProfileMenu"
                        aria-expanded="false"
                        aria-haspopup="true"
                    >
                        <span class="sr-only">Open user menu</span>

                        <!-- User Name (Hidden on small screens) -->
                        <div class="hidden text-left lg:block">
                            <div class="text-sm font-semibold text-gray-900 font-heading">
                                {{ Auth::user()->name }}
                            </div>
                            @auth
                            <p class="text-xs font-medium text-purple-600">
                                {{ Auth::user()->getRoleNames()->first() ?? 'User' }}
                            </p>
                            @endauth
                        </div>

                        <!-- Profile Picture or Avatar -->
                        @if(Auth::user()->profile_picture)
                            <img
                                src="{{ Storage::url(Auth::user()->profile_picture) }}"
                                alt="Foto Profil"
                                class="object-cover w-10 h-10 rounded-full ring-2 ring-purple-100"
                            />
                        @else
                            <div class="flex items-center justify-center w-10 h-10 rounded-full shadow-md bg-gradient-to-br from-purple-600 to-orange-500 ring-2 ring-white">
                                <span class="text-sm font-semibold text-white font-heading">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif



                        <!-- Dropdown Arrow -->
                        <svg
                            class="hidden w-4 h-4 text-gray-500 transition-transform duration-200 md:block"
                            :class="{ 'rotate-180': isProfileMenuOpen }"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Dropdown menu -->
                <ul
                    x-show="isProfileMenuOpen"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click.away="closeProfileMenu"
                    @keydown.escape.window="closeProfileMenu"
                    class="absolute right-0 z-50 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white rounded-md shadow-md"
                    aria-label="submenu"
                    x-cloak
                >

                    <!-- Menu Items -->
                    <div class="py-2">
                        <li>
                            <a href="{{ route('profileGuru.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 transition-colors duration-150 group hover:bg-gray-50 hover:text-gray-900" role="menuitem">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 transition-colors duration-150 bg-blue-100 rounded-lg group-hover:bg-blue-200">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium">Profil Saya</p>
                                    <p class="text-xs text-gray-500">Kelola informasi profil</p>
                                </div>
                            </a>
                        </li>
                    </div>

                    <!-- Logout -->
                    <li class="flex flex-col">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="flex items-center w-full px-4 py-3 text-sm text-red-700 transition-colors duration-150 group hover:bg-red-50 hover:text-red-900"
                                role="menuitem"
                            >
                                <div class="flex items-center justify-center w-8 h-8 mr-3 transition-colors duration-150 bg-red-100 rounded-lg group-hover:bg-red-200">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium">Keluar</p>
                                    <p class="text-xs text-gray-500">Logout dari sistem</p>
                                </div>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
