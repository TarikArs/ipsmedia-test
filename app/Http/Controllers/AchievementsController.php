<?php

namespace App\Http\Controllers;

use App\Models\User;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        try {
            $user->load('comments', 'lessons');
            $user_comments = $user->comments;
            $user_lessons = $user->lessons;
            $achievements = config('achievements');

            $nextAndUnlockedAchievements = $this->getNextAndUnlockedAchievements($user_comments, $user_lessons, $achievements);
            $current_badge = $this->getCurrentBadge($nextAndUnlockedAchievements['unlocked_achievements'], $achievements);
            $nextAvailableAchievements = $this->getNextAvailableAchievements($nextAndUnlockedAchievements['unlocked_achievements'], $achievements);

            return response()->json([
                'unlocked_achievements' => $nextAndUnlockedAchievements['unlocked_achievements'],
                'next_available_achievements' => $nextAndUnlockedAchievements['next_available_achievements'],
                'current_badge' => $current_badge,
                'next_badge' => $nextAvailableAchievements['next_badge'],
                'remaing_to_unlock_next_badge' => $nextAvailableAchievements['remaining_to_unlock_next_badge']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
    }
    private function getNextAndUnlockedAchievements($user_comments, $user_lessons, $achievements): array
    {
        $unlocked_achievements = [];
        $lessons_achievements = $achievements['lessons_watched'];
        $comments_achievements = $achievements['comments_written'];

        $nbr_of_watched_lessons = $user_lessons ? $user_lessons->count() : 0;
        $nbr_of_comments = $user_comments ? $user_comments->count() : 0;
        $next_available_achievements = [];

        foreach ($lessons_achievements as $nbr => $achievement) {
            if ($nbr_of_watched_lessons >= $nbr) {
                $unlocked_achievements[] = $achievement;
            } elseif (empty($next_available_achievements['lessons'])) {
                $next_available_achievements['lessons'] = $achievement;
            }
        }
        foreach ($comments_achievements as $nbr => $achievement) {
            if ($nbr_of_comments >= $nbr) {
                $unlocked_achievements[] = $achievement;
            } elseif (empty($next_available_achievements['comments'])) {
                $next_available_achievements['comments'] = $achievement;
            }
        }
        return [
            'unlocked_achievements' => $unlocked_achievements,
            'next_available_achievements' => $next_available_achievements
        ];
    }
    private function getCurrentBadge($unlocked_achievements, $achievements): string
    {
        $current_badge = '';
        foreach ($achievements['badges'] as $requiredAchievementCount => $badgeName) {
            if (count($unlocked_achievements) >= $requiredAchievementCount) {
                $current_badge = $badgeName;
            }
        }
        return $current_badge;
    }
    private function getNextAvailableAchievements($unlocked_achievements, $achievements): array
    {
        $next_badge = '';
        $remaining_to_unlock_next_badge = 0;

        foreach ($achievements['badges'] as $requiredAchievementCount => $badgeName) {
            if (count($unlocked_achievements) < $requiredAchievementCount) {
                $next_badge = $badgeName;
                $remaining_to_unlock_next_badge = $requiredAchievementCount - count($unlocked_achievements);
                break;
            }
        }
        return [
            'next_badge' => $next_badge,
            'remaining_to_unlock_next_badge' => $remaining_to_unlock_next_badge
        ];
    }
}
