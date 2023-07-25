<?php

/**
 * Selects and caches a proxy.
 *
 * @return null|string
 */
function getRequestProxy()
{
    // If request proxies are set
    if (!empty(config('settings.request_proxy'))) {
        // Check if there's a cached proxy already
        if (config('settings.request_cached_proxy')) {
            $proxy = config('settings.request_cached_proxy');
        } else {
            // Select a proxy at random
            $proxies = preg_split('/\n|\r/', config('settings.request_proxy'), -1, PREG_SPLIT_NO_EMPTY);
            $proxy = $proxies[array_rand($proxies)];

            // Cache the selected proxy
            config(['settings.request_cached_proxy' => $proxy]);
        }

        return $proxy;
    }

    return null;
}