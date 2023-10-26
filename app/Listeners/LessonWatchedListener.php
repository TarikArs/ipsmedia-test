<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LessonWatchedListener
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
        $user = $event->user;
        $lesson = $event->lesson;
        $user_already_watche_lesson = $user->watched->contains($lesson);
        /** if the user already watched the video no action needed */
        if ($user_already_watche_lesson) return;
        /** if the user didn't watch the video we attach it to the user */
        $user->watched()->attach($lesson, ['watched' => true]);
        /** we check if the user unlocked an achievement */
        $nbr_of_watched_lessons = $user->watched()->count();
        $lessons_acheivements = config('achievements.lessons_watched');
        $achievemnt = $lessons_acheivements[$nbr_of_watched_lessons] ?? null;
        /** if the user unlocked an achievement we fire an event */
        if ($achievemnt)
            event(new \App\Events\AchievementUnlocked($user, $achievemnt));
    }
}
