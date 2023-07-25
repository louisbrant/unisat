<?php

namespace App\Http\Controllers;

use App\Models\Migration;
use Illuminate\Support\Facades\Artisan;

class UpdateController extends Controller
{
    /**
     * Show the Welcome page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('update.container', ['view' => 'update.welcome']);
    }

    /**
     * Show the Overview page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function overview()
    {
        $migrations = $this->getMigrations();
        $executedMigrations = $this->getExecutedMigrations();

        return view('update.container', ['view' => 'update.overview', 'updates' => count($migrations) - count($executedMigrations)]);
    }

    /**
     * Show the Complete page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function complete()
    {
        return view('update.container', ['view' => 'update.complete']);
    }

    /**
     * Update the database with the new migrations.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDatabase()
    {
        $migrateDatabase = $this->migrateDatabase();
        if ($migrateDatabase !== true) {
            return back()->with('error', __('Failed to migrate the database. ' . $migrateDatabase));
        }

        return redirect()->route('update.complete');
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
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get the available migrations.
     *
     * @return array
     */
    private function getMigrations()
    {
        $migrations = scandir(database_path().'/migrations');

        $output = [];
        foreach($migrations as $migration) {
            // Select only the .php files
            if($migration != '.' && $migration != '..' && substr($migration, -4, 4) == '.php') {
                $output[] = str_replace('.php', '', $migration);
            }
        }

        return $output;
    }

    /**
     * Get the executed migrations.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getExecutedMigrations()
    {
        return Migration::all()->pluck('migration');
    }
}
