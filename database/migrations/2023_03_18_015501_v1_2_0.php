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
        DB::table('settings')->where('name', '=', 'openai_completions_model')->update(['value' => 'gpt-4']);

        DB::table('settings')->insert(
            [
                ['name' => 'request_proxy', 'value' => null],
                ['name' => 'request_timeout', 'value' => 5],
                ['name' => 'request_user_agent', 'value' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36']
            ]
        );

        DB::table('templates')->update(['views' => 0]);

        DB::table('templates')->insert([
            ['user_id' => '0','slug' => 'product-sheet','name' => 'Product sheet','icon' => 'description','category_id' => 'marketing','description' => 'Generate compelling product sheets for a product or service.','prompt' => 'Generate in the :language language, a product sheet for a product named: :product. The product is about: :description.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'welcome-email','name' => 'Welcome email','icon' => 'mark-email-unread','category_id' => 'marketing','description' => 'Generate engaging welcome emails for a product or service.','prompt' => 'Generate in the :language language, a welcome email for a product named: :product. The voice tone is: :tone. The product is about: :description.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'push-notification','name' => 'Push notification','icon' => 'ad-units','category_id' => 'marketing','description' => 'Generate push notifications based on the description of a product or service.','prompt' => 'Generate in the :language language, a push notification
 with a maximum of 160 characters, for a product about: :description.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'blog-listicle','name' => 'Blog listicle','icon' => 'headlines','category_id' => 'content','description' => 'Generate blog listicles based on the blog post title and content.','prompt' => 'Generate in the :language language, a listicle for a blog post titled: :title. The blog post is about: :content.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'content-grammar','name' => 'Content grammar','icon' => 'checklist','category_id' => 'content','description' => 'Correct the grammatical errors for any text in seconds.','prompt' => 'Correct in the :language language, the grammatical errors for the following text: :content','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'blog-tags','name' => 'Blog tags','icon' => 'text-tags','category_id' => 'content','description' => 'Generate blog tags based on the blog post title and content.','prompt' => 'Generate in the :language language, tags separated by comma, for a blog post titled: :title. The blog post is about: :content.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'pros-cons','name' => 'Pros and cons','icon' => 'thumbs-up-down','category_id' => 'website','description' => 'Generate pros and cons for a product or service.','prompt' => 'Generate in the :language language, pros and cons for a product named: :product. The product is about: :description.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'google-advertisement','name' => 'Google advertisement','icon' => 'google-ads','category_id' => 'marketing','description' => 'Generate optimized Google advertisements for a product or service.','prompt' => 'Generate in the :language language, an ad with 3 Headlines of maximum 30 characters each, and 2 Descriptions of maximum 90 characters each, for a product named: :product. The product is about: :description.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'facebook-advertisement','name' => 'Facebook advertisement','icon' => 'facebook','category_id' => 'marketing','description' => 'Generate optimized Facebook advertisements for a product or service.','prompt' => 'Generate in the :language language, an ad with a Headline of maximum 27 characters, a Description of maximum 27 characters, and a short Primary Text must not exceed 127 characters, for a product named: :product. The product is about: :description.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'job-description','name' => 'Job description','icon' => 'work-outline','category_id' => 'marketing','description' => 'Generate professional job descriptions to attract top talents.','prompt' => 'Generate in the :language language, a job description for a :position position, at a company called: :company. The company is about: :description.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'review','name' => 'Review','icon' => 'reviews','category_id' => 'website','description' => 'Generate reviews based on the name and description of a product or service.','prompt' => 'Generate in the :language language, a review for a product named: :product. The product is about: :description.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06'],
            ['user_id' => '0','slug' => 'feature-section','name' => 'Feature section','icon' => 'auto-awesome','category_id' => 'website','description' => 'Generate feature sections to highlight a product or service.','prompt' => 'Generate in the :language language, a feature section for the title: title. The audience is: :audience. The voice tone is: :tone. The product is named: :product. The product is about: :description.','views' => '0','created_at' => '2023-03-21 15:30:06','updated_at' => '2023-03-21 15:30:06']
        ]);
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
