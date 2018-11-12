<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/13
 * Time: 11:43
 */

namespace App\Traits;


use App\Favorite;
use Illuminate\Database\Eloquent\Model;

trait Favoritable
{
    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    /**
     * 多态关联
     * @return mixed
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class,'favorited');
    }

    /**
     * 点赞
     * @return Model
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];
        // 每个用户只能点赞一条评论或者文章
        if( ! $this->favorites()->where($attributes)->exists()){
            return $this->favorites()->create($attributes);
        }
    }

    /**
     * 取消点赞
     */
    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        $this->favorites()->where($attributes)->get()->each->delete();
    }

    /**
     * 检查评论或者文章是否已被当前用户点赞
     * @return bool
     */
    public function isFavorited()
    {
//        return $this->favorites()->where('user_id', auth()->id())->exists();
        return !! $this->favorites->where('user_id', auth()->id())->count();
    }

    /**
     * 是否点过赞
     * @return bool
     */
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    /**
     * 获得点赞的数量
     * @return mixed
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

}