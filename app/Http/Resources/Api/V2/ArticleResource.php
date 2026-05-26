<?php
namespace App\Http\Resources\Api\V2;
use App\Http\Resources\Api\V1\ArticleResource as ArticleResourceV1;
use Illuminate\Http\Request;

class ArticleResource extends ArticleResourceV1
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
          return array_merge(parent::toArray($request), [
            //add in v2
            'reading_time' => $this->reading_time . ' mins',
            'comments_count'=>$this->comments_count ?? 0,

                'tags' => $this->whenLoaded('tags', function() {
                return $this->tags->map(function($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                    ];
                });
            }),

            ]);

        }




}
