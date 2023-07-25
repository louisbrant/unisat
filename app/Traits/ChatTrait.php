<?php

namespace App\Traits;

use App\Models\Chat;
use Illuminate\Http\Request;

trait ChatTrait
{
    /**
     * Store the Chat.
     *
     * @param Request $request
     * @return Chat
     */
    protected function chatStore(Request $request)
    {
        $chat = new Chat;

        $chat->name = $request->input('name');
        $chat->behavior = $request->input('behavior');
        $chat->user_id = $request->user()->id;
        $chat->save();

        $request->user()->chats_month_count += 1;
        $request->user()->chats_total_count += 1;
        $request->user()->save();

        return $chat;
    }

    /**
     * Update the Chat.
     *
     * @param Request $request
     * @param Chat $chat
     * @return Chat
     */
    protected function chatUpdate(Request $request, Chat $chat)
    {
        if ($request->has('name')) {
            $chat->name = $request->input('name');
        }

        if ($request->has('behavior')) {
            $chat->behavior = $request->input('behavior');
        }

        if ($request->has('favorite')) {
            $chat->favorite = $request->input('favorite');
        }

        $chat->save();

        return $chat;
    }
}
