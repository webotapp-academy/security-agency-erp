<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Guard;
use App\Models\GuardDocument;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class GuardsManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $designationFilter = '';

    // Modal state
    public $showCreateModal = false;
    public $showDetailModal = false;
    public $selectedGuard = null;
    public $isEditing = false;
    public $guardId = null;

    // Form fields
    public $guard_code = '';
    public $full_name = '';
    public $father_name = '';
    public $dob = '';
    public $gender = 'Male';
    public $phone = '';
    public $alternate_phone = '';
    public $email = '';
    public $permanent_address = '';
    public $current_address = '';
    public $emergency_contact_name = '';
    public $emergency_contact_phone = '';
    public $aadhaar_reference = '';
    public $pan_number = '';
    public $police_verification_status = 'pending';
    public $police_verification_ref = '';
    public $background_verification_status = 'pending';
    public $joining_date = '';
    public $employment_type = 'full_time';
    public $designation = 'Security Guard';
    public $qualification = '10th Passed';
    public $training_details = '';
    public $experience_years = '';
    public $blood_group = '';
    
    // Bank & Salary
    public $bank_name = '';
    public $account_holder_name = '';
    public $account_number = '';
    public $ifsc_code = '';
    public $salary_type = 'monthly';
    public $basic_salary = 16500;
    public $allowances = 1500;
    public $overtime_rate = 100;
    public $status = 'active';
    public $notes = '';

    // Document upload simulation/entry
    public $doc_type = 'aadhaar';
    public $doc_title = '';
    public $doc_number = '';
    public $doc_expiry = '';

    protected function rules()
    {
        return [
            'full_name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'designation' => 'required|string',
            'status' => 'required|in:active,inactive,suspended,resigned,terminated',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'basic_salary' => 'required|numeric|min:0',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->isEditing = false;

        // Generate automatic Guard ID: SEC-G-XXX
        $latestGuard = Guard::orderBy('id', 'desc')->first();
        $nextNum = $latestGuard ? ($latestGuard->id + 1) : 1;
        $this->guard_code = sprintf('SEC-G-%03d', $nextNum);
        $this->joining_date = now()->toDateString();

        $this->showCreateModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $this->isEditing = true;
        $this->guardId = $id;

        $guard = Guard::findOrFail($id);
        $this->guard_code = $guard->guard_code;
        $this->full_name = $guard->full_name;
        $this->father_name = $guard->father_name;
        $this->dob = $guard->dob ? $guard->dob->format('Y-m-d') : '';
        $this->gender = $guard->gender;
        $this->phone = $guard->phone;
        $this->alternate_phone = $guard->alternate_phone;
        $this->email = $guard->email;
        $this->permanent_address = $guard->permanent_address;
        $this->current_address = $guard->current_address;
        $this->emergency_contact_name = $guard->emergency_contact_name;
        $this->emergency_contact_phone = $guard->emergency_contact_phone;
        $this->aadhaar_reference = $guard->aadhaar_reference;
        $this->pan_number = $guard->pan_number;
        $this->police_verification_status = $guard->police_verification_status;
        $this->police_verification_ref = $guard->police_verification_ref;
        $this->background_verification_status = $guard->background_verification_status;
        $this->joining_date = $guard->joining_date ? $guard->joining_date->format('Y-m-d') : '';
        $this->employment_type = $guard->employment_type;
        $this->designation = $guard->designation;
        $this->qualification = $guard->qualification;
        $this->training_details = $guard->training_details;
        $this->experience_years = $guard->experience_years;
        $this->blood_group = $guard->blood_group;

        $this->bank_name = $guard->bank_name;
        $this->account_holder_name = $guard->account_holder_name;
        $this->account_number = $guard->account_number;
        $this->ifsc_code = $guard->ifsc_code;
        $this->salary_type = $guard->salary_type;
        $this->basic_salary = $guard->basic_salary;
        $this->allowances = $guard->allowances;
        $this->overtime_rate = $guard->overtime_rate;
        $this->status = $guard->status;
        $this->notes = $guard->notes;

        $this->showCreateModal = true;
    }

    public function viewDetails($id)
    {
        $this->selectedGuard = Guard::with(['documents', 'deployments.site', 'attendances' => function($q) {
            $q->orderBy('attendance_date', 'desc')->take(10);
        }])->findOrFail($id);
        
        $this->showDetailModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'guard_code' => $this->guard_code,
            'full_name' => $this->full_name,
            'father_name' => $this->father_name,
            'dob' => $this->dob ?: null,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'alternate_phone' => $this->alternate_phone,
            'email' => $this->email,
            'permanent_address' => $this->permanent_address,
            'current_address' => $this->current_address,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'aadhaar_reference' => $this->aadhaar_reference,
            'pan_number' => $this->pan_number,
            'police_verification_status' => $this->police_verification_status,
            'police_verification_ref' => $this->police_verification_ref,
            'background_verification_status' => $this->background_verification_status,
            'joining_date' => $this->joining_date ?: null,
            'employment_type' => $this->employment_type,
            'designation' => $this->designation,
            'qualification' => $this->qualification,
            'training_details' => $this->training_details,
            'experience_years' => $this->experience_years,
            'blood_group' => $this->blood_group,
            'bank_name' => $this->bank_name,
            'account_holder_name' => $this->account_holder_name,
            'account_number' => $this->account_number,
            'ifsc_code' => $this->ifsc_code,
            'salary_type' => $this->salary_type,
            'basic_salary' => $this->basic_salary,
            'allowances' => $this->allowances,
            'overtime_rate' => $this->overtime_rate,
            'status' => $this->status,
            'notes' => $this->notes,
        ];

        if ($this->isEditing) {
            $guard = Guard::findOrFail($this->guardId);
            $guard->update($data);
            AuditLog::log('updated', 'guards', $guard->id, "Updated guard profile: {$guard->full_name} ({$guard->guard_code})");
            session()->flash('success', "Guard {$guard->full_name} updated successfully.");
        } else {
            $guard = Guard::create($data);
            AuditLog::log('created', 'guards', $guard->id, "Registered new guard: {$guard->full_name} ({$guard->guard_code})");
            session()->flash('success', "Guard {$guard->full_name} registered successfully with ID {$guard->guard_code}.");
        }

        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function addDocument()
    {
        if (!$this->selectedGuard) return;

        $this->validate([
            'doc_title' => 'required|string|max:100',
            'doc_type' => 'required',
        ]);

        GuardDocument::create([
            'guard_id' => $this->selectedGuard->id,
            'document_type' => $this->doc_type,
            'document_title' => $this->doc_title,
            'document_number' => $this->doc_number,
            'file_path' => 'documents/sample_' . $this->doc_type . '.pdf',
            'expiry_date' => $this->doc_expiry ?: null,
        ]);

        AuditLog::log('created', 'guards', $this->selectedGuard->id, "Uploaded document {$this->doc_title} for guard {$this->selectedGuard->full_name}");

        $this->selectedGuard->load('documents');
        $this->doc_title = '';
        $this->doc_number = '';
        $this->doc_expiry = '';
        session()->flash('doc_success', 'Document attached successfully.');
    }

    public function resetForm()
    {
        $this->guardId = null;
        $this->guard_code = '';
        $this->full_name = '';
        $this->father_name = '';
        $this->dob = '';
        $this->gender = 'Male';
        $this->phone = '';
        $this->alternate_phone = '';
        $this->email = '';
        $this->permanent_address = '';
        $this->current_address = '';
        $this->emergency_contact_name = '';
        $this->emergency_contact_phone = '';
        $this->aadhaar_reference = '';
        $this->pan_number = '';
        $this->police_verification_status = 'pending';
        $this->police_verification_ref = '';
        $this->background_verification_status = 'pending';
        $this->joining_date = '';
        $this->employment_type = 'full_time';
        $this->designation = 'Security Guard';
        $this->qualification = '10th Passed';
        $this->training_details = '';
        $this->experience_years = '';
        $this->blood_group = '';
        $this->bank_name = '';
        $this->account_holder_name = '';
        $this->account_number = '';
        $this->ifsc_code = '';
        $this->salary_type = 'monthly';
        $this->basic_salary = 16500;
        $this->allowances = 1500;
        $this->overtime_rate = 100;
        $this->status = 'active';
        $this->notes = '';
    }

    public function render()
    {
        $query = Guard::with(['activeDeployment.site', 'documents']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('guard_code', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('designation', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->designationFilter) {
            $query->where('designation', $this->designationFilter);
        }

        $guards = $query->orderBy('id', 'desc')->paginate(12);

        $designations = Guard::distinct()->pluck('designation');

        return view('livewire.guards-manager', [
            'guards' => $guards,
            'designations' => $designations,
        ])->layout('layouts.app', ['title' => 'Security Guard Management', 'subtitle' => 'Profiles & Verifications']);
    }
}
