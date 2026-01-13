<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CloseOverdueVisits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-overdue-visits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close visits that are still active from the previous day or overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \App\Models\Visit::where('status', 'active')
            ->update([
                'status' => 'auto_closed',
                'check_out_at' => now()->startOfDay()->subSecond() // 23:59:59 yesterday if run at midnight
            ]);

        // Or if run exactly at 23:59
        // 'check_out_at' => now()

        $this->info("Closed {$count} active visits.");
    }
}
