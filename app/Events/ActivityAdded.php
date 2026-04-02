<?php

namespace App\Events;

use App\Models\Activity;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Activity $activity)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'activity.added';
    }

    public function broadcastWith(): array
    {
        return [
            'activity' => [
                'id' => $this->activity->activity_id,
                'user_name' => $this->activity->user?->lname_user . ', ' . $this->activity->user?->fname_user,
                'activity_at' => $this->activity->activity_at?->timezone(config('app.timezone'))->format('Y-m-d H:i'),
                'facility_used' => $this->activity->facility_used,
                'service_type' => $this->activity->service_type,
            ],
        ];
    }
}
