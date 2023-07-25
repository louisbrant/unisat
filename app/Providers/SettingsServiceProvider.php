<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        try {
            $settings = Setting::all()->pluck('value', 'name');

            // Set the app's name
            config(['app.name' => $settings['title']]);

            // Store all the database settings in a config array
            foreach ($settings as $key => $value) {
                config(['settings.' . $key => $value]);
            }

            // Set the app's default theme
            if (!$request->hasCookie('dark_mode')) {
                config(['settings.dark_mode' => config('settings.theme')]);
            } else {
                // Rewrite the app's theme with the user's preference
                if ($request->cookie('dark_mode') == 1) {
                    config(['settings.dark_mode' => 1]);
                } else {
                    config(['settings.dark_mode' => 0]);
                }
            }

            // Set the app's default mail settings
            config(['mail.default' => config('settings.email_driver')]);
            config(['mail.mailers.smtp.host' => config('settings.email_host')]);
            config(['mail.mailers.smtp.port' => config('settings.email_port')]);
            config(['mail.mailers.smtp.encryption' => config('settings.email_encryption')]);
            config(['mail.mailers.smtp.username' => config('settings.email_username')]);
            config(['mail.mailers.smtp.password' => config('settings.email_password')]);
            config(['mail.from.address' => config('settings.email_address')]);
            config(['mail.from.name' => config('settings.title')]);

            // Set the reCaptcha keys
            config(['captcha.sitekey' => config('settings.captcha_site_key')]);
            config(['captcha.secret' => config('settings.captcha_secret_key')]);

            // Get the available locales
            $locales = [];
            if($handle = opendir(app()->langPath())) {
                while(false !== ($locale = readdir($handle))) {
                    if($locale != '.' && $locale != '..' && pathinfo($locale, PATHINFO_EXTENSION) == 'json') {
                        // Set the default locale
                        if (pathinfo($locale, PATHINFO_FILENAME) == config('settings.locale')) {
                            config(['app.locale' => pathinfo($locale, PATHINFO_FILENAME)]);
                        }

                        $locales[] = pathinfo($locale, PATHINFO_FILENAME);
                    }
                }
                closedir($handle);
            }

            // Store the locales
            config(['app.locales' => array_intersect_key(config('languages'), array_flip($locales))]);
        } catch (\Exception $e) {}
    }
}
