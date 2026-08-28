<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\CommentStatus;
use App\Enums\SeriesPublishStatus;
use App\Models\Series;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreMovieCommentRequest",
    required: ["body", "is_spoiler"],
    properties: [
        new OA\Property(property: "body", type: "string", example: "این سریال فوق‌العاده بود!"),
        new OA\Property(property: "reply_to", type: "integer", example: 1, nullable: true),
        new OA\Property(property: "is_spoiler", type: "boolean", example: false),
    ]
)]
class StoreSeriesCommentRequest extends FormRequest
{
    public ?Series $series;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->series = Series::query()
            ->where('slug', $this->route('series'))
            ->whereIn('publish_status', [SeriesPublishStatus::Published, SeriesPublishStatus::ComingSoon])
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
                    ->where('commentable_id', $this->series->id)
                    ->where('commentable_type', Series::class)
                    ->where('status', CommentStatus::Approved),
            ],
            'is_spoiler' => ['required', 'boolean'],
        ];
    }
}
