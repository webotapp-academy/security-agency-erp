<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Guard;
use App\Models\Client;
use App\Models\Site;
use App\Models\Deployment;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Invoice;
use App\Models\Incident;
use App\Models\AuditLog;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        $today = Carbon::today();

        // 1. Guard stats
        $totalGuards = Guard::count();
        $activeGuards = Guard::where('status', 'active')->count();
        $deployedGuards = Deployment::where('status', 'active')->distinct('guard_id')->count('guard_id');
        $availableGuards = max(0, $activeGuards - $deployedGuards);

        // 2. Client & Site stats
        $totalClients = Client::where('status', 'active')->count();
        $totalSites = Site::where('status', 'active')->count();

        // 3. Today's attendance stats
        $todayPresent = Attendance::where('attendance_date', $today)
            ->whereIn('status', ['present', 'overtime'])
            ->count();
        $todayAbsent = Attendance::where('attendance_date', $today)
            ->where('status', 'absent')
            ->count();
        $todayLeave = Attendance::where('attendance_date', $today)
            ->where('status', 'leave')
            ->count();

        // 4. Financial stats
        $pendingPayrollCount = Payroll::where('status', 'pending')->count();
        $pendingPayrollAmount = (float) Payroll::where('status', 'pending')->sum('net_salary');
        
        $outstandingInvoicesCount = Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])->count();
        $outstandingInvoicesAmount = (float) Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])->sum('balance_amount');

        // 5. Expiring contracts (within 30 days)
        $expiringContracts = Client::where('status', 'active')
            ->whereNotNull('contract_end_date')
            ->where('contract_end_date', '>=', $today)
            ->where('contract_end_date', '<=', $today->copy()->addDays(45))
            ->orderBy('contract_end_date')
            ->get();

        // 6. Recent incidents
        $recentIncidents = Incident::with(['client', 'site'])
            ->orderBy('incident_datetime', 'desc')
            ->take(4)
            ->get();

        // 7. Recent activity logs
        $recentActivities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 8. Site deployment status list
        $sitesOverview = Site::with(['client', 'activeDeployments'])
            ->where('status', 'active')
            ->take(5)
            ->get();

        return view('livewire.dashboard', compact(
            'totalGuards',
            'activeGuards',
            'deployedGuards',
            'availableGuards',
            'totalClients',
            'totalSites',
            'todayPresent',
            'todayAbsent',
            'todayLeave',
            'pendingPayrollCount',
            'pendingPayrollAmount',
            'outstandingInvoicesCount',
            'outstandingInvoicesAmount',
            'expiringContracts',
            'recentIncidents',
            'recentActivities',
            'sitesOverview'
        ))->layout('layouts.app', ['title' => 'Operations Dashboard', 'subtitle' => 'Live Summary']);
    }
}
