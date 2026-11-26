<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Livewire\Dashboard;
use App\Livewire\GuardsManager;
use App\Livewire\ClientsManager;
use App\Livewire\SitesManager;
use App\Livewire\DeploymentsManager;
use App\Livewire\DutyRosterManager;
use App\Livewire\AttendanceManager;
use App\Livewire\LeaveManager;
use App\Livewire\PayrollManager;
use App\Livewire\ClientBillingManager;
use App\Livewire\EquipmentManager;
use App\Livewire\IncidentManager;
use App\Livewire\ExpenseManager;
use App\Livewire\ReportsManager;
use App\Livewire\UsersManager;
use App\Livewire\SettingsManager;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/guards', GuardsManager::class)->name('guards.index');
    Route::get('/clients', ClientsManager::class)->name('clients.index');
    Route::get('/sites', SitesManager::class)->name('sites.index');
    Route::get('/deployments', DeploymentsManager::class)->name('deployments.index');
    Route::get('/duty-roster', DutyRosterManager::class)->name('roster.index');
    Route::get('/attendance', AttendanceManager::class)->name('attendance.index');
    Route::get('/leaves', LeaveManager::class)->name('leaves.index');
    Route::get('/payroll', PayrollManager::class)->name('payroll.index');
    Route::get('/billing', ClientBillingManager::class)->name('billing.index');
    Route::get('/equipment', EquipmentManager::class)->name('equipment.index');
    Route::get('/incidents', IncidentManager::class)->name('incidents.index');
    Route::get('/expenses', ExpenseManager::class)->name('expenses.index');
    Route::get('/reports', ReportsManager::class)->name('reports.index');
    Route::get('/users', UsersManager::class)->name('users.index');
    Route::get('/settings', SettingsManager::class)->name('settings.index');
});
