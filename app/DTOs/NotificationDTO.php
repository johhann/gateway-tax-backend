<?php

namespace App\DTOs;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NotificationDTO
{
    public static function format(DatabaseNotification|Collection|LengthAwarePaginator $input): array
    {
        if ($input instanceof LengthAwarePaginator) {

            return [
                'data' => $input->getCollection()
                    ->map(fn (DatabaseNotification $notification) => self::transform($notification))
                    ->toArray(),
                'meta' => [
                    'total' => $input->total(),
                    'per_page' => $input->perPage(),
                    'current_page' => $input->currentPage(),
                    'last_page' => $input->lastPage(),
                ],
            ];
        }

        if ($input instanceof Collection) {
            return $input
                ->map(fn (DatabaseNotification $notification) => self::transform($notification))
                ->toArray();
        }

        return self::transform($input);
    }

    private static function transform(DatabaseNotification $notification): array
    {
        return [
            'id' => $notification->id,
            'data' => $notification->data,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
        ];
    }
}
