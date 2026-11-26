<div class="space-y-3.5">
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5 no-print">
        <div class="flex flex-wrap items-center gap-2">
            <label class="text-xs font-semibold text-slate-700">Report Type:</label>
            <select wire:model.live="reportType" class="py-1 px-2.5 text-xs bg-slate-50 border border-slate-300 rounded font-medium">
                <option value="guards">Guard List (Active & Inactive)</option>
                <option value="deployments">Site-wise Guard Deployment</option>
                <option value="payroll">Monthly Payroll & Salary</option>
                <option value="invoices">Client Invoices & Outstanding</option>
                <option value="expenses">Agency Expense Report</option>
                <option value="incidents">Security Incident Report</option>
            </select>

            @if ($reportType === 'payroll')
                <select wire:model.live="month" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                    @endfor
                </select>
                <select wire:model.live="year" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded">
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                </select>
            @endif
        </div>

        <div class="flex items-center space-x-2">
            <button wire:click="exportCsv" class="px-3 py-1.5 rounded text-xs font-semibold bg-emerald-700 hover:bg-emerald-800 text-white cursor-pointer">
                Export Excel / CSV
            </button>
            <button onclick="window.print()" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white cursor-pointer">
                Print / Save PDF
            </button>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="px-4 py-2.5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <span class="font-bold text-xs text-slate-900">{{ $report['title'] }}</span>
            <span class="text-[11px] text-slate-500">{{ count($report['rows']) }} Records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        @foreach ($report['columns'] as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($report['rows'] as $row)
                        <tr class="hover:bg-slate-50/80">
                            @foreach ($row as $cell)
                                <td class="text-xs text-slate-800">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($report['columns']) }}" class="text-center py-6 text-xs text-slate-500">No data available for this report.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
