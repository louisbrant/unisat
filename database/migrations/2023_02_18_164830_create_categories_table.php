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
        Schema::create('categories', function (Blueprint $table) {
            $table->comment('');
            $table->string('id', 64)->unique();
            $table->string('name', 128);
        });

        DB::table('categories')->insert([
            ['id' => 'content','name' => 'Content'],
            ['id' => 'marketing','name' => 'Marketing'],
            ['id' => 'other','name' => 'Other'],
            ['id' => 'social','name' => 'Social'],
            ['id' => 'website','name' => 'Website']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
