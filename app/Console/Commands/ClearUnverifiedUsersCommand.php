<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClearUnverifiedUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:clear-unverified-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the unverified users from the `users` database table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('users')->where([['email_verified_at', '=', NULL], ['created_at', '<', Carbon::now()->subDays(30)]])->delete();

        return 0;
    }
}
