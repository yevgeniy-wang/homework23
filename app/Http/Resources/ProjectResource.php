<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $labels = $this->labels()->distinct()->pluck('name');

        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'labels' => $labels,
        ];
    }
}
