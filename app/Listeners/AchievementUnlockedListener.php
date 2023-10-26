<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AchievementUnlockedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // if user unlocks enough achievement to earn a new badge a BadgeUnlocked event must be fired with a payload of:badge_name (string) user (User Model)
        $user = $event->user;
        /** count the achivemnt from comments and lessions and check if a badge is unlocked */
        $nbr_of_watched_lessons = $user->watched()->count();
        $nbr_of_comments = $user->comments()->count();
        $badges = config('achievements.badges');
        if ($nbr_of_watched_lessons + $nbr_of_comments == 0) return;
        $badge = $badges[$nbr_of_watched_lessons + $nbr_of_comments] ?? null;
        /** if the user unlocked a badge we fire an BadgeUnlocked */
        if ($badge)
            event(new \App\Events\BadgeUnlocked($user, $badge));
    }
}
