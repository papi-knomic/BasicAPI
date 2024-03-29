<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Filters the post
     * @param $query
     * @param array $filters
     * @param string $sort
     * @return void
     */
    public function scopeFilter($query, array $filters, string $sort = 'latest' )
    {
        $query->when( $sort === 'popular', function ($query) {
            $query->orderBy('views_count', 'desc');
        }, function ($query) {
            $query->latest('created_at');
        } );


        $query->when($filters['search'] ?? false, function ($query) use($filters) {
            $query->where('tags', 'like', '%' . $filters['tag'] . '%');
        } );

        $query->when($filters['search'] ?? false, function ($query) use($filters) {
            $query->where('title', 'like', '%' . $filters['search'] . '%')
                ->orWhere('body', 'like', '%' . $filters['search'] . '%')
                ->orWhere('tags', 'like', '%' . $filters['search'] . '%');
        } );

    }

    /**
     * Get the users that have liked the post.
     */
    public function likes() : BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('liked')->wherePivot('liked', true);
    }

    /**
     * Get the users that have disliked the post.
     */
    public function dislikes() : BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('disliked')->wherePivot('disliked', true);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('slug', $value)->orWhere('id', $value)->firstOrFail();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc')->whereNull('parent_id');
    }

    public function notifiable()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachments() : HasMany
    {
        return $this->hasMany(PostAttachment::class);
    }
}
