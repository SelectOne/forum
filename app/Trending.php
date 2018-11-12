<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/26
 * Time: 10:08
 */

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    /**
     * 取出热门话题
     * @return array
     */
    public function get()
    {
        return array_map('json_decode',Redis::zrevrange($this->cacheKey(),0,4));
    }

    /**
     * 缓存热门话题
     * @param $thread
     */
    public function push($thread)
    {
        Redis::zincrby($this->cacheKey(),1,json_encode([
            'title' => $thread->title,
            'path' => $thread->path()
        ]));
    }

    /**
     * 判断当前应用程序使用不同的键
     * @return string
     */
    public function cacheKey()
    {
        return app()->environment('string') ? 'testing_trending_threads' : 'trending_threads';
    }

    /**
     * 清空缓存
     */
    public function rest()
    {
        Redis::del($this->cacheKey());
    }
}