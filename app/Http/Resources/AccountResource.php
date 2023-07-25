<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar_url' => gravatar($this->email, 96),
            'locale' => $this->locale,
            'timezone' => $this->timezone,
            'plan' => collect($this->plan)->only(['id', 'name', 'features']),
            'default_variations' => $this->default_variations,
            'default_creativity' => $this->default_creativity,
            'default_language' => $this->default_language,
            'documents_month_count' => $this->documents_month_count,
            'documents_total_count' => $this->documents_total_count,
            'words_month_count' => $this->words_month_count,
            'words_total_count' => $this->words_total_count,
            'images_month_count' => $this->images_month_count,
            'images_total_count' => $this->images_total_count,
            'created_at' => $this->created_at
        ];
    }

    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 200
        ];
    }
}
