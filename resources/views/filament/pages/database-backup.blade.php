<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $backups = $this->getBackupFiles();
        @endphp

        @if (count($backups) > 0)
            <x-filament::section>
                <x-slot name="heading">
                    Backup Files
                </x-slot>

                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
                        <thead>
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                    Filename
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                    Size
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                    Created
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-sm font-semibold text-gray-950 dark:text-white">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach ($backups as $backup)
                                <tr class="bg-white dark:bg-white/5">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $backup['filename'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        @php
                                            $sizeKb = $backup['size'] / 1024;
                                        @endphp
                                        @if ($sizeKb > 1024)
                                            {{ number_format($sizeKb / 1024, 2) }} MB
                                        @else
                                            {{ number_format($sizeKb, 1) }} KB
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <div class="flex items-center justify-end gap-2">
                                            <x-filament::button
                                                wire:click="download('{{ $backup['filename'] }}')"
                                                icon="heroicon-o-arrow-down-tray"
                                                size="sm"
                                                color="gray"
                                            >
                                                Download
                                            </x-filament::button>

                                            <x-filament::button
                                                wire:click="deleteBackup('{{ $backup['filename'] }}')"
                                                wire:confirm="Are you sure you want to delete this backup?"
                                                icon="heroicon-o-trash"
                                                size="sm"
                                                color="danger"
                                                outlined
                                            >
                                                Delete
                                            </x-filament::button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-slot name="footer">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Total: {{ count($backups) }} {{ str('backup')->plural(count($backups)) }}
                    </div>
                </x-slot>
            </x-filament::section>
        @else
            <x-filament::empty-state
                heading="No backups yet"
                description="Run your first database backup using the button above."
                icon="heroicon-o-circle-stack"
            />
        @endif
    </div>
</x-filament-panels::page>
