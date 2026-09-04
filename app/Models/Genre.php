<?php

namespace App\Models;

use App\Http\Resources\Api\V1\GenreResource;
use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\GenreFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UseResource(GenreResource::class)]
#[Fillable(['name', 'slug', 'description', 'is_active', 'sort_order'])]
class Genre extends Model
{
    /** @use HasFactory<GenreFactory> */
    use HasFactory;
    use Sluggable;

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class);
    }

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class);
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }
}
