<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessCronjobRequest;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class CronjobController extends Controller
{
    /**
     * Run the scheduled cron job commands.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ProcessCronjobRequest $request)
    {
        ini_set('max_execution_time', 0);

        Artisan::call('schedule:run');

        Setting::where('name', 'cronjob_executed_at')->update(['value' => Carbon::now()->timestamp]);

        return response()->json([
            'status' => 200
        ], 200);
    }
}
