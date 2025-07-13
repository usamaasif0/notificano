<?php

namespace Notificano\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $notification;
    public string $toUser;

    public function __construct(array $notification)
    {
        $this->notification = $notification;
        $this->toUser = $notification['to_user'];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.' . $this->toUser),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification';
    }

    public function broadcastWith(): array
    {
        $this->notification['avatar'] = getImage('storage/avatars/' . auth()->user()->avatar);
        $this->notification['from_user'] = auth()->user()->name;
        return $this->notification;
    }
}