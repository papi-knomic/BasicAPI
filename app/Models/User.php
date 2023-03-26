<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profilePicture() : HasOne
    {
        return $this->hasOne( ProfilePicture::class );
    }

    public function posts() : HasMany
    {
        return $this->hasMany( Post::class, );
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('username', $value)->orWhere('id', $value)->firstOrFail();
    }

    public function likes()
    {
        return $this->belongsToMany(Post::class, 'post_user')->withTimestamps()->wherePivot('liked', true);
    }

    /**
     * The posts that the user dislikes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dislikes()
    {
        return $this->belongsToMany(Post::class, 'post_user')->withTimestamps()->wherePivot('disliked', true);
    }

    /**
     * Determine if the user has liked the given post.
     *
     * @param  Post  $post
     * @return bool
     */
    public function hasLiked(Post $post)
    {
        return $this->likes()->where('post_id', $post->id)->exists();
    }

    /**
     * Determine if the user has disliked the given post.
     *
     * @param  Post  $post
     * @return bool
     */
    public function hasDisliked(Post $post)
    {
        return $this->dislikes()->where('post_id', $post->id)->exists();
    }

    /**
     * Like the given post.
     *
     * @param  Post  $post
     * @return void
     */
    public function like(Post $post)
    {
        if (!$this->hasLiked($post)) {
            $this->likes()->attach($post, ['liked' => true, 'disliked' => false]);
            $this->removeDislike($post);
        }
    }

    /**
     * Dislike the given post.
     *
     * @param  Post  $post
     * @return void
     */
    public function dislike(Post $post)
    {
        if (!$this->hasDisliked($post)) {
            $this->dislikes()->attach($post, ['liked' => false, 'disliked' => true]);
            $this->likes()->detach($post);
            $this->removeLike($post);
        }
    }

    /**
     * Remove the like from the given post.
     *
     * @param  Post  $post
     * @return void
     */
    public function removeLike(Post $post)
    {
        $this->likes()->detach($post);
    }

    /**
     * Remove the dislike from the given post.
     *
     * @param  Post  $post
     * @return void
     */
    public function removeDislike(Post $post)
    {
        $this->dislikes()->detach($post);
    }

}
