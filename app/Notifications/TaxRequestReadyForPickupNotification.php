<?php

namespace App\Notifications;

use App\Models\TaxRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TaxRequestReadyForPickupNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public TaxRequest $taxRequest) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        return new FcmMessage(notification: new FcmNotification(
            title: 'Tax Request is marked as ready for pickup',
            body: 'Tax Request is marked as ready for pickup',
        ));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tax_request_id' => $this->taxRequest->id,
            'title' => 'Tax Request is marked as ready for pickup',
            'message' => 'Tax Request is marked as ready for pickup',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->view(
            'emails.tax_request_ready_for_pickup',
            ['taxRequest' => $this->taxRequest]
        );
    }
}
