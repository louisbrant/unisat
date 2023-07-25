<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchResult(Builder $query, $value)
    {
        return $query->where('result', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeOfRole(Builder $query, $value)
    {
        return $query->where('role', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeOfChat(Builder $query, $value)
    {
        return $query->where('chat_id', '=', $value);
    }

    /**
     * Get the user of the document.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     * Get the message of the chat.
     */
    public function chat()
    {
        return $this->belongsTo('App\Models\Chat');
    }
}
