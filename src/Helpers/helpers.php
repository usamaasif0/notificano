<?php

use Notificano\Events\NewNotification;
use Notificano\Events\NotificationCountUpdated;
use Notificano\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


if (!function_exists('setNotification')) {
    function setNotification(array $data, ?int $to_user = null): void
    {
        try {
            $to_user = !empty($to_user) ? $to_user : 1;
            $from_user = Auth::id();

            if (!$from_user) {
                return;
            }

            $notification = Notification::create([
                'from_user' => $from_user,
                'to_user' => $to_user,
                'title' => $data['title'] ?? null,
                'url' => null,
                'read_at' => null,
            ]);

            $data['id'] = $notification->id;

            $unreadCount = Notification::where('to_user', $to_user)
                ->whereNull('read_at')
                ->count();

            broadcast(new NewNotification($data));
            broadcast(new NotificationCountUpdated($to_user, $unreadCount));
        } catch (\Throwable $e) {
            // Optionally log the error
            \Log::error('setNotification error: ' . $e->getMessage());
        }
    }
}

if (!function_exists('getImage')) {
    function getImage($path)
    {
        try {
            $defaultImage = asset('images/no_avatar.webp');

            if (!$path) {
                return $defaultImage;
            }

            if (Storage::exists($path)) {
                return Storage::url($path);
            }

            return $defaultImage;
        } catch (\Throwable $e) {
            \Log::error('getImage error: ' . $e->getMessage());
            return asset('images/no_avatar.webp');
        }
    }
}
