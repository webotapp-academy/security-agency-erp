<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\AuditLog;

class ClientsManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    // Modal state
    public $showCreateModal = false;
    public $showDetailModal = false;
    public $isEditing = false;
    public $clientId = null;
    public $selectedClient = null;

    // Form fields
    public $client_code = '';
    public $client_name = '';
    public $company_name = '';
    public $contact_person = '';
    public $phone = '';
    public $email = '';
    public $billing_address = '';
    public $gstin = '';
    public $state = 'Assam';
    public $pincode = '';
    public $contract_start_date = '';
    public $contract_end_date = '';
    public $contract_amount = 0;
    public $payment_terms = 'Net 30';
    public $status = 'active';
    public $notes = '';

    protected function rules()
    {
        return [
            'client_name' => 'required|string|max:150',
            'contact_person' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
            'contract_amount' => 'required|numeric|min:0',
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

        $latestClient = Client::orderBy('id', 'desc')->first();
        $nextNum = $latestClient ? ($latestClient->id + 1) : 1;
        $this->client_code = sprintf('CLI-%03d', $nextNum);
        $this->contract_start_date = now()->toDateString();
        $this->contract_end_date = now()->addYear()->toDateString();

        $this->showCreateModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $this->isEditing = true;
        $this->clientId = $id;

        $client = Client::findOrFail($id);
        $this->client_code = $client->client_code;
        $this->client_name = $client->client_name;
        $this->company_name = $client->company_name;
        $this->contact_person = $client->contact_person;
        $this->phone = $client->phone;
        $this->email = $client->email;
        $this->billing_address = $client->billing_address;
        $this->gstin = $client->gstin;
        $this->state = $client->state;
        $this->pincode = $client->pincode;
        $this->contract_start_date = $client->contract_start_date ? $client->contract_start_date->format('Y-m-d') : '';
        $this->contract_end_date = $client->contract_end_date ? $client->contract_end_date->format('Y-m-d') : '';
        $this->contract_amount = $client->contract_amount;
        $this->payment_terms = $client->payment_terms;
        $this->status = $client->status;
        $this->notes = $client->notes;

        $this->showCreateModal = true;
    }

    public function viewDetails($id)
    {
        $this->selectedClient = Client::with([
            'sites.activeDeployments.securityGuard',
            'invoices' => function($q) {
                $q->orderBy('issue_date', 'desc')->take(5);
            }
        ])->findOrFail($id);

        $this->showDetailModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'client_code' => $this->client_code,
            'client_name' => $this->client_name,
            'company_name' => $this->company_name,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'email' => $this->email,
            'billing_address' => $this->billing_address,
            'gstin' => $this->gstin,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'contract_start_date' => $this->contract_start_date ?: null,
            'contract_end_date' => $this->contract_end_date ?: null,
            'contract_amount' => $this->contract_amount,
            'payment_terms' => $this->payment_terms,
            'status' => $this->status,
            'notes' => $this->notes,
        ];

        if ($this->isEditing) {
            $client = Client::findOrFail($this->clientId);
            $client->update($data);
            AuditLog::log('updated', 'clients', $client->id, "Updated client: {$client->client_name}");
            session()->flash('success', "Client {$client->client_name} updated successfully.");
        } else {
            $client = Client::create($data);
            AuditLog::log('created', 'clients', $client->id, "Created new client: {$client->client_name} ({$client->client_code})");
            session()->flash('success', "Client {$client->client_name} added successfully.");
        }

        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->clientId = null;
        $this->client_code = '';
        $this->client_name = '';
        $this->company_name = '';
        $this->contact_person = '';
        $this->phone = '';
        $this->email = '';
        $this->billing_address = '';
        $this->gstin = '';
        $this->state = 'Assam';
        $this->pincode = '';
        $this->contract_start_date = '';
        $this->contract_end_date = '';
        $this->contract_amount = 0;
        $this->payment_terms = 'Net 30';
        $this->status = 'active';
        $this->notes = '';
    }

    public function render()
    {
        $query = Client::with(['sites.activeDeployments']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('client_name', 'like', '%' . $this->search . '%')
                  ->orWhere('company_name', 'like', '%' . $this->search . '%')
                  ->orWhere('client_code', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $clients = $query->orderBy('id', 'desc')->paginate(10);

        return view('livewire.clients-manager', [
            'clients' => $clients,
        ])->layout('layouts.app', ['title' => 'Client Management', 'subtitle' => 'Corporate & Residential Clients']);
    }
}
