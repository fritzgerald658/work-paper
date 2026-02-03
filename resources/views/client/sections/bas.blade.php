<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            BAS Section
        </h3>

        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
            <p class="text-sm text-yellow-800">
                <strong>Note:</strong> BAS section requires quarterly data (Q1-Q4 only). "All" is not allowed to prevent double-counting.
            </p>
        </div>

        <!-- Income Section -->
        <div class="mb-8">
            <h4 class="text-md font-semibold text-gray-800 mb-3">Income</h4>
            
            @php
                $basIncomes = $workingPaper->incomeItems->where('section_type', 'bas');
            @endphp

            @if($basIncomes->count() > 0)
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($basIncomes as $income)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $income->description }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($income->amount, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">{{ strtoupper($income->quarter) }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <form method="POST" action="{{ route('client.income.destroy', $income) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Add Income Form (similar to SMSF but without "All" option) -->
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
                                <input type="text" name="description" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="amount" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Save Income</button>
                            <button type="button" @click="showIncomeForm = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Expense Section (same structure as SMSF) -->
        <div>
            <h4 class="text-md font-semibold text-gray-800 mb-3">Expenses</h4>
            
            @php
                $basExpenses = $workingPaper->expenseItems->where('section_type', 'bas');
            @endphp

            @if($basExpenses->count() > 0)
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Inc GST</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">GST</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Net</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($basExpenses as $expense)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ strtoupper($expense->field_type ?? '-') }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $expense->description }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->amount_inc_gst, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->gst_amount, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->net_ex_gst, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">{{ strtoupper($expense->quarter) }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($expense->hasMedia('receipts'))
                                            <a href="{{ $expense->getFirstMediaUrl('receipts') }}" target="_blank" class="text-blue-600 hover:underline">View</a>
                                        @else
                                            <span class="text-red-600">⚠️ Missing</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <form method="POST" action="{{ route('client.expense.destroy', $expense) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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

        <!-- Quarterly Consolidation -->
        @php
            $quarters = ['q1', 'q2', 'q3', 'q4'];
            $hasAllQuarters = collect($quarters)->every(function($q) use ($basIncomes, $basExpenses) {
                return $basIncomes->where('quarter', $q)->count() > 0 || $basExpenses->where('quarter', $q)->count() > 0;
            });
        @endphp

        @if($hasAllQuarters)
            <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded">
                <h5 class="font-semibold text-gray-900 mb-2">✓ All Quarters Present</h5>
                <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Combine Q1-Q4 → Annual Summary
                </button>
            </div>
        @endif
    </div>
</div>