<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->infolist }}

        {{-- Comments Section --}}
        @if(auth()->user()->can('view_activity_comment'))
            <x-filament::section>
                <x-slot name="heading">
                    Comments
                </x-slot>

                {{-- Comment Form --}}
                @if(auth()->user()->can('create_activity_comment'))
                    <form wire:submit="submitComment" class="mb-6">
                        <x-filament::input
                            wire:model="newComment"
                            placeholder="Add a comment..."
                        />
                        <x-filament::button
                            type="submit"
                            size="sm"
                            class="mt-2"
                            wire:loading.attr="disabled"
                        >
                            Post Comment
                        </x-filament::button>
                    </form>
                @endif

                {{-- Comments List --}}
                <div class="space-y-4">
                    @forelse($this->getRecord()->comments as $comment)
                        <div class="flex gap-3">
                            <div class="shrink-0">
                                <div class="size-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-xs font-medium">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $comment->user->name }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $comment->comment }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                            No comments yet.
                        </p>
                    @endforelse
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
