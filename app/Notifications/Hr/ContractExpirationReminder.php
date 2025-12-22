<?php

namespace App\Notifications\Hr;

use App\Models\Hr\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractExpirationReminder extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Employee $employee,
        public readonly int $daysUntilExpiration
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Contract Expiration Reminder')
            ->line('An employee contract is nearing expiration.')
            ->line('Employee ID: ' . $this->employee->id)
            ->line('Days until expiration: ' . $this->daysUntilExpiration);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'employee_id' => $this->employee->id,
            'days_until_expiration' => $this->daysUntilExpiration,
            'contract_end_date' => optional($this->employee->contract_end_date)->toDateString(),
        ];
    }
}
