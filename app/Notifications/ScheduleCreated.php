<?php

namespace App\Notifications;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ScheduleCreated extends Notification
{
    use Queueable;

    public function __construct(public Schedule $schedule) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Schedule Created — '.config('app.name'))
            ->view('emails.schedule-created', [
                'schedule' => $this->schedule,
                'user' => $notifiable,
            ]);
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        $start = optional($this->schedule->scheduled_start_time)->toDateTimeString();
        $title = 'New Schedule Created';
        $body = sprintf('Schedule #%d: %s — %s', $this->schedule->id, $start, $this->schedule->type);

        return new FcmMessage(notification: new FcmNotification(
            title: $title,
            body: $body,
        ));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Schedule Created',
            'message' => 'A new schedule was created.',
            'schedule_id' => $this->schedule->id,
            'scheduled_start_time' => $this->schedule->scheduled_start_time,
            'scheduled_end_time' => $this->schedule->scheduled_end_time,
        ];
    }
}
