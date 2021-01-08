<?php

namespace App\Jobs;

use App\Actions\Sunday\CreateTaskFromCalendar;
use App\Models\Automation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCalendar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $automation;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Automation $automation)
    {
        $this->automation = $automation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CreateTaskFromCalendar::create($this->automation);
    }
}
