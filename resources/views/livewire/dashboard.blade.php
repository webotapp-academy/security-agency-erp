<div class="space-y-4">

    <!-- METRICS GRID: COMPACT CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Card 1: Registered Guards -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Total Guards</span>
                <span class="p-1 rounded bg-slate-100 text-slate-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
            </div>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-lg font-bold text-slate-900 leading-tight">{{ $totalGuards }}</span>
                <span class="text-[11px] font-semibold text-emerald-600">{{ $activeGuards }} Active</span>
            </div>
        </div>

        <!-- Card 2: Deployed vs Available -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Deployed</span>
                <span class="p-1 rounded bg-blue-50 text-blue-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </span>
            </div>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-lg font-bold text-blue-700 leading-tight">{{ $deployedGuards }}</span>
                <span class="text-[11px] font-semibold text-slate-600">{{ $availableGuards }} Avail</span>
            </div>
        </div>

        <!-- Card 3: Active Sites -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Clients / Sites</span>
                <span class="p-1 rounded bg-slate-100 text-slate-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                </span>
            </div>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-lg font-bold text-slate-900 leading-tight">{{ $totalSites }} <span class="text-xs font-normal text-slate-500">sites</span></span>
                <span class="text-[11px] font-medium text-slate-600">{{ $totalClients }} Clients</span>
            </div>
        </div>

        <!-- Card 4: Today Attendance -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Today Present</span>
                <span class="p-1 rounded bg-emerald-50 text-emerald-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-lg font-bold text-emerald-700 leading-tight">{{ $todayPresent }}</span>
                <span class="text-[11px] text-slate-500"><span class="text-rose-600 font-semibold">{{ $todayAbsent }}</span> Abs / <span class="text-amber-600 font-semibold">{{ $todayLeave }}</span> Lv</span>
            </div>
        </div>

        <!-- Card 5: Pending Salaries -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Pending Salary</span>
                <span class="p-1 rounded bg-amber-50 text-amber-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-lg font-bold text-slate-900 leading-tight">₹{{ number_format($pendingPayrollAmount) }}</span>
                <span class="text-[11px] font-medium text-amber-700">{{ $pendingPayrollCount }} Guards</span>
            </div>
        </div>

        <!-- Card 6: Outstanding Invoices -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Client Dues</span>
                <span class="p-1 rounded bg-rose-50 text-rose-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                </span>
            </div>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-lg font-bold text-rose-700 leading-tight">₹{{ number_format($outstandingInvoicesAmount) }}</span>
                <span class="text-[11px] font-medium text-slate-600">{{ $outstandingInvoicesCount }} Invoices</span>
            </div>
        </div>
    </div>

    <!-- MIDDLE SECTION: SITE DEPLOYMENTS & QUICK SHORTCUTS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        <!-- Left 2 Cols: Deployment Sites Status Overview -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
            <div class="px-4 py-2.5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-xs text-slate-900">Site Deployments & Guard Manning</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-200 text-slate-700 font-semibold">{{ $totalSites }} Active</span>
                </div>
                <a href="{{ route('deployments.index') }}" class="text-xs text-slate-600 hover:text-slate-900 font-semibold hover:underline">Manage Deployments &rarr;</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left compact-table border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-600">
                            <th>Site Name</th>
                            <th>Client</th>
                            <th>Required</th>
                            <th>Deployed</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sitesOverview as $st)
                            @php
                                $shortage = max(0, $st->required_guards - $st->activeDeployments->count());
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="font-medium text-slate-900">
                                    <div class="leading-tight">{{ $st->site_name }}</div>
                                    <span class="text-[10px] text-slate-400 font-normal">{{ $st->site_code }} &bull; {{ $st->site_type }}</span>
                                </td>
                                <td class="text-slate-600 text-xs">{{ $st->client->client_name ?? '—' }}</td>
                                <td class="font-semibold text-slate-700 text-xs">{{ $st->required_guards }}</td>
                                <td class="font-bold text-slate-900 text-xs">{{ $st->activeDeployments->count() }}</td>
                                <td>
                                    @if ($shortage == 0)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Fully Manned</span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">-{{ $shortage }} Shortage</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('deployments.index') }}" class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700">Assign</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Col: Upcoming Contract Renewals & Quick Links -->
        <div class="space-y-4">
            
            <!-- Contract Renewal Alerts -->
            <div class="bg-white border border-slate-200 rounded shadow-xs p-3.5">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="font-bold text-xs text-slate-900 flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Contract Renewal Alerts</span>
                    </span>
                    <span class="text-[10px] text-slate-500">&le; 45 Days</span>
                </div>

                <div class="mt-2.5 space-y-2">
                    @forelse ($expiringContracts as $expClient)
                        @php
                            $daysRemaining = (int) now()->diffInDays($expClient->contract_end_date, false);
                        @endphp
                        <div class="p-2 rounded border border-amber-200 bg-amber-50/50 flex items-center justify-between">
                            <div class="truncate pr-2">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ $expClient->client_name }}</p>
                                <p class="text-[10px] text-slate-500">Expires: {{ $expClient->contract_end_date->format('d M Y') }}</p>
                            </div>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 flex-shrink-0">
                                {{ $daysRemaining }} days
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 py-2 text-center">No contracts expiring in the next 45 days.</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Action Shortcuts -->
            <div class="bg-white border border-slate-200 rounded shadow-xs p-3.5">
                <span class="font-bold text-xs text-slate-900 block pb-2 border-b border-slate-100">Agency Quick Shortcuts</span>
                <div class="mt-2.5 grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('guards.index') }}" class="p-2 rounded border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>Add Guard</span>
                    </a>
                    <a href="{{ route('attendance.index') }}" class="p-2 rounded border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Mark Attendance</span>
                    </a>
                    <a href="{{ route('billing.index') }}" class="p-2 rounded border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Create Invoice</span>
                    </a>
                    <a href="{{ route('payroll.index') }}" class="p-2 rounded border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Run Payroll</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM SECTION: RECENT INCIDENTS & RECENT AUDIT ACTIVITIES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        
        <!-- Recent Incidents -->
        <div class="bg-white border border-slate-200 rounded shadow-xs p-3.5">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <span class="font-bold text-xs text-slate-900 flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Recent Security Incidents</span>
                </span>
                <a href="{{ route('incidents.index') }}" class="text-xs text-slate-600 hover:text-slate-900 font-semibold hover:underline">View All &rarr;</a>
            </div>

            <div class="mt-2.5 divide-y divide-slate-100">
                @forelse ($recentIncidents as $inc)
                    <div class="py-2 flex items-start justify-between">
                        <div class="pr-3">
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-xs text-slate-900 capitalize">{{ str_replace('_', ' ', $inc->category) }}</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $inc->incident_code }}</span>
                            </div>
                            <p class="text-xs text-slate-600 line-clamp-1 mt-0.5">{{ $inc->description }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $inc->site->site_name ?? 'General' }} &bull; {{ $inc->incident_datetime->format('d M, h:i A') }}</p>
                        </div>
                        <div>
                            @if ($inc->resolution_status === 'resolved' || $inc->resolution_status === 'closed')
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 capitalize">{{ $inc->resolution_status }}</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 capitalize">{{ $inc->resolution_status }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-4 text-center">No recent security incidents recorded.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity Audit Log -->
        <div class="bg-white border border-slate-200 rounded shadow-xs p-3.5">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <span class="font-bold text-xs text-slate-900 flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Recent System Activity</span>
                </span>
                <span class="text-[10px] text-slate-400">Audit Trail</span>
            </div>

            <div class="mt-2.5 divide-y divide-slate-100">
                @foreach ($recentActivities as $act)
                    <div class="py-1.5 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2 truncate pr-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 flex-shrink-0"></span>
                            <span class="text-slate-700 truncate">{{ $act->details ?? $act->action }}</span>
                        </div>
                        <span class="text-[10px] text-slate-400 flex-shrink-0 font-medium">{{ $act->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
