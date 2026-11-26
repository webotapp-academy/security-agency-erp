<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Incident;
use App\Models\Client;
use App\Models\Site;
use App\Models\Guard;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IncidentManager extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';

    // Modal state
    public $showCreateModal = false;
    public $showDetailModal = false;
    public $selectedIncident = null;

    // Form fields
    public $incident_code = '';
    public $client_id = '';
    public $site_id = '';
    public $incident_datetime = '';
    public $reported_by = '';
    public $category = 'theft';
    public $description = '';
    public $action_taken = '';
    public $involved_guards = '';
    public $resolution_status = 'open';
    public $confidential = false;

    // Quick status update in detail modal
    public $update_status = '';
    public $update_action_taken = '';

    public function mount()
    {
        $this->incident_datetime = now()->format('Y-m-d\TH:i');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $count = Incident::count() + 1;
        $this->incident_code = sprintf('INC-%s-%03d', date('Y'), $count);
        $this->incident_datetime = now()->format('Y-m-d\TH:i');
        $this->reported_by = Auth::user()->name;
        $this->category = 'theft';
        $this->description = '';
        $this->action_taken = '';
        $this->involved_guards = '';
        $this->resolution_status = 'open';
        $this->confidential = false;

        $firstSite = Site::where('status', 'active')->first();
        if ($firstSite) {
            $this->site_id = $firstSite->id;
            $this->client_id = $firstSite->client_id;
        }

        $this->showCreateModal = true;
    }

    public function updatedSiteId()
    {
        $site = Site::find($this->site_id);
        if ($site) {
            $this->client_id = $site->client_id;
        }
    }

    public function saveIncident()
    {
        $this->validate([
            'site_id' => 'required|exists:sites,id',
            'incident_datetime' => 'required',
            'reported_by' => 'required|string|max:100',
            'category' => 'required',
            'description' => 'required|string|min:10',
            'resolution_status' => 'required|in:open,investigating,resolved,closed',
        ]);

        $resolvedAt = in_array($this->resolution_status, ['resolved', 'closed']) ? now() : null;

        $incident = Incident::create([
            'incident_code' => $this->incident_code,
            'client_id' => $this->client_id ?: null,
            'site_id' => $this->site_id,
            'incident_datetime' => $this->incident_datetime,
            'reported_by' => $this->reported_by,
            'category' => $this->category,
            'description' => $this->description,
            'action_taken' => $this->action_taken,
            'involved_guards' => $this->involved_guards,
            'resolution_status' => $this->resolution_status,
            'confidential' => $this->confidential,
            'resolved_at' => $resolvedAt,
        ]);

        AuditLog::log('incident_logged', 'incidents', $incident->id, "Logged security incident {$incident->incident_code} ({$incident->category}) at {$incident->site->site_name}");
        session()->flash('success', "Security incident {$incident->incident_code} logged successfully.");

        $this->showCreateModal = false;
    }

    public function viewDetails($id)
    {
        $this->selectedIncident = Incident::with(['client', 'site'])->findOrFail($id);
        $this->update_status = $this->selectedIncident->resolution_status;
        $this->update_action_taken = $this->selectedIncident->action_taken ?? '';
        $this->showDetailModal = true;
    }

    public function updateResolution()
    {
        if (!$this->selectedIncident) return;

        $resolvedAt = in_array($this->update_status, ['resolved', 'closed']) ? now() : $this->selectedIncident->resolved_at;

        $this->selectedIncident->update([
            'resolution_status' => $this->update_status,
            'action_taken' => $this->update_action_taken,
            'resolved_at' => $resolvedAt,
        ]);

        AuditLog::log('incident_updated', 'incidents', $this->selectedIncident->id, "Updated incident {$this->selectedIncident->incident_code} resolution status to {$this->update_status}");
        session()->flash('success', "Incident resolution updated.");

        $this->showDetailModal = false;
    }

    public function render()
    {
        $query = Incident::with(['client', 'site']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('incident_code', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('reported_by', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->statusFilter) {
            $query->where('resolution_status', $this->statusFilter);
        }

        $incidents = $query->orderBy('incident_datetime', 'desc')->paginate(10);
        $sites = Site::where('status', 'active')->orderBy('site_name')->get();

        return view('livewire.incident-manager', [
            'incidents' => $incidents,
            'sites' => $sites,
        ])->layout('layouts.app', ['title' => 'Incidents & Complaints', 'subtitle' => 'Security Log, Breach Investigation & Resolutions']);
    }
}
