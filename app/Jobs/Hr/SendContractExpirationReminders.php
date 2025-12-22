<?php

namespace App\Jobs\Hr;

use App\Models\Hr\Employee;
use App\Models\User;
use App\Notifications\Hr\ContractExpirationReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendContractExpirationReminders implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const NOTICE_WINDOW_DAYS = 30;

    public function handle(): void
    {
        $start = now()->startOfDay();
        $end = now()->addDays(self::NOTICE_WINDOW_DAYS)->endOfDay();

        $employees = Employee::query()
            ->whereNotNull('contract_end_date')
            ->whereBetween('contract_end_date', [$start, $end])
            ->get();

        if ($employees->isEmpty()) {
            return;
        }

        $recipients = User::role(['superadmin', 'hr_admin', 'hr_manager'])->get();
        if ($recipients->isEmpty()) {
            return;
        }

        foreach ($employees as $employee) {
            $daysUntil = $start->diffInDays($employee->contract_end_date, false);
            Notification::send($recipients, new ContractExpirationReminder($employee, $daysUntil));
        }
    }
}
