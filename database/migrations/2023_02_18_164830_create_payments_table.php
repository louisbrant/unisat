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
        Schema::create('payments', function (Blueprint $table) {
            $table->comment('');
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->unsignedInteger('plan_id')->index('plan_id');
            $table->string('payment_id', 128)->index('payment_id');
            $table->string('invoice_id', 128)->nullable()->index('invoice_id');
            $table->string('processor', 32)->index('processor');
            $table->string('amount', 32);
            $table->string('currency', 12);
            $table->string('interval', 16)->index('interval');
            $table->string('status', 16)->index('status');
            $table->text('product')->nullable();
            $table->text('coupon')->nullable();
            $table->text('tax_rates')->nullable();
            $table->text('seller')->nullable();
            $table->text('customer')->nullable();
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
        Schema::dropIfExists('payments');
    }
};
