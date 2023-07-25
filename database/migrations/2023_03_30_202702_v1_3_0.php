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
        Schema::create('chats', function (Blueprint $table) {
            $table->comment('');
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->string('name')->index('name');
            $table->boolean('favorite')->default(false);
            $table->unsignedBigInteger('words')->default(0);
            $table->unsignedBigInteger('messages')->default(0);
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->comment('');
            $table->bigIncrements('id');
            $table->unsignedInteger('chat_id')->index('chat_id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->string('role', 16)->index('role');
            $table->mediumText('result')->nullable();
            $table->unsignedBigInteger('words')->default(0);
            $table->timestamps();
        });

        Schema::table('users', function($table) {
            $table->bigInteger('chats_month_count')->after('documents_month_count')->default(0);
            $table->bigInteger('chats_total_count')->after('documents_total_count')->default(0);
        });

        foreach (DB::table('plans')->select('*')->cursor() as $row) {
            $features = json_decode($row->features, true);

            $features['chats'] = -1;

            DB::statement("UPDATE `plans` SET `features` = :features WHERE `id` = :id", ['features' => json_encode($features), 'id' => $row->id]);
        }

        DB::table('settings')->insert(
            [
                ['name' => 'ai_assistant_name', 'value' => 'Assistant'],
                ['name' => 'ai_assistant_email', 'value' => '']
            ]
        );
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
