<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Plan
 *
 * @mixin Builder
 * @package App
 */
class Plan extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'items' => 'object',
        'tax_rates' => 'object',
        'coupons' => 'object',
        'features' => 'object'
    ];

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchName(Builder $query, $value)
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeOfVisibility(Builder $query, $value)
    {
        return $query->where('visibility', '=', $value);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotDefault(Builder $query)
    {
        return $query->where('id', '>', 1);
    }

    /**
     * Check if the Plan is the default plan.
     *
     * @return bool
     */
    public function isDefault()
    {
        return $this->id == 1;
    }
}
