<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Site;
use App\Models\Client;
use App\Models\User;
use App\Models\AuditLog;

class SitesManager extends Component
{
    use WithPagination;

    public $search = '';
    public $clientFilter = '';
    public $typeFilter = '';

    // Modal state
    public $showCreateModal = false;
    public $showDetailModal = false;
    public $isEditing = false;
    public $siteId = null;
    public $selectedSite = null;

    // Form fields
    public $client_id = '';
    public $site_code = '';
    public $site_name = '';
    public $site_type = 'Office';
    public $address = '';
    public $contact_person = '';
    public $contact_phone = '';
    public $required_guards = 1;
    public $supervisor_id = '';
    public $duty_instructions = '';
    public $shift_requirements = '';
    public $status = 'active';

    protected function rules()
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'site_name' => 'required|string|max:150',
            'site_type' => 'required|string',
            'address' => 'required|string',
            'required_guards' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->isEditing = false;

        $latestSite = Site::orderBy('id', 'desc')->first();
        $nextNum = $latestSite ? ($latestSite->id + 1) : 1;
        $this->site_code = sprintf('STE-%03d', $nextNum);

        $firstClient = Client::where('status', 'active')->first();
        if ($firstClient) {
            $this->client_id = $firstClient->id;
        }

        $this->showCreateModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $this->isEditing = true;
        $this->siteId = $id;

        $site = Site::findOrFail($id);
        $this->client_id = $site->client_id;
        $this->site_code = $site->site_code;
        $this->site_name = $site->site_name;
        $this->site_type = $site->site_type;
        $this->address = $site->address;
        $this->contact_person = $site->contact_person;
        $this->contact_phone = $site->contact_phone;
        $this->required_guards = $site->required_guards;
        $this->supervisor_id = $site->supervisor_id;
        $this->duty_instructions = $site->duty_instructions;
        $this->shift_requirements = $site->shift_requirements;
        $this->status = $site->status;

        $this->showCreateModal = true;
    }

    public function viewDetails($id)
    {
        $this->selectedSite = Site::with(['client', 'supervisor', 'activeDeployments.securityGuard', 'activeDeployments.shift'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'client_id' => $this->client_id,
            'site_code' => $this->site_code,
            'site_name' => $this->site_name,
            'site_type' => $this->site_type,
            'address' => $this->address,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'required_guards' => $this->required_guards,
            'supervisor_id' => $this->supervisor_id ?: null,
            'duty_instructions' => $this->duty_instructions,
            'shift_requirements' => $this->shift_requirements,
            'status' => $this->status,
        ];

        if ($this->isEditing) {
            $site = Site::findOrFail($this->siteId);
            $site->update($data);
            AuditLog::log('updated', 'sites', $site->id, "Updated deployment site: {$site->site_name}");
            session()->flash('success', "Site {$site->site_name} updated successfully.");
        } else {
            $site = Site::create($data);
            AuditLog::log('created', 'sites', $site->id, "Created deployment site: {$site->site_name} ({$site->site_code})");
            session()->flash('success', "Site {$site->site_name} registered successfully.");
        }

        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->siteId = null;
        $this->client_id = '';
        $this->site_code = '';
        $this->site_name = '';
        $this->site_type = 'Office';
        $this->address = '';
        $this->contact_person = '';
        $this->contact_phone = '';
        $this->required_guards = 1;
        $this->supervisor_id = '';
        $this->duty_instructions = '';
        $this->shift_requirements = '';
        $this->status = 'active';
    }

    public function render()
    {
        $query = Site::with(['client', 'supervisor', 'activeDeployments']);

        // Site supervisor restriction
        if (auth()->user()->isSupervisor()) {
            $query->where('supervisor_id', auth()->id());
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('site_name', 'like', '%' . $this->search . '%')
                  ->orWhere('site_code', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->clientFilter) {
            $query->where('client_id', $this->clientFilter);
        }

        if ($this->typeFilter) {
            $query->where('site_type', $this->typeFilter);
        }

        $sites = $query->orderBy('id', 'desc')->paginate(10);
        $clients = Client::where('status', 'active')->orderBy('client_name')->get();
        $supervisors = User::whereIn('role', ['site_supervisor', 'operations_manager', 'admin'])->get();

        return view('livewire.sites-manager', [
            'sites' => $sites,
            'clients' => $clients,
            'supervisors' => $supervisors,
        ])->layout('layouts.app', ['title' => 'Deployment Site Management', 'subtitle' => 'Security Locations & Manning']);
    }
}
