<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommentWrittenListener
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
        //
        $comment = $event->comment;
        $user = $comment->user;
        $nbr_of_comments = $user->comments()->count();
        $comments_acheivements = config('achievements.comments_written');
        $achievemnt = $comments_acheivements[$nbr_of_comments] ?? null;
        /** if the user unlocked an achievement we fire an event */
        if ($achievemnt)
            event(new \App\Events\AchievementUnlocked($user, $achievemnt));
    }
}
