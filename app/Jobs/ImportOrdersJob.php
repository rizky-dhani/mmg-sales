<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportOrdersJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $filePath,
        public int $userId,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Implementation in Phase 3 & 4
    }
}