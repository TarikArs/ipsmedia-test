<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase;

class AchievementsControllerTest extends TestCase
{
    use DatabaseTransactions;

    const FIRST_LESSON_WATCHED = 'First Lesson Watched';
    const FIVE_LESSONS_WATCHED = '5 Lessons Watched';
    const TEN_LESSONS_WATCHED = '10 Lessons Watched';
    const TWENTY_FIVE_LESSONS_WATCHED = '25 Lessons Watched';
    const FIFTY_LESSONS_WATCHED = '50 Lessons Watched';
    const FIRST_COMMENT_WRITTEN = 'First Comment Written';
    const THREE_COMMENTS_WRITTEN = '3 Comments Written';
    const FIVE_COMMENTS_WRITTEN = '5 Comments Written';
    const TEN_COMMENTS_WRITTEN = '10 Comments Written';
    const TWENTY_COMMENTS_WRITTEN = '20 Comments Written';
    const BEGINNER_BADGE = 'beginner';
    const INTERMEDIATE_BADGE = 'intermediate';
    const ADVANCED_BADGE = 'advanced';
    const MASTER_BADGE = 'master';

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    /**
     * @test
     */
    public function it_returns_404_when_user_not_found()
    {
        $response = $this->getJson("users/-1/achievements");
        $response->assertStatus(404);
    }
    /** @test */
    public function it_returns_achievements_for_current_user()
    {
        $response = $this->getJson("users/{$this->user->id}/achievements");
        $response->assertStatus(200);
    }
    /** @test */
    public function it_returns_correct_json_structure()
    {

        $response = $this->getJson("users/{$this->user->id}/achievements");

        $response->assertOk();
        $response->assertJsonStructure([
            'unlocked_achievements',
            'next_available_achievements',
            'current_badge',
            'next_badge',
            'remaing_to_unlock_next_badge'
        ]);
    }
    /** @test */
    public function it_returns_no_achievements_for_user_with_0_lessons_watched_and_0_comments_written()
    {

        $response = $this->getJson("users/{$this->user->id}/achievements");

        $response->assertOk();
        $response->assertExactJson([
            'unlocked_achievements' => [],
            'next_available_achievements' => [
                'lessons' => self::FIRST_LESSON_WATCHED,
                'comments' => self::FIRST_COMMENT_WRITTEN
            ],
            'current_badge' => self::BEGINNER_BADGE,
            'next_badge' => self::INTERMEDIATE_BADGE,
            'remaing_to_unlock_next_badge' => 4
        ]);
    }
    /** @test */
    public function it_returns_correct_json_structure_for_user_with_5_lessons_watched_and_0_comments_written()
    {
        $lesson = Lesson::factory()->count(5)->create();
        $this->user->lessons()->attach($lesson);

        $response = $this->getJson("users/{$this->user->id}/achievements");

        $response->assertOk();
        $response->assertExactJson([
            'unlocked_achievements' => [
                self::FIRST_LESSON_WATCHED,
                self::FIVE_LESSONS_WATCHED,
            ],
            'next_available_achievements' => [
                'comments' => self::FIRST_COMMENT_WRITTEN,
                'lessons' => self::TEN_LESSONS_WATCHED
            ],
            'current_badge' => self::BEGINNER_BADGE,
            'next_badge' => self::INTERMEDIATE_BADGE,
            'remaing_to_unlock_next_badge' => 2
        ]);
    }
    /**
     * @test
     */
    public function it_returns_correct_json_structure_for_user_with_10_lessons_watched_and_0_comments_written()
    {
        $lesson = Lesson::factory()->count(10)->create();
        $this->user->lessons()->attach($lesson);

        $response = $this->getJson("users/{$this->user->id}/achievements");

        $response->assertOk();
        $response->assertExactJson([
            'unlocked_achievements' => [
                self::FIRST_LESSON_WATCHED,
                self::FIVE_LESSONS_WATCHED,
                self::TEN_LESSONS_WATCHED,
            ],
            'next_available_achievements' => [
                'comments' => self::FIRST_COMMENT_WRITTEN,
                'lessons' => self::TWENTY_FIVE_LESSONS_WATCHED
            ],
            'current_badge' => self::BEGINNER_BADGE,
            'next_badge' => self::INTERMEDIATE_BADGE,
            'remaing_to_unlock_next_badge' => 1
        ]);
    }
    /** @test */
    public function it_returns_correct_json_structure_for_user_with_25_lessons_watched_and_0_comments_written()
    {
        $lesson = Lesson::factory()->count(25)->create();
        $this->user->lessons()->attach($lesson);

        $response = $this->getJson("users/{$this->user->id}/achievements");

        $response->assertOk();
        $response->assertExactJson([
            'unlocked_achievements' => [
                self::FIRST_LESSON_WATCHED,
                self::FIVE_LESSONS_WATCHED,
                self::TEN_LESSONS_WATCHED,
                self::TWENTY_FIVE_LESSONS_WATCHED,
            ],
            'next_available_achievements' => [
                'comments' => self::FIRST_COMMENT_WRITTEN,
                'lessons' => self::FIFTY_LESSONS_WATCHED
            ],
            'current_badge' => self::INTERMEDIATE_BADGE,
            'next_badge' => self::ADVANCED_BADGE,
            'remaing_to_unlock_next_badge' => 4
        ]);
    }
    /**
     * @test
     */
    public function it_returns_correct_json_structure_for_user_with_1_lesson_watched_and_1_comment_written()
    {
        $lesson = Lesson::factory()->count(1)->create();
        $this->user->lessons()->attach($lesson);

        $comment = 'This is a comment';
        Comment::factory()->create([
            'user_id' => $this->user->id,
            'body' => $comment
        ]);

        $response = $this->getJson("users/{$this->user->id}/achievements");

        $response->assertOk();
        $response->assertExactJson([
            'unlocked_achievements' => [
                self::FIRST_LESSON_WATCHED,
                self::FIRST_COMMENT_WRITTEN,
            ],
            'next_available_achievements' => [
                'lessons' => self::FIVE_LESSONS_WATCHED,
                'comments' => self::THREE_COMMENTS_WRITTEN
            ],
            'current_badge' => self::BEGINNER_BADGE,
            'next_badge' => self::INTERMEDIATE_BADGE,
            'remaing_to_unlock_next_badge' => 2
        ]);
    }
    /** @test */
    public function it_returns_correct_json_structure_for_user_with_5_lessons_watched_and_3_comments_written()
    {
        $lessons = Lesson::factory()->count(5)->create();
        $this->user->lessons()->attach($lessons);

        Comment::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("users/{$this->user->id}/achievements");

        $response->assertOk();
        $response->assertExactJson([
            'unlocked_achievements' => [
                self::FIRST_LESSON_WATCHED,
                self::FIVE_LESSONS_WATCHED,
                self::FIRST_COMMENT_WRITTEN,
                self::THREE_COMMENTS_WRITTEN,
            ],
            'next_available_achievements' => [
                'lessons' => self::TEN_LESSONS_WATCHED,
                'comments' => self::FIVE_COMMENTS_WRITTEN,
            ],
            'current_badge' => self::INTERMEDIATE_BADGE,
            'next_badge' => self::ADVANCED_BADGE,
            'remaing_to_unlock_next_badge' => 4,
        ]);
    }
    /** @test */
    public function it_returns_correct_json_structure_for_user_with_10_lessons_watched_and_5_comments_written()
    {
        $lessons = Lesson::factory()->count(10)->create();
        $this->user->lessons()->attach($lessons);

        Comment::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("users/{$this->user->id}/achievements");

        $response->assertOk();
        $response->assertExactJson([
            'unlocked_achievements' => [
                self::FIRST_LESSON_WATCHED,
                self::FIVE_LESSONS_WATCHED,
                self::TEN_LESSONS_WATCHED,
                self::FIRST_COMMENT_WRITTEN,
                self::THREE_COMMENTS_WRITTEN,
                self::FIVE_COMMENTS_WRITTEN,
            ],
            'next_available_achievements' => [
                'lessons' => self::TWENTY_FIVE_LESSONS_WATCHED,
                'comments' => self::TEN_COMMENTS_WRITTEN,
            ],
            'current_badge' => self::INTERMEDIATE_BADGE,
            'next_badge' => self::ADVANCED_BADGE,
            'remaing_to_unlock_next_badge' => 2,
        ]);
    }
    /** @test */
    public function it_returns_correct_json_structure_for_user_with_25_lessons_watched_and_10_comments_written()
    {
        $lessons = Lesson::factory()->count(25)->create();
        $this->user->lessons()->attach($lessons);

        Comment::factory()->count(10)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("users/{$this->user->id}/achievements");

        $response->assertOk();
        $response->assertExactJson([
            'unlocked_achievements' => [
                self::FIRST_LESSON_WATCHED,
                self::FIVE_LESSONS_WATCHED,
                self::TEN_LESSONS_WATCHED,
                self::TWENTY_FIVE_LESSONS_WATCHED,
                self::FIRST_COMMENT_WRITTEN,
                self::THREE_COMMENTS_WRITTEN,
                self::FIVE_COMMENTS_WRITTEN,
                self::TEN_COMMENTS_WRITTEN,
            ],
            'next_available_achievements' => [
                'lessons' => self::FIFTY_LESSONS_WATCHED,
                'comments' => self::TWENTY_COMMENTS_WRITTEN,
            ],
            'current_badge' => self::ADVANCED_BADGE,
            'next_badge' => self::MASTER_BADGE,
            'remaing_to_unlock_next_badge' => 2,
        ]);
    }
    /** @test */
    public function it_returns_correct_json_structure_for_user_with_50_lessons_watched_and_20_comments_written()
    {
        $lessons = Lesson::factory()->count(50)->create();
        $this->user->lessons()->attach($lessons);

        Comment::factory()->count(20)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("users/{$this->user->id}/achievements");

        $response->assertOk();
        $response->assertExactJson([
            'unlocked_achievements' => [
                self::FIRST_LESSON_WATCHED,
                self::FIVE_LESSONS_WATCHED,
                self::TEN_LESSONS_WATCHED,
                self::TWENTY_FIVE_LESSONS_WATCHED,
                self::FIFTY_LESSONS_WATCHED,
                self::FIRST_COMMENT_WRITTEN,
                self::THREE_COMMENTS_WRITTEN,
                self::FIVE_COMMENTS_WRITTEN,
                self::TEN_COMMENTS_WRITTEN,
                self::TWENTY_COMMENTS_WRITTEN,
            ],
            'next_available_achievements' => [],
            'current_badge' => self::MASTER_BADGE,
            'next_badge' => "",
            'remaing_to_unlock_next_badge' => 0,
        ]);
    }
}
