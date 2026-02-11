<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Review Working Paper - {{ $workingPaper->user->name }} ({{ $workingPaper->financial_year }})
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-800">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Client Info & Status Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-600">Client Name</p>
                            <p class="text-lg font-semibold">{{ $workingPaper->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $workingPaper->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Financial Year</p>
                            <p class="text-lg font-semibold">{{ $workingPaper->financial_year }}</p>
                            <p class="text-sm text-gray-500">Submitted: {{ $workingPaper->submitted_at?->format('M d, Y h:i A') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded {{ $workingPaper->status_color }}">
                                {{ $workingPaper->status_label }}
                            </span>
                            @if($workingPaper->reviewer)
                                <p class="text-xs text-gray-500 mt-1">Reviewed by: {{ $workingPaper->reviewer->name }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-sm text-gray-600">Selected Work Types</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($workingPaper->selected_types ?? [] as $type)
                                <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded">
                                    {{ strtoupper(str_replace('_', ' ', $type)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    @if($workingPaper->admin_comment)
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded">
                            <p class="text-sm font-semibold text-red-800">Previous Admin Feedback:</p>
                            <p class="text-sm text-red-700 mt-1">{{ $workingPaper->admin_comment }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sections Display (Read-Only) -->
            @if(in_array('wage', $workingPaper->selected_types ?? []))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Wage Section</h3>
                        @if($workingPaper->wageData)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Salary/Wages</p>
                                    <p class="text-lg font-medium">${{ number_format($workingPaper->wageData->salary_wages ?? 0, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tax Withheld</p>
                                    <p class="text-lg font-medium">${{ number_format($workingPaper->wageData->tax_withheld ?? 0, 2) }}</p>
                                </div>
                                @if($workingPaper->wageData->other_employment_items)
                                    <div class="col-span-2">
                                        <p class="text-sm text-gray-600">Other Items</p>
                                        <p class="text-sm">{{ $workingPaper->wageData->other_employment_items }}</p>
                                    </div>
                                @endif
                                @if($workingPaper->wageData->hasMedia('payg_summary'))
                                    <div class="col-span-2">
                                        <a href="{{ route('media.view-wage', $workingPaper->wageData) }}" target="_blank" class="text-blue-600 hover:underline">
                                            📄 View PAYG Summary
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 italic">No wage data</p>
                        @endif
                    </div>
                </div>
            @endif

            @foreach(['rental', 'sole_trader', 'bas', 'ctax', 'ttax', 'smsf'] as $sectionType)
                @if(in_array($sectionType, $workingPaper->selected_types ?? []))
                    @php
                        $sectionIncomes = $workingPaper->incomeItems->where('section_type', $sectionType);
                        $sectionExpenses = $workingPaper->expenseItems->where('section_type', $sectionType);
                        $sectionLabel = ['rental' => 'Rental Property', 'sole_trader' => 'Sole Trader', 'bas' => 'BAS', 'ctax' => 'Company Tax', 'ttax' => 'Trust Tax', 'smsf' => 'SMSF'][$sectionType];
                    @endphp

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $sectionLabel }}</h3>

                            <!-- Income -->
                            @if($sectionIncomes->count() > 0)
                                <h4 class="font-medium text-gray-800 mb-2">Income</h4>
                                <table class="min-w-full mb-4 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Description</th>
                                            <th class="px-4 py-2 text-left">Amount</th>
                                            <th class="px-4 py-2 text-left">Quarter</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($sectionIncomes as $income)
                                            <tr>
                                                <td class="px-4 py-2">{{ $income->description }}</td>
                                                <td class="px-4 py-2">${{ number_format($income->amount, 2) }}</td>
                                                <td class="px-4 py-2">{{ strtoupper($income->quarter ?? 'All') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <!-- Expenses -->
                            @if($sectionExpenses->count() > 0)
                                <h4 class="font-medium text-gray-800 mb-2">Expenses</h4>
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Type</th>
                                            <th class="px-4 py-2 text-left">Description</th>
                                            <th class="px-4 py-2 text-left">Inc GST</th>
                                            <th class="px-4 py-2 text-left">GST</th>
                                            <th class="px-4 py-2 text-left">Net</th>
                                            <th class="px-4 py-2 text-left">Quarter</th>
                                            <th class="px-4 py-2 text-left">Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($sectionExpenses as $expense)
                                            <tr>
                                                <td class="px-4 py-2">{{ strtoupper($expense->field_type ?? '-') }}</td>
                                                <td class="px-4 py-2">{{ $expense->description }}</td>
                                                <td class="px-4 py-2">${{ number_format($expense->amount_inc_gst, 2) }}</td>
                                                <td class="px-4 py-2">${{ number_format($expense->gst_amount, 2) }}</td>
                                                <td class="px-4 py-2">${{ number_format($expense->net_ex_gst, 2) }}</td>
                                                <td class="px-4 py-2">{{ strtoupper($expense->quarter ?? 'All') }}</td>
                                                <td class="px-4 py-2">
                                                    @if($expense->hasMedia('receipts'))
                                                        <a href="{{ route('media.view-expense', $expense) }}" target="_blank" class="text-blue-600 hover:underline">View</a>
                                                    @else
                                                        <span class="text-red-600">Missing</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            @if($sectionIncomes->count() === 0 && $sectionExpenses->count() === 0)
                                <p class="text-gray-500 italic">No data</p>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Rental Properties (if applicable) -->
            @if(in_array('rental', $workingPaper->selected_types ?? []) && $workingPaper->rentalProperties->count() > 0)
                @foreach($workingPaper->rentalProperties as $property)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rental Property: {{ $property->address_label }}</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Ownership: {{ $property->ownership_percentage ?? 'N/A' }}% | Period: {{ $property->period_rented ?? 'N/A' }}
                            </p>

                            @if($property->incomeItems->count() > 0)
                                <h4 class="font-medium text-gray-800 mb-2">Income</h4>
                                <table class="min-w-full mb-4 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Description</th>
                                            <th class="px-4 py-2 text-left">Amount</th>
                                            <th class="px-4 py-2 text-left">Quarter</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($property->incomeItems as $income)
                                            <tr>
                                                <td class="px-4 py-2">{{ $income->description }}</td>
                                                <td class="px-4 py-2">${{ number_format($income->amount, 2) }}</td>
                                                <td class="px-4 py-2">{{ strtoupper($income->quarter ?? 'All') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            @if($property->expenseItems->count() > 0)
                                <h4 class="font-medium text-gray-800 mb-2">Expenses</h4>
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Description</th>
                                            <th class="px-4 py-2 text-left">Amount</th>
                                            <th class="px-4 py-2 text-left">Quarter</th>
                                            <th class="px-4 py-2 text-left">Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($property->expenseItems as $expense)
                                            <tr>
                                                <td class="px-4 py-2">{{ $expense->description }}</td>
                                                <td class="px-4 py-2">${{ number_format($expense->amount_inc_gst, 2) }}</td>
                                                <td class="px-4 py-2">{{ strtoupper($expense->quarter ?? 'All') }}</td>
                                                <td class="px-4 py-2">
                                                    @if($expense->hasMedia('receipts'))
                                                        <a href="{{ route('media.view-expense', $expense) }}" target="_blank" class="text-blue-600 hover:underline">View</a>
                                                    @else
                                                        <span class="text-red-600">Missing</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Action Buttons -->
            @if($workingPaper->isPendingReview())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ showRejectModal: false, rejectComment: '' }">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Review Actions</h3>
                        <div class="flex gap-4">
                            <!-- Approve Button -->
                            <form method="POST" action="{{ route('admin.working-paper.approve', $workingPaper) }}" onsubmit="return confirm('Approve this working paper? This action cannot be undone.')">
                                @csrf
                                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 font-semibold">
                                    ✓ Approve
                                </button>
                            </form>

                            <!-- Reject Button -->
                            <button @click="showRejectModal = true" type="button" class="px-6 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 font-semibold">
                                ✗ Reject & Send Back
                            </button>
                        </div>

                        <!-- Reject Modal -->
                        <div x-show="showRejectModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.away="showRejectModal = false">
                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Working Paper</h3>
                                <form method="POST" action="{{ route('admin.working-paper.reject', $workingPaper) }}">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection <span class="text-red-500">*</span></label>
                                        <textarea name="admin_comment" x-model="rejectComment" required minlength="10" rows="5" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Explain what needs to be corrected..."></textarea>
                                        <p class="text-xs text-gray-500 mt-1">Minimum 10 characters. Client will see this message.</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                            Return to Client
                                        </button>
                                        <button type="button" @click="showRejectModal = false" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-gray-500 italic">
                            This working paper has already been {{ $workingPaper->status }}. No further action needed.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>