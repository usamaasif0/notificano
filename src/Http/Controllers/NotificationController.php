<?php

namespace Notificano\Http\Controllers;

use Notificano\Models\Notification;
use Notificano\Events\NotificationMarkedAsRead;
use Notificano\Events\NotificationCountUpdated;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!Schema::hasTable('notifications')) {
                return view('notificano::notifications.index', [
                    'notifications' => collect(),
                    'totalNotificationCount' => 0,
                    'unreadNotificationCount' => 0,
                    'readNotificationCount' => 0,
                    'tableAvailable' => false,
                ]);
            }

            $user = auth()->user();
            if (!$user) {
                return redirect()->route('login');
            }

            $totalNotificationCount = $user->notifications()->count();
            $unreadNotificationCount = $user->notifications()->whereNull('read_at')->count();
            $readNotificationCount = $totalNotificationCount - $unreadNotificationCount;

            $notifications = $user->notifications()->latest()->get();

            $tableAvailable = true;

            if ($request->ajax()) {
                $notificationsArray = $notifications->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title ?: ucfirst($notification->type),
                        'message' => $notification->message,
                        'icon' => $notification->icon ?: 'fa-circle-info',
                        'is_read' => $notification->is_read,
                        'created_at' => $notification->created_at->diffForHumans(),
                    ];
                });

                return response()->json([
                    'notifications' => $notificationsArray,
                    'unread_count' => $unreadNotificationCount,
                ]);
            }

            return view('notificano::notifications.index', compact('notifications', 'totalNotificationCount', 'unreadNotificationCount', 'readNotificationCount', 'tableAvailable'));
        } catch (\Throwable $e) {
            \Log::error('NotificationController@index error: ' . $e->getMessage());
            return view('notificano::notifications.index', [
                'notifications' => collect(),
                'totalNotificationCount' => 0,
                'unreadNotificationCount' => 0,
                'readNotificationCount' => 0,
                'tableAvailable' => false,
            ]);
        }
    }

    public function markAsRead(Notification $notification)
    {
        try {
            if ($notification->to_user !== auth()->id()) {
                abort(403);
            }

            $notification->update(['read_at' => now()]);

            $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();

            broadcast(new NotificationMarkedAsRead(auth()->id(), $notification->id));
            broadcast(new NotificationCountUpdated(auth()->id(), $unreadCount));

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'notification_id' => $notification->id,
                'unread_count' => $unreadCount
            ]);
        } catch (\Throwable $e) {
            \Log::error('NotificationController@markAsRead error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read.'
            ], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.'
                ], 401);
            }

            $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);

            broadcast(new NotificationMarkedAsRead($user->id, null, true));
            broadcast(new NotificationCountUpdated($user->id, 0));

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Throwable $e) {
            \Log::error('NotificationController@markAllAsRead error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read.'
            ], 500);
        }
    }
} 