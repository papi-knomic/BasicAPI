<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

}
