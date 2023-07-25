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
        Schema::create('documents', function (Blueprint $table) {
            $table->comment('');
            $table->integer('id', true);
            $table->integer('user_id')->index('user_id');
            $table->string('name', 255)->nullable()->index('name');
            $table->string('template_id', 64)->index('type');
            $table->mediumText('result')->nullable();
            $table->unsignedBigInteger('words')->default(0);
            $table->boolean('favorite')->default(false);
            $table->timestamps();

            $table->index(['template_id'], 'template_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
