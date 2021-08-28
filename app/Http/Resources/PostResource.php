<?php

namespace App\Http\Resources;

use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'summary' => $this->summary,
            'status' => $this->status,
            'image' => $this->image,
            'tags' => $this->tags,
            'commentable' => $this->commentable,
            'published_at' => $this->published_at,
            'category_id' => $this->category_id,
            'status_text' => $this->status == 1 ? 'فعال' : 'غیرفعال',
            'commentable_text' => $this->commentable == 1 ? 'فعال' : 'غیرفعال',
            'category_text' => $this->category_text,
            'published_text' => $this->published_text,
            'published' => $this->published,
        ];
    }
}
