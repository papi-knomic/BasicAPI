<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = $this->checkData($this->data);
        return [
            'id' => $this->id,
            'type' => $this->cleanType($this->type),
            'message' => $data->message,
            'user' => $data->user->username,
            'created_at' => $this->created_at,
            'read' => (bool)$this->read_at
        ];
    }

    private function cleanType(string $type) : string
    {
        return str_replace("App\\Notifications\\", "", $type);
    }

    private function checkData( $data )
    {
        if ( is_array( $data ) ) {
            return json_decode(json_encode($data));
        }
        return json_decode($data);
    }
}
