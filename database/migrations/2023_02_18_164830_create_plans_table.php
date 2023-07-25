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
        Schema::create('plans', function (Blueprint $table) {
            $table->comment('');
            $table->increments('id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('trial_days')->nullable();
            $table->string('currency', 12);
            $table->text('coupons')->nullable();
            $table->text('tax_rates')->nullable();
            $table->string('amount_month', 32)->nullable()->default('0');
            $table->string('amount_year', 32)->nullable()->default('0');
            $table->tinyInteger('visibility')->nullable();
            $table->unsignedInteger('position')->nullable()->default(0);
            $table->text('features')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('plans')->insert([
            'name' => 'Default',
            'description' => 'The plan\'s awesome description.',
            'trial_days' => NULL,
            'currency' => '',
            'amount_month' => 0,
            'amount_year' => 0,
            'visibility' => 1,
            'features' => json_encode(['words' => -1, 'documents' => -1, 'images' => -1, 'templates' => 1, 'data_export' => 1, 'api' => 1])
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
};
