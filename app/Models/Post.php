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

    public function scopeFilter( $query, array $filters, $sort = 'popular' )
    {
        switch ($sort) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'latest':
            default:
                $query->latest('updated_at');
                break;
        }

        if($filters['tag'] ?? false){
            $query->where('tags', 'like', '%' . request('tag') . '%');
        }
        if($filters['search'] ?? false){
            $query->where('title', 'like', '%' . request('search') . '%')
                ->orWhere('body', 'like', '%' . request('search') . '%')
                ->orWhere('tags', 'like', '%' . request('search') . '%');
        }
    }

}
