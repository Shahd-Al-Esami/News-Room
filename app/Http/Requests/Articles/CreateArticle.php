<?php

namespace App\Http\Requests\Articles;
use App\Enums\ArticleCategory;
use App\Rules\NotOffensive;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class CreateArticle extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
public function authorize(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->role, ['writer', 'admin']);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => $this->title ? Str::title(trim(preg_replace('/\s+/', ' ', $this->title))) : null,
            'content' => $this->content ? trim(preg_replace('/\s+/', ' ', $this->content)) : null,
            'slug' => Str::slug($this->slug ?? $this->title),
            'status' => $this->status ? strtolower(trim($this->status)) : 'draft',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:10',
                'unique:articles,title',
                new NotOffensive(),
            ],
            'slug' => [
                'required',
                'string',
                'unique:articles,slug',
            ],
            'content' => [
                'required',
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
                'required',
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
            'title.required' => 'An engaging article title is mandatory.',
            'title.min' => 'The article title is too short. It must contain at least 10 characters.',
            'title.unique' => 'This title has already been taken by another article.',
            'slug.unique' => 'A unique URL slug could not be generated; this article title already exists.',
            'content.required' => 'The main article body content cannot be empty.',
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
