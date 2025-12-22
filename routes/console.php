<?php

use App\Jobs\Hr\SendContractExpirationReminders;
use App\Jobs\Hr\SendOnboardingDelayAlerts;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SendContractExpirationReminders())->daily();
Schedule::job(new SendOnboardingDelayAlerts())->daily();
