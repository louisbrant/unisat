<?php

namespace App\Traits;

use GuzzleHttp\Client as HttpClient;

trait WebhookTrait
{
    /**
     * Call webhook event.
     *
     * @param $url
     * @param $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function callWebhook($url, $data)
    {
        if ($url) {
            try {
                $httpClient = new HttpClient();

                $httpClient->request('POST', $url, [
                    'timeout' => 5,
                    'form_params' => $data
                ]);
            } catch (\Exception $e) {}
        }
    }
}
