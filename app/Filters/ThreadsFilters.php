<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/11
 * Time: 16:17
 */

namespace App\Filters;

use App\User;

class ThreadsFilters extends Filters
{
    protected $filters = ['by', 'popularity', 'unanswered'];

    /**
     * 用户名作为条件
     * @param $username
     * @return mixed
     */
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    /**
     * 评论数作为条件
     * @return mixed
     */
    public function popularity()
    {
        $this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count','desc');
    }

    /**
     * 零回复的帖子
     * @return mixed
     */
    public function unanswered()
    {
        return $this->builder->where('replies_count',0);
    }
}