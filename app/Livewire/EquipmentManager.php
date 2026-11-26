<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EquipmentItem;
use App\Models\EquipmentAssignment;
use App\Models\Guard;
use App\Models\AuditLog;
use Carbon\Carbon;

class EquipmentManager extends Component
{
    use WithPagination;

    public $activeTab = 'assignments'; // 'assignments' or 'inventory'
    public $search = '';
    public $statusFilter = '';

    // Modal state
    public $showIssueModal = false;
    public $showReturnModal = false;
    public $showItemModal = false;

    // Issue equipment fields
    public $issue_item_id = '';
    public $issue_guard_id = '';
    public $issue_quantity = 1;
    public $issue_date = '';
    public $condition_on_issue = 'Brand New';
    public $issue_notes = '';

    // Return equipment fields
    public $selectedAssignmentId = null;
    public $return_date = '';
    public $return_status = 'returned'; // returned, lost, damaged
    public $condition_on_return = 'Good';
    public $penalty_amount = 0;
    public $return_notes = '';

    // Add inventory item fields
    public $item_code = '';
    public $item_name = '';
    public $item_category = 'uniform';
    public $total_quantity = 10;
    public $unit_cost = 500;
    public $item_condition = 'Brand New';

    public function mount()
    {
        $this->issue_date = now()->toDateString();
        $this->return_date = now()->toDateString();
    }

    public function openIssueModal()
    {
        $this->resetValidation();
        $firstItem = EquipmentItem::where('available_quantity', '>', 0)->first();
        $this->issue_item_id = $firstItem ? $firstItem->id : '';
        $firstGuard = Guard::where('status', 'active')->first();
        $this->issue_guard_id = $firstGuard ? $firstGuard->id : '';
        $this->issue_quantity = 1;
        $this->issue_date = now()->toDateString();
        $this->condition_on_issue = 'Brand New';
        $this->issue_notes = '';

        $this->showIssueModal = true;
    }

    public function issueEquipment()
    {
        $this->validate([
            'issue_item_id' => 'required|exists:equipment_items,id',
            'issue_guard_id' => 'required|exists:guards,id',
            'issue_quantity' => 'required|integer|min:1',
            'issue_date' => 'required|date',
        ]);

        $item = EquipmentItem::findOrFail($this->issue_item_id);

        if ($item->available_quantity < $this->issue_quantity) {
            $this->addError('issue_quantity', "Only {$item->available_quantity} items available in stock.");
            return;
        }

        $guard = Guard::findOrFail($this->issue_guard_id);

        $assignment = EquipmentAssignment::create([
            'equipment_item_id' => $item->id,
            'guard_id' => $guard->id,
            'quantity' => $this->issue_quantity,
            'issue_date' => $this->issue_date,
            'condition_on_issue' => $this->condition_on_issue,
            'status' => 'issued',
            'notes' => $this->issue_notes,
        ]);

        // Deduct inventory
        $item->decrement('available_quantity', $this->issue_quantity);

        AuditLog::log('equipment_issued', 'equipment', $assignment->id, "Issued {$this->issue_quantity}x {$item->name} to guard {$guard->full_name}");
        session()->flash('success', "Equipment issued successfully to {$guard->full_name}.");

        $this->showIssueModal = false;
    }

    public function openReturnModal($id)
    {
        $this->resetValidation();
        $this->selectedAssignmentId = $id;
        $this->return_date = now()->toDateString();
        $this->return_status = 'returned';
        $this->condition_on_return = 'Good';
        $this->penalty_amount = 0;
        $this->return_notes = '';

        $this->showReturnModal = true;
    }

    public function processReturn()
    {
        $this->validate([
            'return_date' => 'required|date',
            'return_status' => 'required|in:returned,lost,damaged',
            'penalty_amount' => 'nullable|numeric|min:0',
        ]);

        $assignment = EquipmentAssignment::findOrFail($this->selectedAssignmentId);
        $item = $assignment->equipmentItem;
        $guard = $assignment->securityGuard;

        $assignment->update([
            'return_date' => $this->return_date,
            'status' => $this->return_status,
            'condition_on_return' => $this->condition_on_return,
            'penalty_amount' => $this->penalty_amount ?: 0,
            'notes' => $assignment->notes . " [Status on {$this->return_date}: {$this->return_status}. {$this->return_notes}]",
        ]);

        // If returned in usable condition, restore stock
        if ($this->return_status === 'returned') {
            $item->increment('available_quantity', $assignment->quantity);
        }

        AuditLog::log('equipment_returned', 'equipment', $assignment->id, "Processed return of {$item->name} from guard {$guard->full_name} ({$this->return_status})");
        session()->flash('success', "Equipment return updated: " . strtoupper($this->return_status));

        $this->showReturnModal = false;
    }

    public function openItemModal()
    {
        $this->resetValidation();
        $prefix = 'EQP-' . strtoupper(substr($this->item_category, 0, 3));
        $this->item_code = $prefix . '-' . rand(100, 999);
        $this->item_name = '';
        $this->item_category = 'uniform';
        $this->total_quantity = 10;
        $this->unit_cost = 500;
        $this->item_condition = 'Brand New';

        $this->showItemModal = true;
    }

    public function createItem()
    {
        $this->validate([
            'item_name' => 'required|string|max:150',
            'item_code' => 'required|string|unique:equipment_items,item_code',
            'total_quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        EquipmentItem::create([
            'item_code' => strtoupper($this->item_code),
            'name' => $this->item_name,
            'category' => $this->item_category,
            'total_quantity' => $this->total_quantity,
            'available_quantity' => $this->total_quantity,
            'unit_cost' => $this->unit_cost,
            'condition_status' => $this->item_condition,
        ]);

        session()->flash('success', "New equipment item '{$this->item_name}' added to inventory.");
        $this->showItemModal = false;
    }

    public function render()
    {
        $inventory = EquipmentItem::withCount('assignments')->orderBy('name')->get();
        $guards = Guard::where('status', 'active')->orderBy('full_name')->get();

        $query = EquipmentAssignment::with(['equipmentItem', 'securityGuard']);

        if ($this->search) {
            $query->whereHas('securityGuard', function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('guard_code', 'like', '%' . $this->search . '%');
            })->orWhereHas('equipmentItem', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('item_code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $assignments = $query->orderBy('issue_date', 'desc')->paginate(12);

        return view('livewire.equipment-manager', [
            'inventory' => $inventory,
            'guards' => $guards,
            'assignments' => $assignments,
        ])->layout('layouts.app', ['title' => 'Equipment & Uniforms', 'subtitle' => 'Uniforms, Gear Inventory & Issue Register']);
    }
}
