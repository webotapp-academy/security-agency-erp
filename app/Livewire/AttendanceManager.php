<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\AttendanceAudit;
use App\Models\Guard;
use App\Models\Site;
use App\Models\Shift;
use App\Models\Deployment;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceManager extends Component
{
    public $selectedDate;
    public $selectedSiteId = '';

    // Quick mark modal / edit single entry
    public $showEditModal = false;
    public $attendanceId = null;
    public $guard_name = '';
    public $status = 'present';
    public $check_in_time = '06:00';
    public $check_out_time = '14:00';
    public $late_minutes = 0;
    public $overtime_hours = 0.0;
    public $remarks = '';
    public $audit_reason = '';

    // Bulk action state
    public $bulkStatus = 'present';

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
        $firstSite = Site::where('status', 'active')->first();
        if ($firstSite) {
            $this->selectedSiteId = $firstSite->id;
        }
    }

    public function populateDeployedGuards()
    {
        // For all active deployments at selected site (or all sites), ensure attendance record exists for the date
        $query = Deployment::where('status', 'active');
        if ($this->selectedSiteId) {
            $query->where('site_id', $this->selectedSiteId);
        }

        $deployments = $query->get();
        $count = 0;

        foreach ($deployments as $dep) {
            $existing = Attendance::where('guard_id', $dep->guard_id)
                ->where('attendance_date', $this->selectedDate)
                ->first();

            if (!$existing) {
                Attendance::create([
                    'guard_id' => $dep->guard_id,
                    'site_id' => $dep->site_id,
                    'shift_id' => $dep->shift_id,
                    'attendance_date' => $this->selectedDate,
                    'status' => 'present',
                    'check_in_time' => $dep->shift->start_time ?? '06:00:00',
                    'check_out_time' => $dep->shift->end_time ?? '14:00:00',
                    'late_minutes' => 0,
                    'overtime_hours' => 0.00,
                    'verified_by_id' => Auth::id(),
                    'remarks' => 'Auto-populated from active deployment',
                ]);
                $count++;
            }
        }

        AuditLog::log('attendance_populated', 'attendances', null, "Populated {$count} attendance records for date {$this->selectedDate}");
        session()->flash('success', "Generated attendance records for {$count} deployed guards.");
    }

    public function quickSetStatus($attId, $newStatus)
    {
        $att = Attendance::findOrFail($attId);
        $oldStatus = $att->status;

        if ($oldStatus !== $newStatus) {
            $att->update([
                'status' => $newStatus,
                'verified_by_id' => Auth::id(),
            ]);

            AttendanceAudit::create([
                'attendance_id' => $att->id,
                'changed_by_id' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => 'Quick status update from daily attendance sheet',
            ]);
        }

        session()->flash('success', "Guard status changed to {$newStatus}.");
    }

    public function openEditModal($id)
    {
        $att = Attendance::with('securityGuard')->findOrFail($id);
        $this->attendanceId = $att->id;
        $this->guard_name = $att->securityGuard->full_name ?? 'Guard';
        $this->status = $att->status;
        $this->check_in_time = $att->check_in_time ? substr($att->check_in_time, 0, 5) : '06:00';
        $this->check_out_time = $att->check_out_time ? substr($att->check_out_time, 0, 5) : '14:00';
        $this->late_minutes = $att->late_minutes;
        $this->overtime_hours = (float) $att->overtime_hours;
        $this->remarks = $att->remarks;
        $this->audit_reason = '';

        $this->showEditModal = true;
    }

    public function saveEdit()
    {
        $this->validate([
            'status' => 'required|in:present,absent,half_day,weekly_off,leave,holiday,overtime',
            'audit_reason' => 'required|string|min:3',
        ]);

        $att = Attendance::findOrFail($this->attendanceId);
        $oldStatus = $att->status;

        $att->update([
            'status' => $this->status,
            'check_in_time' => $this->check_in_time ? $this->check_in_time . ':00' : null,
            'check_out_time' => $this->check_out_time ? $this->check_out_time . ':00' : null,
            'late_minutes' => $this->late_minutes,
            'overtime_hours' => $this->overtime_hours,
            'remarks' => $this->remarks,
            'verified_by_id' => Auth::id(),
        ]);

        AttendanceAudit::create([
            'attendance_id' => $att->id,
            'changed_by_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => $this->status,
            'reason' => $this->audit_reason,
        ]);

        AuditLog::log('attendance_corrected', 'attendances', $att->id, "Corrected attendance for {$this->guard_name} to {$this->status}: {$this->audit_reason}");
        session()->flash('success', "Attendance updated with audit log.");

        $this->showEditModal = false;
    }

    public function bulkMarkAll()
    {
        $query = Attendance::where('attendance_date', $this->selectedDate);
        if ($this->selectedSiteId) {
            $query->where('site_id', $this->selectedSiteId);
        }

        $records = $query->get();
        foreach ($records as $rec) {
            if ($rec->status !== $this->bulkStatus) {
                $old = $rec->status;
                $rec->update([
                    'status' => $this->bulkStatus,
                    'verified_by_id' => Auth::id(),
                ]);

                AttendanceAudit::create([
                    'attendance_id' => $rec->id,
                    'changed_by_id' => Auth::id(),
                    'old_status' => $old,
                    'new_status' => $this->bulkStatus,
                    'reason' => 'Bulk attendance marking action',
                ]);
            }
        }

        session()->flash('success', "Marked all filtered records as {$this->bulkStatus}.");
    }

    public function render()
    {
        $sites = Site::where('status', 'active')->orderBy('site_name')->get();

        $query = Attendance::with(['securityGuard', 'site', 'shift', 'verifiedBy', 'audits.changedBy'])
            ->where('attendance_date', $this->selectedDate);

        if ($this->selectedSiteId) {
            $query->where('site_id', $this->selectedSiteId);
        }

        $attendances = $query->get();

        // Summary counts for this date
        $summary = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
            'leave' => $attendances->where('status', 'leave')->count(),
            'overtime' => $attendances->where('status', 'overtime')->count(),
            'weekly_off' => $attendances->where('status', 'weekly_off')->count(),
        ];

        return view('livewire.attendance-manager', [
            'sites' => $sites,
            'attendances' => $attendances,
            'summary' => $summary,
        ])->layout('layouts.app', ['title' => 'Guard Attendance', 'subtitle' => 'Daily Attendance Sheet & Audit Log']);
    }
}
