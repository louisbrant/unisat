<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->comment('');
            $table->string('name', 128)->primary();
            $table->text('value')->nullable();
        });

        DB::table('settings')->insert([
            ['name' => 'announcement_guest','value' => ''],
            ['name' => 'announcement_guest_id','value' => Str::random(16)],
            ['name' => 'announcement_guest_type','value' => 'info'],
            ['name' => 'announcement_user','value' => ''],
            ['name' => 'announcement_user_id','value' => Str::random(16)],
            ['name' => 'announcement_user_type','value' => 'info'],
            ['name' => 'bad_words','value' => ''],
            ['name' => 'bank', 'value' => '0'],
            ['name' => 'bank_account_number', 'value' => NULL],
            ['name' => 'bank_account_owner', 'value' => NULL],
            ['name' => 'bank_bic_swift', 'value' => NULL],
            ['name' => 'bank_iban', 'value' => NULL],
            ['name' => 'bank_name', 'value' => NULL],
            ['name' => 'bank_routing_number', 'value' => NULL],
            ['name' => 'billing_address','value' => ''],
            ['name' => 'billing_city','value' => ''],
            ['name' => 'billing_country','value' => ''],
            ['name' => 'billing_invoice_prefix','value' => ''],
            ['name' => 'billing_phone','value' => ''],
            ['name' => 'billing_postal_code','value' => ''],
            ['name' => 'billing_state','value' => ''],
            ['name' => 'billing_vat_number','value' => ''],
            ['name' => 'billing_vendor','value' => ''],
            ['name' => 'brc20', 'value' => ''],
            ['name' => 'brc20_address', 'value' => ''],
            ['name' => 'captcha_contact','value' => '0'],
            ['name' => 'captcha_registration','value' => '0'],
            ['name' => 'captcha_secret_key','value' => ''],
            ['name' => 'captcha_site_key','value' => ''],
            ['name' => 'coinbase','value' => '0'],
            ['name' => 'coinbase_key','value' => NULL],
            ['name' => 'coinbase_wh_secret','value' => NULL],
            ['name' => 'contact_email','value' => ''],
            ['name' => 'cronjob_executed_at','value' => NULL],
            ['name' => 'cronjob_key','value' => Str::random(32)],
            ['name' => 'cryptocom','value' => '0'],
            ['name' => 'cryptocom_key','value' => NULL],
            ['name' => 'cryptocom_secret','value' => NULL],
            ['name' => 'cryptocom_wh_secret','value' => NULL],
            ['name' => 'custom_css','value' => '@import url("https://rsms.me/inter/inter.css");'],
            ['name' => 'custom_js','value' => ''],
            ['name' => 'email_address', 'value' => NULL],
            ['name' => 'email_driver', 'value' => 'log'],
            ['name' => 'email_encryption', 'value' => 'log'],
            ['name' => 'email_host', 'value' => NULL],
            ['name' => 'email_password', 'value' => NULL],
            ['name' => 'email_port', 'value' => NULL],
            ['name' => 'email_username', 'value' => NULL],
            ['name' => 'favicon','value' => 'favicon.png'],
            ['name' => 'index','value' => ''],
            ['name' => 'legal_cookie_url', 'value' => NULL],
            ['name' => 'legal_privacy_url', 'value' => NULL],
            ['name' => 'legal_terms_url', 'value' => NULL],
            ['name' => 'license_key', 'value' => NULL],
            ['name' => 'license_type', 'value' => NULL],
            ['name' => 'locale','value' => 'en'],
            ['name' => 'login_tfa','value' => '0'],
            ['name' => 'logo','value' => 'logo.svg'],
            ['name' => 'logo_dark','value' => 'logo_dark.svg'],
            ['name' => 'openai_completions_model','value' => 'text-davinci-003'],
            ['name' => 'openai_key','value' => ''],
            ['name' => 'paginate','value' => '10'],
            ['name' => 'paypal','value' => '0'],
            ['name' => 'paypal_client_id','value' => NULL],
            ['name' => 'paypal_mode','value' => 'sandbox'],
            ['name' => 'paypal_secret','value' => NULL],
            ['name' => 'paypal_webhook_id','value' => NULL],
            ['name' => 'paystack','value' => '0'],
            ['name' => 'paystack_key','value' => NULL],
            ['name' => 'paystack_secret','value' => NULL],
            ['name' => 'razorpay','value' => '0'],
            ['name' => 'razorpay_key','value' => NULL],
            ['name' => 'razorpay_secret','value' => NULL],
            ['name' => 'razorpay_wh_secret','value' => NULL],
            ['name' => 'registration','value' => '1'],
            ['name' => 'registration_tfa','value' => '0'],
            ['name' => 'registration_verification','value' => '1'],
            ['name' => 'social_facebook','value' => ''],
            ['name' => 'social_instagram','value' => ''],
            ['name' => 'social_twitter','value' => ''],
            ['name' => 'social_youtube','value' => ''],
            ['name' => 'stripe','value' => '0'],
            ['name' => 'stripe_key','value' => NULL],
            ['name' => 'stripe_secret','value' => NULL],
            ['name' => 'stripe_wh_secret','value' => NULL],
            ['name' => 'tagline','value' => 'AI powered content generator'],
            ['name' => 'theme','value' => '0'],
            ['name' => 'timezone','value' => 'UTC'],
            ['name' => 'title','value' => 'phpContent'],
            ['name' => 'webhook_user_created','value' => NULL],
            ['name' => 'webhook_user_deleted','value' => NULL],
            ['name' => 'webhook_user_updated','value' => NULL]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
