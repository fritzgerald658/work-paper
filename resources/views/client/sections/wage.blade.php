<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            Wage Section
        </h3>

        <form method="POST" action="{{ route('client.wage.save', $workingPaper) }}" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary/Wages</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="salary_wages" 
                        value="{{ $workingPaper->wageData->salary_wages ?? '' }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="0.00"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax Withheld</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="tax_withheld" 
                        value="{{ $workingPaper->wageData->tax_withheld ?? '' }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="0.00"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Other Employment Items</label>
                    <textarea 
                        name="other_employment_items" 
                        rows="3" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Any additional employment-related information..."
                    >{{ $workingPaper->wageData->other_employment_items ?? '' }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload PAYG Summary (Optional)</label>
                    <input type="file" name="payg_summary" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                    <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 10MB)</p>
                    
                    @if($workingPaper->wageData && $workingPaper->wageData->hasMedia('payg_summary'))
                        <div class="mt-2">
                            <a href="{{ $workingPaper->wageData->getFirstMediaUrl('payg_summary') }}" target="_blank" class="text-blue-600 hover:underline text-sm">
                                View current PAYG summary
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Save Wage Data
                </button>
            </div>
        </form>
    </div>
</div>