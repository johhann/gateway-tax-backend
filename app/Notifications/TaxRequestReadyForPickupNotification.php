<?php

namespace App\Notifications;

use App\Models\TaxRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

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
        return ['database'];
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
}
