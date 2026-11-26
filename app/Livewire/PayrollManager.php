<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payroll;
use App\Models\SalaryAdvance;
use App\Models\Guard;
use App\Models\Attendance;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PayrollManager extends Component
{
    use WithPagination;

    public $month = 11;
    public $year = 2026;
    public $statusFilter = '';

    // Modal state
    public $showGenerateModal = false;
    public $showDisburseModal = false;
    public $showPayModal = false;
    public $showSlipModal = false;
    public $showAdvanceModal = false;

    public $selectedPayroll = null;

    // Disburse fields
    public $disbursePayrollId = null;
    public $payment_date = '';
    public $payment_mode = 'bank_transfer';
    public $payment_reference = '';
    public $disburse_notes = '';

    // Advance fields
    public $advance_guard_id = '';
    public $advance_amount = 0;
    public $advance_reason = '';

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->payment_date = now()->toDateString();
    }

    public function openGenerateModal()
    {
        $this->showGenerateModal = true;
    }

    public function generatePayroll()
    {
        $guards = Guard::where('status', 'active')->get();
        $daysInMonth = Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;
        $count = 0;

        foreach ($guards as $g) {
            // Check if already generated
            $existing = Payroll::where('guard_id', $g->id)
                ->where('month', $this->month)
                ->where('year', $this->year)
                ->first();

            if ($existing) continue;

            // Attendance calculation
            $attendances = Attendance::where('guard_id', $g->id)
                ->whereMonth('attendance_date', $this->month)
                ->whereYear('attendance_date', $this->year)
                ->get();

            $presentDays = 0.0;
            $overtimeHours = 0.0;

            foreach ($attendances as $att) {
                if (in_array($att->status, ['present', 'overtime'])) {
                    $presentDays += 1.0;
                } elseif ($att->status === 'half_day') {
                    $presentDays += 0.5;
                } elseif ($att->status === 'weekly_off' || $att->status === 'holiday') {
                    $presentDays += 1.0; // Paid off
                }
                $overtimeHours += (float) $att->overtime_hours;
            }

            // Fallback if no attendance marked yet
            if ($attendances->isEmpty()) {
                $presentDays = (float) min($daysInMonth, 26);
            }

            $basic = (float) $g->basic_salary;
            $allowances = (float) $g->allowances;
            $otRate = (float) $g->overtime_rate;
            $otAmount = $overtimeHours * $otRate;

            // Recover any pending advance
            $pendingAdvance = SalaryAdvance::where('guard_id', $g->id)
                ->where('status', 'approved')
                ->whereColumn('recovered_amount', '<', 'amount')
                ->first();

            $advanceDeduction = 0.0;
            if ($pendingAdvance) {
                $remaining = $pendingAdvance->amount - $pendingAdvance->recovered_amount;
                $advanceDeduction = min($remaining, 2500.00); // Standard recovery installment
            }

            // Prorated basic if less attendance
            $earnedBasic = ($basic / max(1, $daysInMonth)) * $presentDays;
            $gross = $earnedBasic + $allowances + $otAmount;
            $net = max(0, $gross - $advanceDeduction);

            $payrollCode = sprintf('PAY-%04d%02d-%03d', $this->year, $this->month, $g->id);

            Payroll::create([
                'payroll_code' => $payrollCode,
                'guard_id' => $g->id,
                'month' => $this->month,
                'year' => $this->year,
                'salary_type' => $g->salary_type,
                'basic_salary' => $basic,
                'allowances' => $allowances,
                'overtime_hours' => $overtimeHours,
                'overtime_rate' => $otRate,
                'overtime_amount' => $otAmount,
                'total_working_days' => $daysInMonth,
                'present_days' => $presentDays,
                'leave_deductions' => max(0, $basic - $earnedBasic),
                'advance_deductions' => $advanceDeduction,
                'other_deductions' => 0.00,
                'gross_salary' => $gross,
                'net_salary' => $net,
                'status' => 'pending',
                'generated_by_id' => Auth::id(),
            ]);

            $count++;
        }

        AuditLog::log('payroll_generated', 'payroll', null, "Generated {$count} payroll sheets for {$this->month}/{$this->year}");
        session()->flash('success', "Generated {$count} payroll records for " . date('F', mktime(0, 0, 0, $this->month, 10)) . " {$this->year}.");

        $this->showGenerateModal = false;
    }

    public function openDisburseModal($id)
    {
        $this->disbursePayrollId = $id;
        $this->payment_date = now()->toDateString();
        $this->payment_mode = 'bank_transfer';
        $this->payment_reference = 'NEFT' . rand(10000000, 99999999);
        $this->disburse_notes = '';
        $this->showDisburseModal = true;
    }

    public function disburseSalary()
    {
        $this->validate([
            'payment_date' => 'required|date',
            'payment_mode' => 'required',
        ]);

        $payroll = Payroll::findOrFail($this->disbursePayrollId);
        $payroll->update([
            'status' => 'paid',
            'payment_date' => $this->payment_date,
            'payment_mode' => $this->payment_mode,
            'payment_reference' => $this->payment_reference,
            'notes' => $this->disburse_notes,
        ]);

        // If advance deduction occurred, update advance recovered_amount
        if ($payroll->advance_deductions > 0) {
            $adv = SalaryAdvance::where('guard_id', $payroll->guard_id)
                ->where('status', 'approved')
                ->whereColumn('recovered_amount', '<', 'amount')
                ->first();

            if ($adv) {
                $newRecovered = $adv->recovered_amount + $payroll->advance_deductions;
                $adv->update([
                    'recovered_amount' => $newRecovered,
                    'status' => ($newRecovered >= $adv->amount) ? 'recovered' : 'approved',
                ]);
            }
        }

        AuditLog::log('salary_disbursed', 'payroll', $payroll->id, "Disbursed ₹{$payroll->net_salary} to {$payroll->securityGuard->full_name} via {$this->payment_mode}");
        session()->flash('success', "Salary marked as PAID for {$payroll->securityGuard->full_name}.");

        $this->showDisburseModal = false;
    }

    public function viewSalarySlip($id)
    {
        $this->selectedPayroll = Payroll::with(['securityGuard'])->findOrFail($id);
        $this->showSlipModal = true;
    }

    public function openAdvanceModal()
    {
        $this->resetValidation();
        $this->advance_guard_id = Guard::where('status', 'active')->first()->id ?? '';
        $this->advance_amount = 3000;
        $this->advance_reason = '';
        $this->showAdvanceModal = true;
    }

    public function submitAdvance()
    {
        $this->validate([
            'advance_guard_id' => 'required|exists:guards,id',
            'advance_amount' => 'required|numeric|min:500',
            'advance_reason' => 'required|string|min:4',
        ]);

        $latestAdv = SalaryAdvance::orderBy('id', 'desc')->first();
        $code = sprintf('ADV-%04d-%03d', date('Y'), $latestAdv ? ($latestAdv->id + 1) : 1);

        SalaryAdvance::create([
            'guard_id' => $this->advance_guard_id,
            'advance_code' => $code,
            'amount' => $this->advance_amount,
            'request_date' => now()->toDateString(),
            'status' => 'approved',
            'approved_by_id' => Auth::id(),
            'recovered_amount' => 0.00,
            'reason' => $this->advance_reason,
        ]);

        session()->flash('success', "Salary advance approved and registered for auto-recovery.");
        $this->showAdvanceModal = false;
    }

    public function render()
    {
        $query = Payroll::with(['securityGuard'])
            ->where('month', $this->month)
            ->where('year', $this->year);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $payrolls = $query->orderBy('id', 'desc')->paginate(12);

        $guards = Guard::where('status', 'active')->orderBy('full_name')->get();
        $advances = SalaryAdvance::with('securityGuard')->orderBy('id', 'desc')->take(6)->get();

        $totalGross = (float) Payroll::where('month', $this->month)->where('year', $this->year)->sum('gross_salary');
        $totalNet = (float) Payroll::where('month', $this->month)->where('year', $this->year)->sum('net_salary');
        $paidCount = Payroll::where('month', $this->month)->where('year', $this->year)->where('status', 'paid')->count();
        $pendingCount = Payroll::where('month', $this->month)->where('year', $this->year)->where('status', 'pending')->count();
        $totalPayout = $totalNet;
        $totalPaid = (float) Payroll::where('month', $this->month)->where('year', $this->year)->where('status', 'paid')->sum('net_salary');
        $totalPending = $totalPayout - $totalPaid;

        return view('livewire.payroll-manager', [
            'payrolls' => $payrolls,
            'guards' => $guards,
            'advances' => $advances,
            'totalGross' => $totalGross,
            'totalNet' => $totalNet,
            'paidCount' => $paidCount,
            'pendingCount' => $pendingCount,
            'totalPayout' => $totalPayout,
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPending,
        ])->layout('layouts.app', ['title' => 'Payroll & Salaries', 'subtitle' => 'Wage Calculations & Pay Slips']);
    }
}
