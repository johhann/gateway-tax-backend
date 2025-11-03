<?php

namespace App\Http\Controllers;

use App\DTOs\NotificationDTO;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request
            ->user()
            ->notifications()
            ->when($request->filled('type') && $request->type == 'unread', fn ($query) => $query->where('read_at', null))
            ->when($request->filled('type') && $request->type == 'read', fn ($query) => $query->where('read_at', '!=', null))
            ->orderByDesc('created_at')
            ->paginate(perPage: $request->limit ?? 10, page: $request->page ?? 1);

        return NotificationDTO::format($notifications);
    }

    public function markAsRead(Request $request, $notification)
    {
        $notification = $request
            ->user()
            ->notifications()
            ->findOrFail($notification);

        $notification->markAsRead();

        return NotificationDTO::format($notification);
    }

    public function markAllRead(Request $request)
    {
        $notifications = $request
            ->user()
            ->unreadNotifications()
            ->get();

        $notifications->markAsRead();

        return NotificationDTO::format($notifications);
    }

    public function counter(Request $request): array
    {
        return [
            'unread_count' => $request
                ->user()
                ->unreadNotifications()
                ->count(),
        ];
    }
}
