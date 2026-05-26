<?php

namespace App\Http\Requests\Articles;
use App\Enums\ArticleCategory;
use App\Models\Article;
use App\Rules\NotOffensive;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateArticle extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
public function authorize(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

      

        $article = $this->route('article');

        return $user->role === 'writer' && $article && $user->id === $article->writer_id;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $mergeData = [];

        if ($this->has('title')) {
            $mergeData['title'] = Str::title(trim(preg_replace('/\s+/', ' ', $this->title)));
            $mergeData['slug'] = Str::slug($mergeData['title']);
        }

        if ($this->has('content')) {
            $mergeData['content'] = trim(preg_replace('/\s+/', ' ', $this->content));
        }

        if ($this->has('status')) {
            $mergeData['status'] = strtolower(trim($this->status));
        }

        if (!empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $articleModel = $this->route('article');
        $articleId = $articleModel instanceof Article ? $articleModel->id : $articleModel;

        return [
            'title' => [
                'sometimes',
                'string',
                'min:10',
                Rule::unique('articles', 'title')->ignore($articleId),
                new NotOffensive(),
            ],
            'slug' => [
                'sometimes',
                'string',
                Rule::unique('articles', 'slug')->ignore($articleId),
            ],
            'content' => [
                'sometimes',
                'string',
                'min:100',
                new NotOffensive()
            ],
            'tags' => [
                'sometimes',
                'array',
            ],
            'tags.*' => [
                'string',
                'exists:tags,id',
            ],
            'status' => [
                'sometimes',
                'string',
                'in:draft,published,archived',
            ],
            'category' => [
                'sometimes',
                'string',
                new Enum(ArticleCategory::class),
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.min' => 'The article title is too short. It must contain at least 10 characters.',
            'title.unique' => 'This title has already been taken by another article.',
            'slug.unique' => 'A unique URL slug could not be generated; this article title already exists.',
            'status.in' => 'The selected status is invalid. Accepted values are: draft, published, or archived.',
            'tags.*.exists' => 'One or more of the selected tags do not exist in our database.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'article title',
            'slug' => 'URL slug',
            'content' => 'body content',
            'tags' => 'tags list',
            'status' => 'publishing status',
            'category' => 'article category',
        ];
    }
    }
