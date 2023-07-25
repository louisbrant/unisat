<?php

namespace App\Policies;

use App\Models\Transcription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TranscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any transcriptions.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the transcription.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transcription  $transcription
     * @return mixed
     */
    public function view(User $user, Transcription $transcription)
    {
        //
    }

    /**
     * Determine whether the user can create transcriptions.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->plan->features->transcriptions == -1) {
            return true;
        } elseif($user->plan->features->transcriptions > 0) {
            if ($user->transcriptions_month_count < $user->plan->features->transcriptions) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can update the transcription.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transcription  $transcription
     * @return mixed
     */
    public function update(User $user, Transcription $transcription)
    {
        //
    }

    /**
     * Determine whether the user can delete the transcription.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transcription  $transcription
     * @return mixed
     */
    public function delete(User $user, Transcription $transcription)
    {
        //
    }

    /**
     * Determine whether the user can restore the transcription.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transcription  $transcription
     * @return mixed
     */
    public function restore(User $user, Transcription $transcription)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the transcription.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transcription  $transcription
     * @return mixed
     */
    public function forceDelete(User $user, Transcription $transcription)
    {
        //
    }
}
