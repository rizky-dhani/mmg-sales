<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
        <h3 class="text-lg font-semibold text-gray-950 dark:text-white mb-4 flex items-center gap-2">
            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-900 dark:text-green-300">Won</span>
            Recent Wins
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-3 font-medium text-gray-500 dark:text-gray-400">Project</th>
                        <th class="text-right py-2 px-3 font-medium text-gray-500 dark:text-gray-400">Value</th>
                        <th class="text-left py-2 px-3 font-medium text-gray-500 dark:text-gray-400">Closed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getRecentWins() as $win)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 px-3">
                                <div class="text-gray-950 dark:text-white">{{ $win['name'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $win['customer'] }}</div>
                            </td>
                            <td class="py-2 px-3 text-right text-gray-950 dark:text-white">{{ Number::currency($win['value'], 'IDR', 'id_ID') }}</td>
                            <td class="py-2 px-3 text-gray-500 dark:text-gray-400">{{ $win['closed_at'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
        <h3 class="text-lg font-semibold text-gray-950 dark:text-white mb-4 flex items-center gap-2">
            <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700 dark:bg-red-900 dark:text-red-300">Lost</span>
            Recent Losses
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-3 font-medium text-gray-500 dark:text-gray-400">Project</th>
                        <th class="text-right py-2 px-3 font-medium text-gray-500 dark:text-gray-400">Value</th>
                        <th class="text-left py-2 px-3 font-medium text-gray-500 dark:text-gray-400">Closed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getRecentLosses() as $loss)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 px-3">
                                <div class="text-gray-950 dark:text-white">{{ $loss['name'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $loss['customer'] }}</div>
                            </td>
                            <td class="py-2 px-3 text-right text-gray-950 dark:text-white">{{ Number::currency($loss['value'], 'IDR', 'id_ID') }}</td>
                            <td class="py-2 px-3 text-gray-500 dark:text-gray-400">{{ $loss['closed_at'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
