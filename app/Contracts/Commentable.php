<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Commentable
{
    /**
     * Get the model's comments.
     */
    public function comments(): MorphMany;
}
