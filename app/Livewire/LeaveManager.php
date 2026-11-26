<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Leave;
use App\Models\Guard;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    // Modal state
    public $showApplyModal = false;
    public $showActionModal = false;
    public $selectedLeaveId = null;
    public $rejectionReason = '';

    // Form fields
    public $guard_id = '';
    public $leave_type = 'casual';
    public $start_date = '';
    public $end_date = '';
    public $reason = '';

    protected function rules()
    {
        return [
            'guard_id' => 'required|exists:guards,id',
            'leave_type' => 'required|in:casual,sick,earned,unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:250',
        ];
    }

    public function mount()
    {
        $this->start_date = now()->toDateString();
        $this->end_date = now()->addDays(2)->toDateString();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openApplyModal()
    {
        $this->resetValidation();
        $this->guard_id = '';
        $this->leave_type = 'casual';
        $this->start_date = now()->toDateString();
        $this->end_date = now()->addDays(2)->toDateString();
        $this->reason = '';
        $this->showApplyModal = true;
    }

    public function applyLeave()
    {
        $this->validate();

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $days = $start->diffInDays($end) + 1;

        $leave = Leave::create([
            'guard_id' => $this->guard_id,
            'leave_type' => $this->leave_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'days_count' => $days,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        $guard = Guard::find($this->guard_id);
        AuditLog::log('leave_applied', 'leaves', $leave->id, "Applied {$this->leave_type} leave ({$days} days) for guard {$guard->full_name}");

        session()->flash('success', "Leave application submitted for {$guard->full_name}.");
        $this->showApplyModal = false;
    }

    public function approveLeave($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->update([
            'status' => 'approved',
            'approved_by_id' => Auth::id(),
        ]);

        AuditLog::log('leave_approved', 'leaves', $leave->id, "Approved leave for guard {$leave->securityGuard->full_name}");
        session()->flash('success', "Leave approved successfully.");
    }

    public function openRejectModal($id)
    {
        $this->selectedLeaveId = $id;
        $this->rejectionReason = '';
        $this->showActionModal = true;
    }

    public function rejectLeave()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:4',
        ]);

        $leave = Leave::findOrFail($this->selectedLeaveId);
        $leave->update([
            'status' => 'rejected',
            'approved_by_id' => Auth::id(),
            'rejection_reason' => $this->rejectionReason,
        ]);

        AuditLog::log('leave_rejected', 'leaves', $leave->id, "Rejected leave for guard {$leave->securityGuard->full_name}. Reason: {$this->rejectionReason}");
        session()->flash('success', "Leave request rejected.");
        $this->showActionModal = false;
    }

    public function render()
    {
        $guards = Guard::where('status', 'active')->orderBy('full_name')->get();

        $query = Leave::with(['securityGuard', 'approvedBy']);

        if ($this->search) {
            $query->whereHas('securityGuard', function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('guard_code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $leaves = $query->orderBy('start_date', 'desc')->paginate(12);

        return view('livewire.leave-manager', [
            'leaves' => $leaves,
            'guards' => $guards,
        ])->layout('layouts.app', ['title' => 'Leave Management', 'subtitle' => 'Guard Time-off Requests & Approvals']);
    }
}
