<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Deployment;
use App\Models\Guard;
use App\Models\Site;
use App\Models\Shift;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class DeploymentsManager extends Component
{
    use WithPagination;

    public $search = '';
    public $siteFilter = '';
    public $statusFilter = 'active';

    // Modals
    public $showAssignModal = false;
    public $showTransferModal = false;
    public $showRelieveModal = false;

    // Assignment Form
    public $guard_id = '';
    public $site_id = '';
    public $shift_id = '';
    public $designation = 'Security Guard';
    public $start_date = '';
    public $duty_instructions = '';
    public $notes = '';

    // Transfer / Relieve Form
    public $selectedDeploymentId = null;
    public $selectedDeployment = null;
    public $transfer_site_id = '';
    public $transfer_shift_id = '';
    public $transfer_date = '';
    public $relieve_date = '';
    public $relieve_reason = 'Completed rotation period';

    protected function rules()
    {
        return [
            'guard_id' => 'required|exists:guards,id',
            'site_id' => 'required|exists:sites,id',
            'designation' => 'required|string',
            'start_date' => 'required|date',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openAssignModal()
    {
        $this->resetValidation();
        $this->guard_id = '';
        $this->site_id = '';
        $this->shift_id = '';
        $this->designation = 'Security Guard';
        $this->start_date = now()->toDateString();
        $this->duty_instructions = '';
        $this->notes = '';

        $firstSite = Site::where('status', 'active')->first();
        if ($firstSite) {
            $this->site_id = $firstSite->id;
        }

        $firstShift = Shift::first();
        if ($firstShift) {
            $this->shift_id = $firstShift->id;
        }

        $this->showAssignModal = true;
    }

    public function openTransferModal($id)
    {
        $this->selectedDeploymentId = $id;
        $this->selectedDeployment = Deployment::with(['securityGuard', 'site', 'shift'])->findOrFail($id);
        $this->transfer_site_id = '';
        $this->transfer_shift_id = $this->selectedDeployment->shift_id;
        $this->transfer_date = now()->toDateString();
        $this->showTransferModal = true;
    }

    public function openRelieveModal($id)
    {
        $this->selectedDeploymentId = $id;
        $this->selectedDeployment = Deployment::with(['securityGuard', 'site'])->findOrFail($id);
        $this->relieve_date = now()->toDateString();
        $this->relieve_reason = 'Routine rotation';
        $this->showRelieveModal = true;
    }

    public function assignGuard()
    {
        $this->validate();

        // Enforce rule: PREVENT OVERLAPPING ACTIVE ASSIGNMENTS
        $existing = Deployment::where('guard_id', $this->guard_id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            $guard = Guard::find($this->guard_id);
            $site = Site::find($existing->site_id);
            $this->addError('guard_id', "Guard {$guard->full_name} is already actively deployed at {$site->site_name}. Relieve or transfer first.");
            return;
        }

        $deployment = Deployment::create([
            'guard_id' => $this->guard_id,
            'site_id' => $this->site_id,
            'shift_id' => $this->shift_id ?: null,
            'designation' => $this->designation,
            'start_date' => $this->start_date,
            'duty_instructions' => $this->duty_instructions,
            'status' => 'active',
            'notes' => $this->notes,
        ]);

        $guard = Guard::find($this->guard_id);
        $site = Site::find($this->site_id);
        AuditLog::log('deployed', 'deployments', $deployment->id, "Deployed guard {$guard->full_name} to {$site->site_name}");

        session()->flash('success', "Guard {$guard->full_name} successfully deployed to {$site->site_name}.");
        $this->showAssignModal = false;
    }

    public function transferGuard()
    {
        $this->validate([
            'transfer_site_id' => 'required|exists:sites,id|different:selectedDeployment.site_id',
            'transfer_date' => 'required|date',
        ]);

        DB::transaction(function() {
            $dep = Deployment::findOrFail($this->selectedDeploymentId);
            $guard = $dep->securityGuard;
            $oldSite = $dep->site;
            $newSite = Site::findOrFail($this->transfer_site_id);

            // Relieve from current site
            $dep->update([
                'status' => 'transferred',
                'end_date' => $this->transfer_date,
                'notes' => $dep->notes . " [Transferred to {$newSite->site_name} on {$this->transfer_date}]",
            ]);

            // Create new active deployment
            Deployment::create([
                'guard_id' => $guard->id,
                'site_id' => $newSite->id,
                'shift_id' => $this->transfer_shift_id ?: null,
                'designation' => $dep->designation,
                'start_date' => $this->transfer_date,
                'status' => 'active',
                'notes' => "Transferred from {$oldSite->site_name}",
            ]);

            AuditLog::log('transferred', 'deployments', $dep->id, "Transferred {$guard->full_name} from {$oldSite->site_name} to {$newSite->site_name}");
        });

        session()->flash('success', "Guard transferred successfully.");
        $this->showTransferModal = false;
    }

    public function relieveGuard()
    {
        $this->validate([
            'relieve_date' => 'required|date',
            'relieve_reason' => 'required|string',
        ]);

        $dep = Deployment::findOrFail($this->selectedDeploymentId);
        $dep->relieve($this->relieve_date, $this->relieve_reason);

        AuditLog::log('relieved', 'deployments', $dep->id, "Relieved guard {$dep->securityGuard->full_name} from {$dep->site->site_name}");

        session()->flash('success', "Guard relieved from deployment.");
        $this->showRelieveModal = false;
    }

    public function render()
    {
        $query = Deployment::with(['securityGuard', 'site.client', 'shift']);

        if ($this->search) {
            $query->whereHas('securityGuard', function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('guard_code', 'like', '%' . $this->search . '%');
            })->orWhereHas('site', function($q) {
                $q->where('site_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->siteFilter) {
            $query->where('site_id', $this->siteFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $deployments = $query->orderBy('status')->orderBy('start_date', 'desc')->paginate(12);

        // Available guards for deployment (active guards without current active deployment)
        $activeDeployedGuardIds = Deployment::where('status', 'active')->pluck('guard_id')->toArray();
        $availableGuards = Guard::where('status', 'active')
            ->whereNotIn('id', $activeDeployedGuardIds)
            ->orderBy('full_name')
            ->get();

        $sites = Site::where('status', 'active')->orderBy('site_name')->get();
        $shifts = Shift::where('is_active', true)->get();

        return view('livewire.deployments-manager', [
            'deployments' => $deployments,
            'availableGuards' => $availableGuards,
            'sites' => $sites,
            'shifts' => $shifts,
        ])->layout('layouts.app', ['title' => 'Guard Deployment & Postings', 'subtitle' => 'Site Assignments & Transfers']);
    }
}
