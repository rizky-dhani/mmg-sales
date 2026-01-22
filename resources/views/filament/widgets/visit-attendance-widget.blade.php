<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Visit Attendance
        </x-slot>

        <div class="flex flex-col items-center justify-center space-y-4 py-4">
            @if ($activeVisit)
                <div class="text-center">
                    <p class="text-lg text-gray-500 dark:text-gray-400 mb-1">Ongoing visit:</p>
                    <p class="text-xl font-bold text-primary-600 mb-1">{{ $activeVisit->customer->facility_name }}</p>
                    <p class="text-md text-gray-400 mt-1 mb-3">Started at: {{ $activeVisit->visit_started_at->timezone('Asia/Jakarta')->format('H:i') }}</p>
                </div>

                <div>
                    {{ $this->endVisitAction }}
                </div>
            @else
                <div class="text-center mb-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1"">You don't have an active visit.</p>
                </div>

                <div>
                    {{ $this->startVisitAction }}
                </div>
            @endif
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
