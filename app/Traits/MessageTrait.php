<?php

namespace App\Traits;

use App\Models\Chat;
use App\Models\Message;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait MessageTrait
{
    /**
     * Store the Message.
     *
     * @param Request $request
     * @return Message
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function messageStore(Request $request)
    {
        $chat = Chat::where('id', $request->input('chat_id'))->firstOrFail();

        $message = new Message();
        $message->chat_id = $request->input('chat_id');
        $message->user_id = $request->user()->id;
        $message->role = $request->input('role');

        if ($request->input('role') == 'user') {
            $text = trim($request->input('message'));
        } else {
            try {
                $httpClient = new Client();

                $oldMessages = Message::where([['chat_id', '=', $request->input('chat_id')], ['user_id', '=', $request->user()->id]])->orderBy('id', 'desc')->limit(20)->get()->reverse()->toArray();

                // If there's a chat history
                if ($oldMessages) {
                    // Check if the last message is from the user
                    if (end($oldMessages)['user_id'] == $request->user()->id) {
                        // Remove the last message
                        array_pop($oldMessages);
                    }
                }

                // If there's a behavior defined
                if ($chat->behavior) {
                    $messages[] = ['role' => 'system', 'content' => $chat->behavior];
                }

                // Prepare the chat history
                foreach ($oldMessages as $oldMessage) {
                    $messages[] = ['role' => $oldMessage['role'], 'content' => trim(preg_replace('/(?:\s{2,}+|[^\S ])/ui', ' ', $oldMessage['result']))];
                }

                // Append the user's input
                $messages[] = ['role' => 'user', 'content' => trim(preg_replace('/(?:\s{2,}+|[^\S ])/ui', ' ', $request->input('message')))];

                $response = $httpClient->request('POST', 'https://api.openai.com/v1/chat/completions',
                    [
                        'proxy' => [
                            'http' => getRequestProxy(),
                            'https' => getRequestProxy()
                        ],
                        'timeout' => config('settings.request_timeout') * 60,
                        'headers' => [
                            'User-Agent' => config('settings.request_user_agent'),
                            'Authorization' => 'Bearer ' . config('settings.openai_key'),
                        ],
                        'json' => [
                            'model' => config('settings.openai_completions_model'),
                            'messages' => $messages,
                            'temperature' => $request->has('creativity') ? (float) $request->input('creativity') : 0.5,
                            'n' => 1,
                            'frequency_penalty' => 0,
                            'presence_penalty' => 0,
                            'user' => 'user' . $request->user()->id
                        ]
                    ]
                );

                $result = json_decode($response->getBody()->getContents(), true);

                $text = $result['choices'][0]['message']['content'] ?? '';
            } catch (\Exception $e) {
                $text = $e->getMessage();
            }
        }

        $wordsCount = wordsCount($text);

        if ($request->input('role') == 'assistant') {
            $request->user()->words_month_count += $wordsCount;
            $request->user()->words_total_count += $wordsCount;
            $request->user()->save();
        }

        $message->result = $text;
        $message->words = $wordsCount;
        $message->save();

        // Update the Chat
        $chat->words += $message->words;
        $chat->messages += 1;
        $chat->save();

        return $message;
    }
}
