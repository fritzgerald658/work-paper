<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tax Data Capture') }}
        </h2>
    </x-slot>

    <!-- File Viewer Modal -->
    <div x-data="fileViewerData()" @open-file-viewer.window="openFile($event.detail.url, $event.detail.name)">
        @include('components.file-viewer-modal')
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Define Alpine component BEFORE using it -->
            <script>
                function workingPaperApp(initialTypes) {
                    return {
                        selectedTypes: Array.isArray(initialTypes) ? initialTypes : [],
                    }
                }

                function fileViewerData() {
                    return {
                        ...fileViewerModal(),
                        
                        openFile(url, name) {
                            this.openModal(url, name);
                        }
                    }
                }
            </script>

            <!-- Main Container with Alpine.js -->
            <div x-data="workingPaperApp({{ json_encode($workingPaper->selected_types ?? []) }})" class="space-y-6">
                
                <!-- Type Selector Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Financial Year Switcher -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Financial Year</label>
                            <select 
                                onchange="window.location.href='{{ route('dashboard') }}?year='+this.value" 
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach($financialYears as $year)
                                    <option value="{{ $year }}" {{ $year === $workingPaper->financial_year ? 'selected' : '' }}>
                                        {{ $year }}
                                        @if($year === $workingPaper->financial_year)
                                            (Current)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Select a year to view or create working papers for that period
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Select Work Types</label>
                            
                            <form method="POST" action="{{ route('client.working-paper.update-types', $workingPaper) }}">
                                @csrf
                                @method('PATCH')
                                
                                <div class="flex flex-wrap gap-3 mb-4">
                                    @foreach($availableTypes as $key => $label)
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                name="selected_types[]" 
                                                value="{{ $key }}"
                                                x-model="selectedTypes"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                {{ in_array($key, $workingPaper->selected_types ?? []) ? 'checked' : '' }}
                                                {{ $workingPaper->status === 'submitted' ? 'disabled' : '' }}
                                            >
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                @if($workingPaper->status !== 'submitted')
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        Update Types
                                    </button>
                                @else
                                    <p class="text-sm text-amber-600">
                                        This working paper has been submitted. Work types cannot be changed.
                                    </p>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Wage Section -->
                <div x-show="selectedTypes.includes('wage')" x-cloak>
                    @include('client.sections.wage', ['workingPaper' => $workingPaper])
                </div>

                <!-- Rental Property Section -->
                <div x-show="selectedTypes.includes('rental')" x-cloak>
                    @include('client.sections.rental', ['workingPaper' => $workingPaper])
                </div>

                <!-- Sole Trader Section -->
                <div x-show="selectedTypes.includes('sole_trader')" x-cloak>
                    @include('client.sections.sole-trader', ['workingPaper' => $workingPaper])
                </div>

                <!-- BAS Section -->
                <div x-show="selectedTypes.includes('bas')" x-cloak>
                    @include('client.sections.bas', ['workingPaper' => $workingPaper])
                </div>

                <!-- Company Tax Section -->
                <div x-show="selectedTypes.includes('ctax')" x-cloak>
                    @include('client.sections.ctax', ['workingPaper' => $workingPaper])
                </div>

                <!-- Trust Tax Section -->
                <div x-show="selectedTypes.includes('ttax')" x-cloak>
                    @include('client.sections.ttax', ['workingPaper' => $workingPaper])
                </div>

                <!-- SMSF Section -->
                <div x-show="selectedTypes.includes('smsf')" x-cloak>
                    @include('client.sections.smsf', ['workingPaper' => $workingPaper])
                </div>

                <!-- Submit Button -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-show="selectedTypes.length > 0">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">
                                    Status: 
                                    <span class="font-semibold px-2 py-1 rounded {{ $workingPaper->status === 'submitted' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($workingPaper->status) }}
                                    </span>
                                </p>
                                @if($workingPaper->submitted_at)
                                    <p class="text-sm text-gray-600 mt-1">Submitted: {{ $workingPaper->submitted_at->format('d M Y, h:i A') }}</p>
                                @endif
                            </div>
                            
                            @if($workingPaper->status !== 'submitted')
                                <form method="POST" action="{{ route('client.working-paper.submit', $workingPaper) }}">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        onclick="return confirm('Submit this working paper? You will not be able to edit it after submission.')"
                                        class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 font-semibold"
                                    >
                                        Submit All Data
                                    </button>
                                </form>
                            @else
                                <div class="text-sm text-gray-500">
                                    This working paper has been submitted
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>