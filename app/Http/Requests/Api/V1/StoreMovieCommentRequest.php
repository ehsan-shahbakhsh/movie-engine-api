<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\CommentStatus;
use App\Enums\MovieStatus;
use App\Models\Movie;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreMovieCommentRequest",
    required: ["body", "is_spoiler"],
    properties: [
        new OA\Property(property: "body", type: "string", example: "این فیلم فوق‌العاده بود!"),
        new OA\Property(property: "reply_to", type: "integer", example: 1, nullable: true),
        new OA\Property(property: "is_spoiler", type: "boolean", example: false),
    ]
)]
class StoreMovieCommentRequest extends FormRequest
{
    public ?Movie $movie;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->movie = Movie::query()
            ->where('slug', $this->route('movie'))
            ->whereIn('status', [MovieStatus::Published, MovieStatus::ComingSoon])
            ->firstOrFail();
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
                    ->where('commentable_id', $this->movie->id)
                    ->where('commentable_type', Movie::class)
                    ->where('status', CommentStatus::Approved),
            ],
            'is_spoiler' => ['required', 'boolean'],
        ];
    }
}
