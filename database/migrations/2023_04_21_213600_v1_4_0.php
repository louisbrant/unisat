<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transcriptions', function (Blueprint $table) {
            $table->comment('');
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->string('name')->index('name');
            $table->text('result')->nullable();
            $table->boolean('favorite')->default(false);
            $table->unsignedBigInteger('words')->default(0);
            $table->timestamps();
        });

        Schema::table('chats', function($table) {
            $table->string('behavior', 128)->after('name')->nullable();
        });

        Schema::table('users', function($table) {
            $table->bigInteger('transcriptions_month_count')->after('chats_month_count')->default(0);
            $table->bigInteger('transcriptions_total_count')->after('chats_total_count')->default(0);
        });

        DB::table('settings')->insert(
            [
                ['name' => 'openai_transcriptions_size', 'value' => '25'],
                ['name' => 'openai_transcriptions_model', 'value' => 'whisper-1']
            ]
        );

        foreach (DB::table('plans')->select('*')->cursor() as $row) {
            $features = json_decode($row->features, true);

            $features['transcriptions'] = -1;

            DB::statement("UPDATE `plans` SET `features` = :features WHERE `id` = :id", ['features' => json_encode($features), 'id' => $row->id]);
        }

        DB::table('templates')->where('slug', '=', 'meta-description')->update(['prompt' => 'Generate in the :language language, a description in two sentences, for a page called :title. Include the keywords: :keywords. The page is about: :description.']);

        DB::table('templates')->where('slug', '=', 'meta-keywords')->update(['prompt' => 'Generate in the :language language, meta keywords for a page called :title. The page is about: :description.']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
