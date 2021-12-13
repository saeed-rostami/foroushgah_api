<?php

namespace App\Http\Resources\Content;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'body' => $this->body,
            'seen' => $this->seen,
            'approved' => $this->approved,
            'approved_text' => $this->approved == 1 ? 'تایید شده' : 'تایید نشده',
            'status' => $this->status,
            'status_text' => $this->status == 1 ? 'فعال' : 'غیرفعال',
            'parent_id' => $this->parent_id,
            'commentable' => $this->commentable->title ?? '',
            'user_name' => $this->author->fillName ?? 'بعدا انجام بده!',
        ];
    }
}
