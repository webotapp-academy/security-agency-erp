<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ExpenseManager extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    public $showCreateModal = false;
    public $expense_code = '';
    public $expense_date = '';
    public $category = 'other';
    public $amount = 0;
    public $payment_mode = 'bank_transfer';
    public $payee = '';
    public $reference_number = '';
    public $description = '';

    public function mount()
    {
        $this->expense_date = now()->toDateString();
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->endOfMonth()->toDateString();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $count = Expense::count() + 1;
        $this->expense_code = sprintf('EXP-%s-%03d', date('Y'), $count);
        $this->expense_date = now()->toDateString();
        $this->category = 'other';
        $this->amount = '';
        $this->payment_mode = 'bank_transfer';
        $this->payee = '';
        $this->reference_number = '';
        $this->description = '';
        $this->showCreateModal = true;
    }

    public function saveExpense()
    {
        $this->validate([
            'expense_date' => 'required|date',
            'category' => 'required',
            'amount' => 'required|numeric|min:1',
            'payee' => 'required|string|max:150',
            'payment_mode' => 'required',
        ]);

        $expense = Expense::create([
            'expense_code' => $this->expense_code,
            'expense_date' => $this->expense_date,
            'category' => $this->category,
            'amount' => $this->amount,
            'payment_mode' => $this->payment_mode,
            'payee' => $this->payee,
            'reference_number' => $this->reference_number,
            'description' => $this->description,
            'created_by_id' => Auth::id(),
        ]);

        AuditLog::log('expense_recorded', 'expenses', $expense->id, "Recorded expense {$expense->expense_code} of ₹{$this->amount} to {$this->payee}");
        session()->flash('success', "Expense {$expense->expense_code} recorded.");
        $this->showCreateModal = false;
    }

    public function render()
    {
        $query = Expense::with('createdBy');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('expense_code', 'like', '%' . $this->search . '%')
                  ->orWhere('payee', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('expense_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('expense_date', '<=', $this->dateTo);
        }

        $totalFiltered = (float) (clone $query)->sum('amount');
        $expenses = $query->orderBy('expense_date', 'desc')->paginate(12);

        return view('livewire.expense-manager', [
            'expenses' => $expenses,
            'totalFiltered' => $totalFiltered,
        ])->layout('layouts.app', ['title' => 'Expense Management', 'subtitle' => 'Agency Operating Costs & Vouchers']);
    }
}
