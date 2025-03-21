<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $user_id;
    public $notification;

    public function __construct($user_id, $notification)
    {
        $this->user_id = $user_id;
        $this->notification = $notification;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user_id);
    }

    public function broadcastAs()
    {
        return 'create';
    }

    public function broadcastWith() :array
    {
        return [
            'message' => "[{$this->notification->created_at}] new notification received by user {$this->user_id}: {$this->notification->message}",
            'notification' => $this->notification, // Anda bisa menyertakan seluruh objek notifikasi jika diperlukan
        ];
    }
}
