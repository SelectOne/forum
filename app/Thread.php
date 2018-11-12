<?php

namespace App;

use App\Events\ThreadReceivedNewReply;
use App\Notifications\ThreadWasUpdated;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Thread extends Model
{
    use RecordsActivity, Searchable;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    protected $casts = [
        'locked' => 'boolean'
    ];

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * 属性修改器
     * @param $value
     */
    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);

        if (static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-" . $this->id;
        }

        $this->attributes['slug'] = $slug;
    }

//    public function incrementSlug($slug)
//    {
//        // 取出最大 id 话题的 Slug 值
//        $max = static::whereTitle($this->title)->latest('id')->value('slug');
//
//        // 如果最后一个字符为数字
//        if (is_numeric($max[-1])) {
//            // 正则匹配出末尾的数字，然后自增 1
//            return preg_replace_callback('/(\d+)$/', function ($matches) {
//               return $matches[1]+1;
//            },$max);
//        }
//
//        // 否则后缀数字为 2
//        return "{$slug}-2";
//    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        // 全局作用域
//        static::addGlobalScope('replyCount', function ($builder) {
//            $builder->withCount('replies');
//        });

        // 监听模型删除事件
        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });

        static::created(function ($thread) {
            $thread->update([
                'slug' => $thread->title,
                'body' => clean($thread->body,'thread_or_reply_body')
            ]);
        });

        // 监听模型创建事件
//        static::created(function ($thread) {
//            $thread->recordActivity('created');
//        });
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');   // 使用 user_id 字段进行模型关联
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id',auth()->id())
            ->exists();
    }

    /**
     * 添加回复
     * @param $reply
     * @return Model
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    /**
     * 消息通知
     * @param $reply
     */
    public function notifySubscribers($reply)
    {
        $this->subscriptions
            ->where('user_id','!=',$reply->user_id)
            ->each
            ->notify($reply);
    }

    /**
     * 订阅
     * @param null $userId
     * @return $this
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    /**
     * 取消订阅
     * @param null $userId
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id',$userId ?: auth()->id())
            ->delete();
    }

    /**
     * 判断话题是否被更新
     * @param $user
     * @return bool
     * @throws \Exception
     */
    public function hasUpdatesFor($user)
    {
        // Look in the cache for the proper key
        // compare that carbon instance with the $thread->updated_at

        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    //浏览话题
    public function visits()
    {
        return new Visits($this);
    }

    /**
     * 添加最佳回复
     * @param Reply $reply
     */
    public function markBestReply(Reply $reply)
    {
        $this->update(['best_reply_id' => $reply->id]);
    }

    public function toSearchableArray()
    {
        return $this->toArray() + ['path' => $this->path()];
    }
}