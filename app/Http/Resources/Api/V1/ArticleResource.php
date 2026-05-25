<?php

namespace App\Http\Resources\Api\V1;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'category' => $this->category,
            'published_at' => $this->created_at->format('Y-m-d H:i'),

            'writer' => [
                'id' => $this->writer->id,
                'first_name' => $this->writer->first_name,
                'last_name' => $this->writer->last_name,
                'email' => $this->writer->email,
            ],

            // 'tags' => $this->whenLoaded('tags', function() {
            //     return $this->tags->map(function($tag) {
            //         return [
            //             'id' => $tag->id,
            //             'name' => $tag->name,
            //             'slug' => $tag->slug,
            //         ];
            //     });
            // }),

            'comments' => $this->whenLoaded('comments', function() {
                return $this->comments->map(function($comment) {
                    return [
                        'id' => $comment->id,
                        'body' => $comment->body,
                        'commented_by' => $comment->user->first_name . ' ' . $comment->user->last_name,
                        'created_at' => $comment->created_at->diffForHumans(),
                    ];
                });
            }),


        ];

        }
}
