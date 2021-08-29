<?php

namespace App\Http\Resources\Content;

use Illuminate\Http\Resources\Json\JsonResource;

class FAQResource extends JsonResource
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
            'question' => $this->question,
            'answer' => $this->answer,
            'tags' => $this->tags,
            'slug' => $this->slug,
            'status' => $this->status,
            'status_text' => $this->status == 1 ? 'فعال' : 'غیرفعال'

        ];
    }
}
