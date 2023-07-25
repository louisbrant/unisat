<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallDatabaseRequest;
use App\Http\Requests\InstallConfigRequest;
use App\Models\Setting;
use App\Models\User;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstallController extends Controller
{
    /**
     * Show the Welcome page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('install.container', ['view' => 'install.welcome']);
    }

    /**
     * Show the Requirements page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function requirements()
    {
        $requirements = config('install.extensions');

        $results = [];
        // Check the requirements
        foreach ($requirements as $type => $extensions) {
            if (strtolower($type) == 'php') {
                foreach ($requirements[$type] as $extensions) {
                    $results['extensions'][$type][$extensions] = true;

                    if (!extension_loaded($extensions)) {
                        $results['extensions'][$type][$extensions] = false;

                        $results['errors'] = true;
                    }
                }
            } elseif (strtolower($type) == 'apache') {
                foreach ($requirements[$type] as $extensions) {
                    // Check if the function exists
                    // Prevents from returning a false error
                    if (function_exists('apache_get_modules')) {
                        $results['extensions'][$type][$extensions] = true;

                        if (!in_array($extensions, apache_get_modules())) {
                            $results['extensions'][$type][$extensions] = false;

                            $results['errors'] = true;
                        }
                    }
                }
            }
        }

        // If the current php version doesn't meet the requirements
        if (version_compare(PHP_VERSION, config('install.php_version')) == -1) {
            $results['errors'] = true;
        }

        return view('install.container', ['view' => 'install.requirements', 'results' => $results]);
    }

    /**
     * Show the Permissions page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function permissions()
    {
        $permissions = config('install.permissions');

        $results = [];
        foreach ($permissions as $type => $files) {
            foreach ($files as $file) {
                if (is_writable(base_path($file))) {
                    $results['permissions'][$type][$file] = true;
                } else {
                    $results['permissions'][$type][$file] = false;
                    $results['errors'] = true;
                }
            }
        }

        return view('install.container', ['view' => 'install.permissions', 'results' => $results]);
    }

    /**
     * Show the Database configuration page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function database()
    {
        return view('install.container', ['view' => 'install.database']);
    }

    /**
     * Show the Admin credentials page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function account()
    {
        return view('install.container', ['view' => 'install.account']);
    }

    /**
     * Show the Complete page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function complete()
    {
        return view('install.container', ['view' => 'install.complete']);
    }

    /**
     * Validate the database credentials, and write the .env config file.
     *
     * @param InstallConfigRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeConfig(InstallConfigRequest $request)
    {
        $validateDatabase = $this->validateDatabaseCredentials($request);
        if ($validateDatabase !== true) {
            return back()->with('error', __('Invalid database credentials. ' . $validateDatabase))->withInput();
        }

        $validateConfigFile = $this->writeEnvFile($request);
        if ($validateConfigFile !== true) {
            return back()->with('error', __('Unable to save .env file, check file permissions. ' . $validateConfigFile))->withInput();
        }

        return redirect()->route('install.account');
    }

    /**
     * Migrate the database, and add the default admin user.
     *
     * @param InstallDatabaseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDatabase(InstallDatabaseRequest $request)
    {
        $migrateDatabase = $this->migrateDatabase();
        if ($migrateDatabase !== true) {
            return back()->with('error', __('Failed to migrate the database. ' . $migrateDatabase))->withInput();
        }

        $createDefaultUser = $this->createDefaultUser($request);
        if ($createDefaultUser !== true) {
            return back()->with('error', __('Failed to create the default user. ' . $createDefaultUser))->withInput();
        }

        $saveInstalledFile = $this->writeEnvInstalledStatus();
        if ($saveInstalledFile !== true) {
            return back()->with('error', __('Failed to finalize the installation. ' . $saveInstalledFile))->withInput();
        }

        if ($request->input('newsletter')) {
            $this->subscribeNewsletter($request);
        }

        return redirect()->route('install.complete');
    }

    /**
     * Validate the database credentials.
     *
     * @return bool|string
     */
    private function validateDatabaseCredentials(Request $request)
    {
        $settings = config("database.connections.mysql");

        config([
            'database' => [
                'default' => 'mysql',
                'connections' => [
                    'mysql' => array_merge($settings, [
                        'driver' => 'mysql',
                        'host' => $request->input('database_hostname'),
                        'port' => $request->input('database_port'),
                        'database' => $request->input('database_name'),
                        'username' => $request->input('database_username'),
                        'password' => $request->input('database_password'),
                    ]),
                ],
            ],
        ]);

        DB::purge();

        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Migrate the database.
     *
     * @return bool|string
     */
    private function migrateDatabase()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Create the default admin user.
     *
     * @return bool|string
     */
    private function createDefaultUser(Request $request)
    {
        try {
            $user = new User;

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->locale = config('app.locale');
            $user->role = 1;
            $user->timezone = 'UTC';
            $user->api_token = Str::random(64);

            $user->save();

            $user->markEmailAsVerified();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Write the .env file.
     *
     * @return bool|string
     */
    private function writeEnvFile(Request $request)
    {
        $config =
            "APP_NAME='".config('info.software.name')."'\n".
            "APP_ENV=production\n".
            "APP_KEY=base64:'".base64_encode(Str::random(32))."'\n".
            "APP_DEBUG=false\n".
            "APP_URL='".route('home')."'\n".
            "\n".
            "LOG_CHANNEL=stack\n".
            "\n".
            "DB_CONNECTION=mysql\n".
            "DB_HOST='".$request->input('database_hostname')."'\n".
            "DB_PORT=".$request->input('database_port')."\n".
            "DB_DATABASE='".$request->input('database_name')."'\n".
            "DB_USERNAME='".$request->input('database_username')."'\n".
            "DB_PASSWORD='".$request->input('database_password')."'\n".
            "\n".
            "BROADCAST_DRIVER=log\n".
            "CACHE_DRIVER=file\n".
            "QUEUE_CONNECTION=sync\n".
            "SESSION_DRIVER=file\n".
            "SESSION_LIFETIME=120\n".
            "\n".
            "REDIS_HOST=127.0.0.1\n".
            "REDIS_PASSWORD=null\n".
            "REDIS_PORT=6379\n".
            "\n".
            "MAIL_DRIVER=smtp\n".
            "MAIL_HOST=smtp.mailtrap.io\n".
            "MAIL_PORT=2525\n".
            "MAIL_USERNAME=null\n".
            "MAIL_PASSWORD=null\n".
            "MAIL_ENCRYPTION=null\n".
            "MAIL_FROM_ADDRESS=null\n".
            "MAIL_FROM_NAME=\"\${APP_NAME}\"\n".
            "\n".
            "AWS_ACCESS_KEY_ID=\n".
            "AWS_SECRET_ACCESS_KEY=\n".
            "AWS_DEFAULT_REGION=us-east-1\n".
            "AWS_BUCKET=\n".
            "\n".
            "PUSHER_APP_ID=\n".
            "PUSHER_APP_KEY=\n".
            "PUSHER_APP_SECRET=\n".
            "PUSHER_APP_CLUSTER=mt1\n".
            "\n".
            "MIX_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"\n".
            "MIX_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"";

        try {
            file_put_contents(base_path('.env'), $config);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Write the installed status to the .env file.
     *
     * @return bool|string
     */
    private function writeEnvInstalledStatus()
    {
        try {
            file_put_contents(base_path('.env'), "\n\nAPP_INSTALLED=true", FILE_APPEND);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Subscribe to the newsletter.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function subscribeNewsletter(Request $request)
    {
        $httpClient = new HttpClient(['timeout' => 10, 'verify' => false]);

        try {
            $httpClient->request('POST', 'https://api.lunatio.com/newsletter',
                [
                    'form_params' => [
                        'email' => $request->input('email')
                    ]
                ]
            );
        } catch (\Exception $e) {}
    }
}
