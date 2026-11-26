<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DutyRoster;
use App\Models\Site;
use App\Models\Shift;
use App\Models\Guard;
use App\Models\AuditLog;
use Carbon\Carbon;

class DutyRosterManager extends Component
{
    public $selectedDate;
    public $selectedSiteId = '';
    public $viewMode = 'daily'; // daily, weekly

    // Schedule modal
    public $showScheduleModal = false;
    public $showAssignModal = false;
    public $roster_site_id = '';
    public $roster_guard_id = '';
    public $roster_shift_id = '';
    public $roster_date = '';
    public $replacement_guard_id = '';
    public $roster_status = 'scheduled';
    public $notes = '';

    // Shift creation modal
    public $showShiftModal = false;
    public $newShiftName = '';
    public $newShiftCode = '';
    public $newShiftStart = '08:00';
    public $newShiftEnd = '16:00';
    public $newShiftHours = 8.0;

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
        $this->roster_date = now()->toDateString();
        $firstSite = Site::where('status', 'active')->first();
        if ($firstSite) {
            $this->selectedSiteId = $firstSite->id;
            $this->roster_site_id = $firstSite->id;
        }
    }

    public function openScheduleModal()
    {
        $this->roster_site_id = $this->selectedSiteId ?: (Site::where('status', 'active')->value('id') ?? '');
        $this->roster_date = $this->selectedDate;
        $this->roster_guard_id = '';
        $this->roster_shift_id = Shift::first()->id ?? '';
        $this->replacement_guard_id = '';
        $this->roster_status = 'scheduled';
        $this->notes = '';
        $this->showScheduleModal = true;
    }

    public function saveSchedule()
    {
        $this->validate([
            'roster_site_id' => 'required|exists:sites,id',
            'roster_guard_id' => 'required|exists:guards,id',
            'roster_shift_id' => 'required|exists:shifts,id',
            'roster_date' => 'required|date',
        ]);

        DutyRoster::updateOrCreate(
            [
                'site_id' => $this->roster_site_id,
                'guard_id' => $this->roster_guard_id,
                'roster_date' => $this->roster_date,
                'shift_id' => $this->roster_shift_id,
            ],
            [
                'status' => $this->roster_status,
                'replacement_guard_id' => $this->replacement_guard_id ?: null,
                'notes' => $this->notes,
            ]
        );

        AuditLog::log('scheduled', 'duty_rosters', null, "Scheduled guard in duty roster for date {$this->roster_date}");
        session()->flash('success', "Duty roster updated successfully.");

        $this->showScheduleModal = false;
    }

    public function openShiftModal()
    {
        $this->newShiftName = '';
        $this->newShiftCode = '';
        $this->newShiftStart = '08:00';
        $this->newShiftEnd = '16:00';
        $this->newShiftHours = 8.0;
        $this->showShiftModal = true;
    }

    public function saveShift()
    {
        $this->validate([
            'newShiftName' => 'required|string',
            'newShiftCode' => 'required|string|unique:shifts,code',
            'newShiftStart' => 'required',
            'newShiftEnd' => 'required',
            'newShiftHours' => 'required|numeric',
        ]);

        Shift::create([
            'name' => $this->newShiftName,
            'code' => strtoupper($this->newShiftCode),
            'start_time' => $this->newShiftStart,
            'end_time' => $this->newShiftEnd,
            'duration_hours' => $this->newShiftHours,
            'is_active' => true,
        ]);

        session()->flash('success', "New shift '{$this->newShiftName}' created.");
        $this->showShiftModal = false;
    }

    public function changeStatus($rosterId, $newStatus)
    {
        $roster = DutyRoster::findOrFail($rosterId);
        $roster->update(['status' => $newStatus]);
        session()->flash('success', "Roster status updated to {$newStatus}.");
    }

    public function deleteRoster($rosterId)
    {
        $roster = DutyRoster::findOrFail($rosterId);
        $roster->delete();
        session()->flash('success', "Roster entry removed.");
    }

    public function render()
    {
        $sites = Site::where('status', 'active')->orderBy('site_name')->get();
        $shifts = Shift::where('is_active', true)->get();
        $guards = Guard::where('status', 'active')->orderBy('full_name')->get();

        $dateCarbon = Carbon::parse($this->selectedDate);

        // Daily Query
        $dailyRosters = DutyRoster::with(['securityGuard', 'site', 'shift', 'replacementGuard'])
            ->where('roster_date', $this->selectedDate)
            ->when($this->selectedSiteId, function($q) {
                $q->where('site_id', $this->selectedSiteId);
            })
            ->get();

        // Weekly dates array
        $startOfWeek = $dateCarbon->copy()->startOfWeek();
        $weekDates = [];
        for ($i = 0; $i < 7; $i++) {
            $weekDates[] = $startOfWeek->copy()->addDays($i);
        }

        $weeklyRosters = DutyRoster::with(['securityGuard', 'shift', 'site'])
            ->whereBetween('roster_date', [$startOfWeek->toDateString(), $startOfWeek->copy()->endOfWeek()->toDateString()])
            ->when($this->selectedSiteId, function($q) {
                $q->where('site_id', $this->selectedSiteId);
            })
            ->get();

        return view('livewire.duty-roster-manager', [
            'sites' => $sites,
            'shifts' => $shifts,
            'guards' => $guards,
            'dailyRosters' => $dailyRosters,
            'dailySlots' => $dailyRosters,
            'weekDates' => $weekDates,
            'weeklyRosters' => $weeklyRosters,
        ])->layout('layouts.app', ['title' => 'Duty Roster & Shifts', 'subtitle' => 'Shift Schedules & Roster Print']);
    }
}
