<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'excerpt' => $this->excerpt,
            'tags' => $this->tags,
            'slug' => $this->slug,
            'creator' => $this->user->username,
            'views_count' => $this->views_count,
            'likes_count' => count($this->likes),
            'dislikes_count' => count($this->dislikes),
            'liked_by_user' => $this->likedByUser($request->user()),
            'disliked_by_user' => $this->dislikedByUser($request->user()),
            'comments' => $request->user() ? CommentResource::collection( $this->comments ) : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    private function likedByUser($user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->likes->contains(function ($item) use ($user) {
            return $item->id === $user->id;
        });
    }

    private function dislikedByUser($user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->dislikes->contains(function ($item) use ($user) {
            return $item->id === $user->id;
        });
    }
}
