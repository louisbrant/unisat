<?php

namespace App\Traits;

use App\Models\Transcription;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

trait TranscriptionTrait
{
    /**
     * Store the Transcription.
     *
     * @param Request $request
     * @return Transcription
     */
    protected function transcriptionStore(Request $request)
    {
        $transcription = new Transcription;

        $httpClient = new Client();

        // Store the temporary file
        $fileName = $request->file('file')->hashName();
        $request->file('file')->move(public_path('uploads/users/transcriptions'), $fileName);

        $response = $httpClient->request('POST', 'https://api.openai.com/v1/audio/transcriptions',
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
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen('uploads/users/transcriptions/' . $fileName, 'r')
                    ],
                    [
                        'name'     => 'model',
                        'contents' => config('settings.openai_transcriptions_model')
                    ],
                    [
                        'name'     => 'prompt',
                        'contents' => trim(preg_replace('/(?:\s{2,}+|[^\S ])/ui', ' ', $request->input('description')))
                    ],
                    [
                        'name'     => 'language',
                        'contents' => $request->input('language')
                    ],
                    [
                        'name'     => 'user',
                        'contents' => 'user' . $request->user()->id
                    ]
                ]
            ]
        );

        // Remove the temporary file
        unlink(public_path('uploads/users/transcriptions/' . $fileName));

        $result = json_decode($response->getBody()->getContents(), true);

        $wordsCount = wordsCount($result['text']);

        $transcription->name = $request->input('name');
        $transcription->user_id = $request->user()->id;
        $transcription->result = trim($result['text']);
        $transcription->words = $wordsCount;
        $transcription->save();

        $request->user()->transcriptions_month_count += 1;
        $request->user()->transcriptions_total_count += 1;
        $request->user()->words_month_count += $wordsCount;
        $request->user()->words_total_count += $wordsCount;
        $request->user()->save();

        return $transcription;
    }

    /**
     * Update the Transcription.
     *
     * @param Request $request
     * @param Transcription $transcription
     * @return Transcription
     */
    protected function transcriptionUpdate(Request $request, Transcription $transcription)
    {
        if ($request->has('name')) {
            $transcription->name = $request->input('name');
        }

        if ($request->has('favorite')) {
            $transcription->favorite = $request->input('favorite');
        }

        if ($request->has('result')) {
            $transcription->result = $request->input('result');
        }

        $transcription->save();

        return $transcription;
    }
}
