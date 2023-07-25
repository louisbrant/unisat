<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('images', function (Blueprint $table) {
            $table->comment('');
            $table->integer('id', true);
            $table->integer('user_id')->index('user_id');
            $table->string('name', 255)->nullable()->index('name');
            $table->string('style', 64)->nullable()->index('style');
            $table->string('medium', 64)->nullable()->index('medium');
            $table->string('filter', 64)->nullable()->index('filter');
            $table->string('resolution', 16)->nullable()->index('resolution');
            $table->text('result')->nullable();
            $table->boolean('favorite')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
};
