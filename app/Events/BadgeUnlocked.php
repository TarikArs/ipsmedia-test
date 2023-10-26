<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BadgeUnlocked
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $user;
    public $badge;
    public function __construct(User $user, string $badge)
    {
        $this->user = $user;
        $this->badge = $badge;
    }

}
