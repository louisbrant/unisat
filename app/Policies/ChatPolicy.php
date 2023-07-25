<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any chats.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the chat.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return mixed
     */
    public function view(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can create chats.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->plan->features->chats == -1) {
            return true;
        } elseif($user->plan->features->chats > 0) {
            if ($user->chats_month_count < $user->plan->features->chats) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can update the chat.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return mixed
     */
    public function update(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can delete the chat.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return mixed
     */
    public function delete(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can restore the chat.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return mixed
     */
    public function restore(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the chat.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return mixed
     */
    public function forceDelete(User $user, Chat $chat)
    {
        //
    }
}
