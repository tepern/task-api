<?php

namespace App\Http\Resources;

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
            'owner' => UserResource::make($this->ownerId),
            'assignee' => UserResource::make($this->assigneeId),
            'endTask' => $this->endTask,
            'finishedAt' => $this->finished_at,
            'createdAt' => $this->created_at,
            'updatedAt' =>$this->updated_at
        ];
    }
}
