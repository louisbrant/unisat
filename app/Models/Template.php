<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

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
    public function scopeOfUser(Builder $query, $value)
    {
        return $query->where('user_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeGlobal(Builder $query)
    {
        return $query->where('user_id', '=', 0);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePrivate(Builder $query)
    {
        return $query->where('user_id', '<>', 0);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeCustom(Builder $query)
    {
        return $query->whereNull('slug');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePremade(Builder $query)
    {
        return $query->whereNotNull('slug');
    }

    /**
     * Get the user that owns the template.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     * Get the category of the template.
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * Get the template's documents.
     */
    public function documents()
    {
        return $this->hasMany('App\Models\Document');
    }

    /**
     * Get the template's URL.
     */
    public function getUrlAttribute()
    {
        if ($this->isCustom()) {
            return route('templates.show', $this->id);
        }

        return route('templates.' . str_replace('-', '_', $this->slug));
    }

    /**
     * Get the total documents count under the template.
     *
     * @return int
     */
    public function getTotalDocumentsAttribute()
    {
        return $this->hasMany('App\Models\Document')->where('template_id', $this->id)->count();
    }

    /**
     * Check if the template is custom.
     *
     * @return bool
     */
    public function isCustom()
    {
        return !$this->slug;
    }
}
