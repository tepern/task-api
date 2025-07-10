<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'name' => $this->name,
            'description' => $this->description,
            'owner' => UserResource::make($this->owner),
            'assignee' => UserResource::make($this->assignee),
            'endTask' => $this->endTask,
            'finishedAt' => $this->finished_at,
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'updatedAt' =>$this->updated_at
        ];
    }
}
