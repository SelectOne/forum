<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/26
 * Time: 15:01
 */
namespace App;

use Illuminate\Support\Facades\Redis;

class Visits
{
    protected $thread;

    public function __construct($thread)
    {
        $this->thread = $thread;
    }


    public function cacheKey()
    {
        return "threads.{$this->thread->id}.visits";
    }

    /**
     * @return $this
     */
    public function record()
    {
        Redis::incr($this->cacheKey());

        return $this;
    }

    /**
     * 清空缓存
     * @return $this
     */
    public function reset()
    {
        Redis::del($this->cacheKey());

        return $this;
    }

    /**
     * 获得记录数
     * @return int
     */
    public function count()
    {
        return Redis::get($this->cacheKey()) ?: 0;
    }
}