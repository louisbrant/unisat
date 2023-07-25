<?php

namespace App\Traits;

use App\Models\Image;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait ImageTrait
{
    /**
     * Store the Image.
     *
     * @param Request $request
     * @param string|null $prompt
     * @param int $variations
     * @return array
     * @throws GuzzleException
     */
    protected function imagesStore(Request $request)
    {
        $response = $this->fetchImages($request);

        $results = [];
        $i = 1;
        foreach ($response['data'] as $result) {
            if ($request->user()->can('create', ['App\Models\Image'])) {
                $results[] = $this->imageModel($request, $result, $i);
                $i++;
            }
        }

        return $results;
    }

    /**
     * Store the Image.
     *
     * @param Request $request
     * @param string|null $prompt
     * @param int $variations
     * @return Image
     * @throws GuzzleException
     */
    protected function imageStore(Request $request, string $prompt = null)
    {
        $response = $this->fetchImages($request);

        return $this->imageModel($request, $response, 0);
    }

    /**
     * @param Request $request
     * @param $result
     * @param $count
     * @return Image
     * @throws GuzzleException
     */
    private function imageModel(Request $request, $result, $count)
    {
        $fileName = Str::uuid();

        $httpClient = new Client();

        $httpClient->request('GET', $count == 0 ? $result['data'][0]['url'] : $result['url'], [
            'sink' => public_path('uploads/users/images/' . $fileName)
        ]);

        $imageResource = imagecreatefrompng(public_path('uploads/users/images/' . $fileName));

        $imageFileName = $fileName . '.jpg';

        // Convert and optimize the image
        imagejpeg($imageResource, public_path('uploads/users/images/' . $imageFileName), 88);

        // Remove the original image
        unlink(public_path('uploads/users/images/' . $fileName));

        $image = new Image;
        $image->name = $request->input('name'). ($count > 1 ? ' (' . $count .')' : '');
        $image->user_id = $request->user()->id;
        $image->style = $request->input('style');
        $image->medium = $request->input('medium');
        $image->filter = $request->input('filter');
        $image->resolution = $request->input('resolution');
        $image->result = $imageFileName;
        $image->save();

        $request->user()->images_month_count += 1;
        $request->user()->images_total_count += 1;
        $request->user()->save();

        return $image;
    }

    /**
     * Update the Image..
     *
     * @param Request $request
     * @param Image $image
     * @return Image
     */
    protected function imageUpdate(Request $request, Image $image)
    {
        if ($request->has('name')) {
            $image->name = $request->input('name');
        }

        if ($request->has('favorite')) {
            $image->favorite = $request->input('favorite');
        }

        $image->save();

        return $image;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws GuzzleException
     */
    private function fetchImages(Request $request)
    {
        $httpClient = new Client();

        $response = $httpClient->request('POST', 'https://api.openai.com/v1/images/generations',
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
                    'prompt' => trim(preg_replace('/(?:\s{2,}+|[^\S ])/ui', ' ', $request->input('description'))) . ($request->input('style') ? '. ' . __('The image should have :style style.', ['style' => $request->input('style')]) : '') . ($request->input('medium') ? '. ' . __('The image should be on a :medium medium.', ['medium' => $request->input('medium')]) : '') . ($request->input('filter') ? '. ' . __('Apply :filter filter.', ['filter' => $request->input('filter')]) : ''),
                    'n' => $request->has('variations') ? (float) $request->input('variations') : 1,
                    'size' => $request->input('resolution'),
                    'response_format' => 'url',
                    'user' => 'user' . $request->user()->id
                ]
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}
