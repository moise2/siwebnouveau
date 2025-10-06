<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'start_date',
        'end_date',
        'location',
        'featured_image',
        'media_file',
    ];
    // public const PUBLISHED = 'PUBLISHED';
    // /**
    //  * Scope a query to only published scopes.
    //  *
    //  * @param \Illuminate\Database\Eloquent\Builder $query
    //  *
    //  * @return \Illuminate\Database\Eloquent\Builder
    //  */

    // public function scopePublished(Builder $query)
    // {
    //     return $query->where('status', '=', static::PUBLISHED);
    // }
}
