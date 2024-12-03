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
            'project_id' => $this->project_id,
            'name' => $this->name,
            'priority' => $this->priority,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'project' => $this->project,
        ];
    }
}
