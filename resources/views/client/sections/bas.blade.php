<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            BAS (Business Activity Statement) Section
        </h3>

        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
            <p class="text-sm text-yellow-800">
                <strong>Important:</strong> BAS requires quarterly data (Q1-Q4 only). The "All" option is disabled to prevent double-counting in your BAS lodgement.
            </p>
        </div>

        <!-- Income Section -->
        <div class="mb-8">
            <h4 class="text-md font-semibold text-gray-800 mb-3">Income (Sales)</h4>
            
            @php
                $basIncomes = $workingPaper->incomeItems->where('section_type', 'bas');
                
                // Group by quarter for display
                $incomeByQuarter = [
                    'q1' => $basIncomes->where('quarter', 'q1'),
                    'q2' => $basIncomes->where('quarter', 'q2'),
                    'q3' => $basIncomes->where('quarter', 'q3'),
                    'q4' => $basIncomes->where('quarter', 'q4'),
                ];
            @endphp

            @if($basIncomes->count() > 0)
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($basIncomes as $income)
                                <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-gray-50' : '' }}">
                                    <td class="px-4 py-2 text-sm font-semibold">{{ strtoupper($income->quarter) }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $income->description }}</td>
                                    <td class="px-4 py-2 text-sm font-medium">${{ number_format($income->amount, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <form method="POST" action="{{ route('client.income.destroy', $income) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this income item?')" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td colspan="2" class="px-4 py-2 text-sm font-bold text-gray-900">Total Income</td>
                                <td class="px-4 py-2 text-sm font-bold text-green-600">${{ number_format($basIncomes->sum('amount'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Quarterly Breakdown -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    @foreach(['q1' => 'Q1 (Jul-Sep)', 'q2' => 'Q2 (Oct-Dec)', 'q3' => 'Q3 (Jan-Mar)', 'q4' => 'Q4 (Apr-Jun)'] as $qKey => $qLabel)
                        <div class="p-3 {{ $incomeByQuarter[$qKey]->count() > 0 ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }} border rounded">
                            <p class="text-xs text-gray-600">{{ $qLabel }}</p>
                            <p class="text-lg font-bold {{ $incomeByQuarter[$qKey]->count() > 0 ? 'text-green-700' : 'text-gray-400' }}">
                                ${{ number_format($incomeByQuarter[$qKey]->sum('amount'), 2) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $incomeByQuarter[$qKey]->count() }} item(s)</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Add Income Form -->
            <div x-data="{ showIncomeForm: false }">
                <button 
                    @click="showIncomeForm = !showIncomeForm" 
                    type="button"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-3"
                >
                    + Add Income Line
                </button>

                <div x-show="showIncomeForm" x-cloak class="border rounded-lg p-4 bg-gray-50">
                    <form method="POST" action="{{ route('client.income.store', $workingPaper) }}">
                        @csrf
                        <input type="hidden" name="section_type" value="bas">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                <input type="text" name="description" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Sales revenue">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="amount" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quarter <span class="text-red-500">*</span></label>
                                <select name="quarter" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Quarter</option>
                                    <option value="q1">Q1 (Jul-Sep)</option>
                                    <option value="q2">Q2 (Oct-Dec)</option>
                                    <option value="q3">Q3 (Jan-Mar)</option>
                                    <option value="q4">Q4 (Apr-Jun)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">"All" option disabled for BAS to prevent double-counting</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment (Optional)</label>
                                <textarea name="client_comment" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add any notes or comments..."></textarea>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Save Income</button>
                            <button type="button" @click="showIncomeForm = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Expense Section -->
        <div class="mb-8">
            <h4 class="text-md font-semibold text-gray-800 mb-3">Expenses (Purchases)</h4>
            
            @php
                $basExpenses = $workingPaper->expenseItems->where('section_type', 'bas');
                
                // Group by quarter for display
                $expenseByQuarter = [
                    'q1' => $basExpenses->where('quarter', 'q1'),
                    'q2' => $basExpenses->where('quarter', 'q2'),
                    'q3' => $basExpenses->where('quarter', 'q3'),
                    'q4' => $basExpenses->where('quarter', 'q4'),
                ];
            @endphp

            @if($basExpenses->count() > 0)
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Inc GST</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">GST</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Net</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($basExpenses as $expense)
                                <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-gray-50' : '' }}">
                                    <td class="px-4 py-2 text-sm font-semibold">{{ strtoupper($expense->quarter) }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <span class="px-2 py-1 text-xs font-medium rounded {{ $expense->field_type === 'a' ? 'bg-blue-100 text-blue-800' : ($expense->field_type === 'b' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                            {{ strtoupper($expense->field_type ?? '-') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm">{{ $expense->description }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->amount_inc_gst, 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-orange-600">${{ number_format($expense->gst_amount, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->net_ex_gst, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($expense->hasMedia('receipts'))
                                            <button @click="$dispatch('open-file-viewer', {url: '{{ route('media.view-expense', $expense) }}', name: '{{ $expense->getMedia('receipts')->first()?->file_name ?? 'Receipt' }}'})" type="button" class="text-blue-600 hover:underline cursor-pointer">
                                                View
                                            </button>
                                        @else
                                            <span class="text-red-600">Missing</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <form method="POST" action="{{ route('client.expense.destroy', $expense) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this expense item?')" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-sm font-bold text-gray-900">Total Expenses</td>
                                <td class="px-4 py-2 text-sm font-bold text-gray-700">${{ number_format($basExpenses->sum('amount_inc_gst'), 2) }}</td>
                                <td class="px-4 py-2 text-sm font-bold text-orange-600">${{ number_format($basExpenses->sum('gst_amount'), 2) }}</td>
                                <td class="px-4 py-2 text-sm font-bold text-gray-700">${{ number_format($basExpenses->sum('net_ex_gst'), 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Quarterly Breakdown -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    @foreach(['q1' => 'Q1 (Jul-Sep)', 'q2' => 'Q2 (Oct-Dec)', 'q3' => 'Q3 (Jan-Mar)', 'q4' => 'Q4 (Apr-Jun)'] as $qKey => $qLabel)
                        <div class="p-3 {{ $expenseByQuarter[$qKey]->count() > 0 ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200' }} border rounded">
                            <p class="text-xs text-gray-600">{{ $qLabel }}</p>
                            <p class="text-sm font-bold {{ $expenseByQuarter[$qKey]->count() > 0 ? 'text-gray-700' : 'text-gray-400' }}">
                                Net: ${{ number_format($expenseByQuarter[$qKey]->sum('net_ex_gst'), 2) }}
                            </p>
                            <p class="text-sm font-bold {{ $expenseByQuarter[$qKey]->count() > 0 ? 'text-orange-600' : 'text-gray-400' }}">
                                GST: ${{ number_format($expenseByQuarter[$qKey]->sum('gst_amount'), 2) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $expenseByQuarter[$qKey]->count() }} item(s)</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Add Expense Form -->
            <div x-data="{ showExpenseForm: false, autoCalculateGST: true, amountIncGst: 0 }">
                <button 
                    @click="showExpenseForm = !showExpenseForm" 
                    type="button"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-3"
                >
                    + Add Expense Line
                </button>

                <div x-show="showExpenseForm" x-cloak class="border rounded-lg p-4 bg-gray-50">
                    <form method="POST" action="{{ route('client.expense.store', $workingPaper) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="section_type" value="bas">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Field Type <span class="text-red-500">*</span></label>
                                <select name="field_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Type</option>
                                    <option value="a">Type A</option>
                                    <option value="b">Type B</option>
                                    <option value="c">Type C</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quarter <span class="text-red-500">*</span></label>
                                <select name="quarter" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Quarter</option>
                                    <option value="q1">Q1 (Jul-Sep)</option>
                                    <option value="q2">Q2 (Oct-Dec)</option>
                                    <option value="q3">Q3 (Jan-Mar)</option>
                                    <option value="q4">Q4 (Apr-Jun)</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                <input type="text" name="description" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Business expenses">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (inc GST) <span class="text-red-500">*</span></label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    name="amount_inc_gst" 
                                    x-model="amountIncGst"
                                    required 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    placeholder="0.00"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">GST Amount</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    name="gst_amount" 
                                    :value="autoCalculateGST ? (amountIncGst - (amountIncGst / 1.1)).toFixed(2) : ''"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    placeholder="Auto-calculated"
                                    :readonly="autoCalculateGST"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Net (ex GST)</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    name="net_ex_gst" 
                                    :value="autoCalculateGST ? (amountIncGst / 1.1).toFixed(2) : ''"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    placeholder="Auto-calculated"
                                    :readonly="autoCalculateGST"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" x-model="autoCalculateGST" class="rounded border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Auto-calculate GST (10%)</span>
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Receipt <span class="text-red-500">*</span></label>
                                <input type="file" name="receipt" required accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                                <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 10MB) - REQUIRED</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment (Optional)</label>
                                <textarea name="client_comment" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Save Expense
                            </button>
                            <button type="button" @click="showExpenseForm = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- BAS Summary & Quarterly Status -->
        @if($basIncomes->count() > 0 || $basExpenses->count() > 0)
            @php
                $quarters = ['q1', 'q2', 'q3', 'q4'];
                $quarterStatus = [];
                foreach($quarters as $q) {
                    $quarterStatus[$q] = [
                        'has_income' => $incomeByQuarter[$q]->count() > 0,
                        'has_expense' => $expenseByQuarter[$q]->count() > 0,
                        'complete' => $incomeByQuarter[$q]->count() > 0 || $expenseByQuarter[$q]->count() > 0
                    ];
                }
                $allQuartersComplete = collect($quarterStatus)->every(fn($s) => $s['complete']);
            @endphp

            <!-- Quarterly Status Indicator -->
            <div class="mb-6 p-4 {{ $allQuartersComplete ? 'bg-green-50 border-green-300' : 'bg-blue-50 border-blue-300' }} border rounded-lg">
                <h5 class="font-semibold text-gray-900 mb-3">Quarterly Completion Status</h5>
                <div class="grid grid-cols-4 gap-2">
                    @foreach(['q1' => 'Q1', 'q2' => 'Q2', 'q3' => 'Q3', 'q4' => 'Q4'] as $qKey => $qLabel)
                        <div class="text-center p-2 rounded {{ $quarterStatus[$qKey]['complete'] ? 'bg-green-100 border-green-300' : 'bg-gray-100 border-gray-300' }} border">
                            <p class="text-sm font-bold {{ $quarterStatus[$qKey]['complete'] ? 'text-green-700' : 'text-gray-500' }}">
                                {{ $qLabel }}
                            </p>
                            <p class="text-xs {{ $quarterStatus[$qKey]['complete'] ? 'text-green-600' : 'text-gray-500' }}">
                                {{ $quarterStatus[$qKey]['complete'] ? '✓ Complete' : 'Empty' }}
                            </p>
                        </div>
                    @endforeach
                </div>

                @if($allQuartersComplete)
                    <div class="mt-4 p-3 bg-green-100 rounded">
                        <p class="text-sm text-green-800 mb-2">All quarters have data! You can now view the annual summary.</p>
                        <button 
                            type="button" 
                            onclick="document.getElementById('annual-summary').scrollIntoView({behavior: 'smooth'})"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                        >
                            View Annual Summary ↓
                        </button>
                    </div>
                @else
                    <div class="mt-4 p-3 bg-blue-100 rounded">
                        <p class="text-sm text-blue-800">
                            Add data to all quarters (Q1-Q4) to generate the annual BAS summary.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Annual BAS Summary (shown when all quarters complete) -->
            @if($allQuartersComplete)
                <div id="annual-summary" class="mt-6 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-300 rounded-lg">
                    <h5 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        Annual BAS Summary (All Quarters Combined)
                    </h5>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <!-- Total Income -->
                        <div class="p-4 bg-white rounded-lg shadow">
                            <p class="text-sm text-gray-600 mb-1">Total Sales (G1)</p>
                            <p class="text-2xl font-bold text-green-600">${{ number_format($basIncomes->sum('amount'), 2) }}</p>
                        </div>

                        <!-- Total Purchases -->
                        <div class="p-4 bg-white rounded-lg shadow">
                            <p class="text-sm text-gray-600 mb-1">Total Purchases (G11)</p>
                            <p class="text-2xl font-bold text-gray-700">${{ number_format($basExpenses->sum('net_ex_gst'), 2) }}</p>
                        </div>

                        <!-- GST Collected vs Paid -->
                        <div class="p-4 bg-white rounded-lg shadow">
                            <p class="text-sm text-gray-600 mb-1">Total GST on Purchases (G10)</p>
                            <p class="text-2xl font-bold text-orange-600">${{ number_format($basExpenses->sum('gst_amount'), 2) }}</p>
                        </div>
                    </div>

                    <!-- Quarterly Breakdown Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg overflow-hidden">
                            <thead class="bg-indigo-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Quarter</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Sales</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Purchases (Net)</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">GST on Purchases</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach(['q1' => 'Q1 (Jul-Sep)', 'q2' => 'Q2 (Oct-Dec)', 'q3' => 'Q3 (Jan-Mar)', 'q4' => 'Q4 (Apr-Jun)'] as $qKey => $qLabel)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium">{{ $qLabel }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-green-600 font-medium">${{ number_format($incomeByQuarter[$qKey]->sum('amount'), 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-right">${{ number_format($expenseByQuarter[$qKey]->sum('net_ex_gst'), 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-orange-600">${{ number_format($expenseByQuarter[$qKey]->sum('gst_amount'), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-indigo-50 font-bold">
                                <tr>
                                    <td class="px-4 py-3 text-sm">Annual Total</td>
                                    <td class="px-4 py-3 text-sm text-right text-green-700">${{ number_format($basIncomes->sum('amount'), 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">${{ number_format($basExpenses->sum('net_ex_gst'), 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-orange-700">${{ number_format($basExpenses->sum('gst_amount'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4 p-3 bg-white rounded border border-indigo-200">
                        <p class="text-xs text-gray-600">
                            <strong>Note:</strong> This summary combines all quarterly data. Use these totals when preparing your annual BAS lodgement. 
                            Individual quarter details are preserved above for your records.
                        </p>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>