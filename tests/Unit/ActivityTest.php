<?php

namespace Tests\Unit;

use App\Activity;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActivityTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_records_activity_when_a_thread_is_created()
    {
        $this->singIn();

        $thread = create('App\Thread');

        $this->assertDatabaseHas('activities', [
            'type'  => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => 'App\Thread'
        ]);

        $activity = Activity::first(); // 当前测试中，表里只存在一条记录

        $this->assertEquals($activity->subject->id,$thread->id);
    }

    /** @test */
    public function it_records_activity_when_a_reply_is_created()
    {
        $this->singIn();

        $reply = create('App\Reply');

        $this->assertEquals(2,Activity::count());
    }

    /** @test */
    public function it_fetches_a_feed_for_any_user()
    {
        // Given we have a thread
        $this->singIn();

        // And another thread from a week ago
        create('App\Thread', [
           'user_id' => auth()->id()
        ], 2);

        auth()->user()->activity()->first()->update(['created_at'=>Carbon::now()->subWeek()]);

        // When we fetch their feed
        $feed = Activity::feed(auth()->user());

//        dd($feed->toArray());

        // Then,it should be returned in the proper format.
        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('Y-m-d')
        ));

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->subWeek()->format('Y-m-d')
        ));
    }
}
