<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Duty;

class UpdateDutyStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duty:update-duty-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of duties based on current time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Duty::all()->each(function ($duty) {
            $duty->updateDutyStatus(); // Call a separate method to handle the update logic
        });

        $this->info('Duty statuses updated successfully.');
    }
}
