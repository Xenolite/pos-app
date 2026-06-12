<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Mail\DailyReportMail;
class SendDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $setting = Setting::first();

    if (!$setting || !$setting->report_enabled) {
        return;
    }

    Mail::to($setting->report_email)
        ->send(new DailyReportMail());

    $this->info('Daily report sent!');
    }
}
