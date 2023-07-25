<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
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
     * @param $value
     * @return Builder
     */
    public function scopeOfFavorite(Builder $query, $value)
    {
        return $query->where('favorite', '=', $value);
    }

    /**
     * Get the user of the document.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     * Get the chat's messages.
     */
    public function messages()
    {
        return $this->hasMany('App\Models\Message');
    }

    /**
     * Get the chat's most recent message.
     */
    public function latestMessage()
    {
        return $this->hasOne('App\Models\Message')->latestOfMany();
    }
}
