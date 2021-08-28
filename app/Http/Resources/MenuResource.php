<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'status' => $this->status,
            'parent_id' => $this->parent_id,
            'parent_text' => $this->mainMenu->name ?? 'null',
            'status_text' => $this->status == 1 ? 'فعال' : 'غیرفعال'
        ];
    }
}
