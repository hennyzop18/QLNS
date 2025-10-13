<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- Head giống admin layout --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Employee</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">


    <div class="min-h-screen bg-gray-100">
        <!-- @livewire('navigation-menu') -->

        <!-- Page Content -->
        <main class="flex">
            {{-- Employee Sidebar --}}
            <aside class="w-64 bg-white shadow-md hidden md:block flex-shrink-0">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Nhân Viên</h3>
                    <nav class="space-y-2">
                        {{-- Link Dashboard (nếu có) --}}
                        {{-- <a href="{{ route('employee.dashboard') }}" ...></a> --}}
                        {{-- <a href="{{ route('employee.attendance.create') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('employee.attendance.create') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">✅</span> Chấm công
                        </a> --}}
                        <a href="{{ route('employee.attendance.history') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('employee.attendance.history') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">📜</span> Lịch sử chấm công
                        </a>
                        <a href="{{ route('employee.profile.edit') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('employee.profile.edit') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">👤</span> Thông tin cá nhân
                        </a>
                        <a href="{{ route('employee.schedule.index') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('employee.schedule.*') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">🗓️</span> Đăng ký lịch
                        </a>
                        <a href="{{ route('employee.face.register.form') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md {{ request()->routeIs('employee.profile.edit') ? 'bg-gray-200 font-semibold' : '' }}">
                            <span class="mr-2">👤</span> Đăng ký FaceID
                        </a>
                        <a href="{{ route('logout') }}"
                            class="flex items-center px-3 py-2 text-gray-600 hover:bg-gray-200 hover:text-gray-700 rounded-md"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="mr-2">🚪</span> Đăng xuất
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                        {{-- Thêm link xem bảng lương cá nhân (nếu có) --}}
                        {{-- <a href="{{ route('employee.salaries.index') }}" ...>💰 Bảng lương</a> --}}
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

                {{ $slot }}
            </div>

        </main>
    </div>
    @stack('modals')
    <!-- @livewireScripts -->
    @stack('scripts')
</body>

</html>