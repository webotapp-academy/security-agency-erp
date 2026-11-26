<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - {{ \App\Models\Setting::get('agency_name', 'WeBotApp Security Agency Manager') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans text-slate-800 antialiased text-[13px] bg-slate-100 flex overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden" style="display: none;"></div>

    <!-- LEFT SIDEBAR -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-60 bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 ease-in-out lg:static lg:inset-auto lg:translate-x-0 flex-shrink-0">
        
        <!-- Sidebar Brand / Header -->
        <div class="h-13 px-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5 overflow-hidden">
                <div class="w-7 h-7 rounded bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="truncate">
                    <span class="font-bold text-xs text-slate-900 block leading-tight truncate">{{ \App\Models\Setting::get('software_logo_name', 'WeBotApp') }}</span>
                    <span class="text-[10px] text-slate-500 font-medium tracking-tight">Agency Manager</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- 16 Navigation Menu Items -->
        <nav class="flex-1 overflow-y-auto py-2.5 px-2.5 space-y-0.5 text-xs">
            @php
                $user = auth()->user();
                $currentRoute = request()->route()->getName();
                
                $navItems = [
                    ['route' => 'dashboard', 'label' => 'Dashboard', 'module' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'guards.index', 'label' => 'Guards', 'module' => 'guards', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['route' => 'clients.index', 'label' => 'Clients', 'module' => 'clients', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    ['route' => 'sites.index', 'label' => 'Sites', 'module' => 'sites', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'deployments.index', 'label' => 'Deployments', 'module' => 'deployments', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                    ['route' => 'roster.index', 'label' => 'Duty Roster', 'module' => 'duty_roster', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'attendance.index', 'label' => 'Attendance', 'module' => 'attendance', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                    ['route' => 'leaves.index', 'label' => 'Leave Management', 'module' => 'leaves', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['route' => 'payroll.index', 'label' => 'Payroll', 'module' => 'payroll', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['route' => 'billing.index', 'label' => 'Client Billing', 'module' => 'billing', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z'],
                    ['route' => 'equipment.index', 'label' => 'Equipment', 'module' => 'equipment', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['route' => 'incidents.index', 'label' => 'Incidents', 'module' => 'incidents', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    ['route' => 'expenses.index', 'label' => 'Expenses', 'module' => 'expenses', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['route' => 'reports.index', 'label' => 'Reports', 'module' => 'reports', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'users.index', 'label' => 'Users & Staff', 'module' => 'settings', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['route' => 'settings.index', 'label' => 'Settings', 'module' => 'settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ];
            @endphp

            @foreach ($navItems as $item)
                @if ($user && $user->canAccessModule($item['module']))
                    @php
                        $isActive = request()->routeIs($item['route']) || request()->routeIs(explode('.', $item['route'])[0] . '.*');
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center px-2.5 py-1.5 rounded transition font-medium {{ $isActive ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4 mr-2.5 flex-shrink-0 {{ $isActive ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <!-- Sidebar Footer / User Info -->
        <div class="p-3 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center space-x-2 truncate">
                <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs flex-shrink-0">
                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                </div>
                <div class="truncate text-left">
                    <p class="text-xs font-semibold text-slate-900 truncate leading-tight">{{ $user->name ?? 'User' }}</p>
                    <p class="text-[10px] text-slate-500 capitalize truncate">{{ str_replace('_', ' ', $user->role ?? 'Staff') }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Logout" class="p-1 text-slate-400 hover:text-rose-600 rounded hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- TOP APP BAR -->
        <header class="h-13 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 no-print">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-1 rounded text-slate-500 hover:text-slate-800 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center space-x-2">
                    <h2 class="text-xs font-bold text-slate-900 tracking-tight uppercase">{{ $title ?? 'Dashboard' }}</h2>
                    @if(isset($subtitle))
                        <span class="text-slate-300">/</span>
                        <span class="text-xs text-slate-500 font-medium">{{ $subtitle }}</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center space-x-3 text-xs">
                <!-- Today's Date Badge -->
                <div class="hidden sm:flex items-center space-x-1.5 px-2 py-1 bg-slate-50 border border-slate-200 rounded text-slate-600 font-medium">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ now()->format('D, d M Y') }}</span>
                </div>

                <!-- Role Pill -->
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 capitalize">
                    {{ str_replace('_', ' ', $user->role ?? 'User') }}
                </span>
            </div>
        </header>

        <!-- FLASH NOTIFICATIONS -->
        @if (session('success'))
            <div class="mx-4 sm:mx-6 mt-3 p-2.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded flex items-center justify-between no-print">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-4 sm:mx-6 mt-3 p-2.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded flex items-center justify-between no-print">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- PAGE BODY -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-100">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>
