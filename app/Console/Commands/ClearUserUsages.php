<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ClearUserUsages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:clear-user-usages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the `users` database table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::query()->update(['documents_month_count' => 0, 'words_month_count' => 0, 'images_month_count' => 0, 'chats_month_count', 'transcriptions_month_count']);

        return 0;
    }
}
