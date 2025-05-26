<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <!-- @livewireStyles -->
</head>

<body class="font-sans antialiased">


    <div class="min-h-screen bg-gray-100">
        {{-- Navigation Menu từ Breeze/Jetstream --}}
        <!-- @livewire('navigation-menu') -->


        <!-- Page Content -->
        <main class="flex">
            {{-- Admin Sidebar --}}
            <aside class="w-64 bg-white shadow-md hidden md:block flex-shrink-0">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Quản lý</h3>
                    <nav class="space-y-2">
                        {{-- Link Dashboard (nếu có view riêng) --}}
                        {{-- <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">📊</span> Dashboard
                        </a> --}}
                        <a href="{{ route('admin.employees.index') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('admin.employees.*') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">👥</span> Nhân sự
                        </a>
                        <a href="{{ route('admin.positions.index') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('admin.positions.*') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">🏷️</span> Chức vụ
                        </a>
                        <a href="{{ route('admin.departments.index') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('admin.departments.*') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">🏢</span> Phòng ban
                        </a>
                        <a href="{{ route('admin.work-schedules.index') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('admin.work-schedules.*') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">⏰</span> Ca làm việc
                        </a>
                        <a href="{{ route('admin.attendances.index') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('admin.attendances.*') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">✅</span> Chấm công
                        </a>
                        <a href="{{ route('admin.salaries.index') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('admin.salaries.*') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">💰</span> Quản lý lương
                        </a>
                        <a href="{{ route('admin.rewards-discipline.index') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('admin.rewards-discipline.*') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">🏆</span> Khen thưởng/Kỷ luật
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">👤</span> Tài khoản
                        </a>
                        <a href="{{ route('admin.reports.index') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('admin.reports.*') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">📈</span> Thống kê
                        </a>
                        <a href="{{ route('logout') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="mr-2">🚪</span> Đăng xuất
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                        {{-- Thêm các link khác --}}
                    </nav>
                </div>
            </aside>

            {{-- Main Content Area --}}
            <div class="flex-1">
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                {{-- Content từ các view con --}}
                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('modals')
    <!-- @livewireScripts -->
    {{-- Thêm script riêng nếu cần --}}
    @stack('scripts')
</body>

</html>