<?php

namespace App\Jobs;

use App\Actions\Sunday\CreateTaskFromGmail;
use App\Models\Automation;
use Google_Service_Gmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessGmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $automation;
    protected $gmailThreads;
    protected $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Automation $automation, Array $gmailThreads)
    {
        $this->automation = $automation;
        $this->gmailThreads = $gmailThreads;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CreateTaskFromGmail::create($this->automation, $this->gmailThreads);
    }
}
