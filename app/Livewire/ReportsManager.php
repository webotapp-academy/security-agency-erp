<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Guard;
use App\Models\Client;
use App\Models\Site;
use App\Models\Deployment;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Incident;
use App\Models\EquipmentAssignment;
use Carbon\Carbon;

class ReportsManager extends Component
{
    public $reportType = 'guards';
    public $month = '';
    public $year = '';

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function exportCsv()
    {
        $filename = "webotapp_{$this->reportType}_report_" . date('Ymd_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $data = $this->getReportData();

        return response()->stream(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $data['columns']);
            foreach ($data['rows'] as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function getReportData(): array
    {
        switch ($this->reportType) {
            case 'guards':
                $rows = Guard::orderBy('guard_code')->get()->map(fn($g) => [
                    $g->guard_code, $g->full_name, $g->designation, $g->phone, $g->police_verification_status, $g->basic_salary, ucfirst($g->status)
                ])->toArray();
                return ['title' => 'Registered Security Guards', 'columns' => ['Code', 'Name', 'Designation', 'Phone', 'Police Verif.', 'Basic Salary', 'Status'], 'rows' => $rows];

            case 'deployments':
                $rows = Deployment::with(['securityGuard', 'site.client', 'shift'])->where('status', 'active')->get()->map(fn($d) => [
                    $d->securityGuard->guard_code ?? '', $d->securityGuard->full_name ?? '', $d->site->site_name ?? '', $d->site->client->client_name ?? '', $d->shift->name ?? 'General', $d->start_date->format('d M Y')
                ])->toArray();
                return ['title' => 'Active Site-wise Guard Deployments', 'columns' => ['Guard Code', 'Guard Name', 'Site', 'Client', 'Shift', 'Start Date'], 'rows' => $rows];

            case 'payroll':
                $rows = Payroll::with('securityGuard')->where('month', $this->month)->where('year', $this->year)->get()->map(fn($p) => [
                    $p->payroll_code, $p->securityGuard->full_name ?? '', (float)$p->present_days, $p->gross_salary, $p->advance_deductions, $p->net_salary, ucfirst($p->status)
                ])->toArray();
                return ['title' => 'Monthly Payroll Report', 'columns' => ['Payroll #', 'Guard', 'Present Days', 'Gross (₹)', 'Advance Ded. (₹)', 'Net Payable (₹)', 'Status'], 'rows' => $rows];

            case 'invoices':
                $rows = Invoice::with('client')->orderBy('issue_date', 'desc')->get()->map(fn($i) => [
                    $i->invoice_number, $i->client->client_name ?? '', $i->billing_period, $i->total_amount, $i->paid_amount, $i->balance_amount, ucfirst(str_replace('_', ' ', $i->status))
                ])->toArray();
                return ['title' => 'Client Invoices & Outstanding Report', 'columns' => ['Invoice #', 'Client', 'Period', 'Total (₹)', 'Paid (₹)', 'Balance (₹)', 'Status'], 'rows' => $rows];

            case 'expenses':
                $rows = Expense::orderBy('expense_date', 'desc')->get()->map(fn($e) => [
                    $e->expense_code, $e->expense_date->format('d M Y'), ucfirst(str_replace('_', ' ', $e->category)), $e->payee, $e->amount, ucfirst(str_replace('_', ' ', $e->payment_mode))
                ])->toArray();
                return ['title' => 'Agency Expense Report', 'columns' => ['Voucher #', 'Date', 'Category', 'Payee', 'Amount (₹)', 'Mode'], 'rows' => $rows];

            case 'incidents':
                $rows = Incident::with('site')->orderBy('incident_datetime', 'desc')->get()->map(fn($inc) => [
                    $inc->incident_code, $inc->incident_datetime->format('d M Y H:i'), $inc->site->site_name ?? '', ucfirst(str_replace('_', ' ', $inc->category)), $inc->reported_by, ucfirst($inc->resolution_status)
                ])->toArray();
                return ['title' => 'Security Incidents Report', 'columns' => ['Code', 'Date & Time', 'Site', 'Category', 'Reported By', 'Status'], 'rows' => $rows];

            default:
                return ['title' => 'Report', 'columns' => [], 'rows' => []];
        }
    }

    public function render()
    {
        $report = $this->getReportData();
        return view('livewire.reports-manager', compact('report'))
            ->layout('layouts.app', ['title' => 'Reports & Exports', 'subtitle' => 'Operational, Attendance & Financial Reports']);
    }
}
