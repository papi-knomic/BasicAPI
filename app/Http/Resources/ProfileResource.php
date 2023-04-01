<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email,
            "username" => $this->username,
            "profile_picture" => $this->profilePicture->url ?? null,
            "bio" => $this->bio,
            "following" => $this->followsUser(),
            "follows_you" => $this->userFollows()
        ];
    }

    private function followsUser() : bool
    {
        if ( !auth()->id() || $this->id === auth()->id() ) {
            return false;
        }

        return $this->isFollowedByUser( auth()->id());
    }

    private function userFollows() : bool
    {
        if ( !auth()->id() ) {
            return false;
        }

        return auth()->user()->isFollowedByUser( $this->id );
    }
}
