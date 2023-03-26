<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'id'  => $this->id,
            'body' => $this->body,
            'children' => CommentResource::collection( $this->children ),
            'creator' => $this->user->username,
            'created_at' => $this->created_at
        ];
    }
}
