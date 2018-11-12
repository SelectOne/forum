<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {
        $this->get('/threads')
             ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {
        $this->get($this->thread->path())
             ->assertSee($this->thread->title);
    }

    /** @test */
//    public function a_user_can_read_replies_that_are_associated_with_a_thread()
//    {
//        // 如果存在 Thread
//        // 并且该 Thread 拥有回复
//        $reply = create('App\Reply', ['thread_id' => $this->thread->id]);
//        // 那么当我们看该 Thread 时
//        // 我们也要看到回复
//        $this->get($this->thread->path())
//            ->assertSee($reply->body);
//    }

    /** @test */
    public function a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/'.$channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_any_username()
    {
        $this->singIn(create('App\User', ['name' => 'NoNo1']));

        $threadByNoNo1 = create('App\Thread', ['user_id' => auth()->id()]);

        $threadNotByNoNo1 = create('App\Thread');

        $this->get('threads?by=NoNo1')
            ->assertSee($threadByNoNo1->title)
            ->assertDontSee($threadNotByNoNo1->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        // Given we have three threads
        // With 2 replies,3 replies,0 replies, respectively
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id'=>$threadWithTwoReplies->id], 3);

        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id'=>$threadWithTwoReplies->id], 2);

        $threadWithNoReplies = $this->thread;

        // When I filter all threads by popularity
        $response = $this->getJson('threads?popularity=1')->json();

        // Then they should be returned from most replies to least.
        $this->assertEquals([3,2,0],array_column($response['data'], 'replies_count'));
    }

}