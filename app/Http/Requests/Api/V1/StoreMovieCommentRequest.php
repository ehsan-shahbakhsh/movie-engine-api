<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\CommentStatus;
use App\Models\Movie;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMovieCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:3', 'max:1000'],
            'reply_to' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')
                    ->where('commentable_id', $this->route('movie')->id)
                    ->where('commentable_type', Movie::class)
                    ->where('status', CommentStatus::Approved),
            ],
            'is_spoiler' => ['required', 'boolean'],
        ];
    }
}
