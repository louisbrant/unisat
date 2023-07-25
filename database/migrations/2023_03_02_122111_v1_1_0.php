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
        Schema::dropIfExists('templates');

        Schema::create('templates', function (Blueprint $table) {
            $table->comment('');
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->string('slug', 64)->unique()->nullable();
            $table->string('name', 128);
            $table->string('icon', 32)->nullable();
            $table->string('category_id', 64)->index('category_id');
            $table->text('description');
            $table->text('prompt');
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();
        });

        DB::table('templates')->insert([
            ['user_id' => '0','slug' => 'freestyle','name' => 'Freestyle','icon' => 'draw','category_id' => 'custom','description' => 'Ask about anything, the only limit is imagination.','prompt' => ':prompt','views' => '351','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'about-us','name' => 'About us','icon' => 'people-alt','category_id' => 'website','description' => 'Generate about us text on the title and description of a page.','prompt' => 'Generate in the :language language, an about us text, for a product named: :product. The voice tone is: :tone. The audience is: :audience. The product is about: :description.','views' => '1','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'advertisement','name' => 'Advertisement','icon' => 'ads-click','category_id' => 'marketing','description' => 'Generate creative ad descriptions for a product or service.','prompt' => 'Generate in the :language language, a creative ad, aimed at: :audience, for the product: :product.','views' => '7','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'article','name' => 'Article','icon' => 'text-image','category_id' => 'content','description' => 'Generate articles based on title, keywords, and subheading.','prompt' => 'Generate in the :language language, an article about :title. The topics of the articles are: :subheadings. Include the keywords: :keywords. The length of the article should be: :length.','views' => '73727','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-03-05 17:52:30'],
            ['user_id' => '0','slug' => 'blog-intro','name' => 'Blog intro','icon' => 'horizontal-split-top','category_id' => 'content','description' => 'Generate blog intros based on the blog post title and content.','prompt' => 'Generate in the :language language, an intro for a blog post titled: :title. The blog post is about: :content.','views' => '1','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'blog-outline','name' => 'Blog outline','icon' => 'format-list-bulleted','category_id' => 'content','description' => 'Generate blog outlines based on the blog post title and content.','prompt' => 'Generate in the :language language, an outline for a blog post titled: :title. The blog post is about: :content.','views' => '7','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'blog-outro','name' => 'Blog outro','icon' => 'horizontal-split','category_id' => 'content','description' => 'Generate blog outros based on the blog post title and content.','prompt' => 'Generate in the :language language, an outro for a blog post titled: :title. The blog post is about: :content.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'blog-paragraph','name' => 'Blog paragraph','icon' => 'subject','category_id' => 'content','description' => 'Generate blog paragraphs based on the blog post title and subheading.','prompt' => 'Generate in the :language language, a blog paragraph about :title. The topic of the paragraph is: :subheading.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'blog-post','name' => 'Blog post','icon' => 'view-headline','category_id' => 'content','description' => 'Generate blog posts, focused on keywords, about any topic.','prompt' => 'Generate in the :language language, a blog post about: :description. Focus on the keywords: :keywords.','views' => '6','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'blog-section','name' => 'Blog section','icon' => 'drag-handle','category_id' => 'content','description' => 'Generate blog sections based on the blog post title and subheading.','prompt' => 'Generate in the :language language, a few blog paragraphs about :title. The topic of the paragraphs is: :subheading.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'blog-talking-points','name' => 'Blog talking points','icon' => 'format-list-numbered','category_id' => 'content','description' => 'Generate blog talking points based on the blog post title and subheading.','prompt' => 'Generate in the :language language, talking points for a blog post titled: :title. The blog topic is: :subheading.','views' => '1','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-03-05 05:55:30'],
            ['user_id' => '0','slug' => 'blog-title','name' => 'Blog title','icon' => 'short-text','category_id' => 'content','description' => 'Generate blog titles based on the blog post content.','prompt' => 'Generate in the :language language, a blog title where the content of the blog post is: :content.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'call-to-action','name' => 'Call to action','icon' => 'call-to-action','category_id' => 'website','description' => 'Generate CTA lines based on the name and description of a product or service.','prompt' => 'Generate in the :language language, a short CTA line for a product named: :product. The product is about: :description. The audience of the product will include: :audience. The voice tone is: :tone.','views' => '1','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'content-rewrite','name' => 'Content rewrite','icon' => 'cached','category_id' => 'content','description' => 'Rewrite any kind of content in seconds, in an enhanced way.','prompt' => 'Rewrite in the :language language: :content','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'content-summary','name' => 'Content summary','icon' => 'compress','category_id' => 'content','description' => 'Summarize any kind of content in seconds, in an enhanced way.','prompt' => 'Summarize in the :language language: :content','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'faq','name' => 'FAQ','icon' => 'contact-support','category_id' => 'website','description' => 'Generate frequently asked questions for a product or service.','prompt' => 'Generate in the :language language, a FAQ for a product named: :product. The product is about: :description.','views' => '63','created_at' => '2023-03-03 12:01:42','updated_at' => '2023-03-03 12:01:42'],
            ['user_id' => '0','slug' => 'hashtags','name' => 'Hashtags','icon' => 'tag','category_id' => 'social','description' => 'Generate #hashtags for social network content.','prompt' => 'Generate in the :language language, hashtags for: :description.','views' => '4','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'headline','name' => 'Headline','icon' => 'headline','category_id' => 'website','description' => 'Generate engaging headlines for products and services.','prompt' => 'Generate in the :language language, a headline of about 5 to 8 words for a product named: :product. The product is about: :description. The audience of the product will include: :audience. The voice tone is: :tone.','views' => '9','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'how-it-works','name' => 'How it works','icon' => 'settings-suggest','category_id' => 'website','description' => 'Generate steps about how a product or service works.','prompt' => 'Generate in the :language language, how it works steps, for a product named: :product. The audience is: :audience. The product is about: :description.','views' => '3','created_at' => '2023-03-03 12:01:42','updated_at' => '2023-03-03 12:01:42'],
            ['user_id' => '0','slug' => 'meta-description','name' => 'Meta description','icon' => 'wysiwyg','category_id' => 'website','description' => 'Generate meta descriptions based on the title and description of a page.','prompt' => 'Generate in the :language language, a description in two sentences, for a page called :name. Include the keywords: :keywords. The page is about: :description.','views' => '5','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'meta-keywords','name' => 'Meta keywords','icon' => 'meta-keywords','category_id' => 'website','description' => 'Generate meta keywords based on the title and description of a page.','prompt' => 'Generate in the :language language, meta keywords for a page called :name. The page is about: :description.','views' => '1','created_at' => '2023-03-03 12:01:42','updated_at' => '2023-03-03 20:49:54'],
            ['user_id' => '0','slug' => 'mission-statement','name' => 'Mission statement','icon' => 'rocket','category_id' => 'marketing','description' => 'Generate comprehensive and informative mission statements.','prompt' => 'Generate in the :language language, a mission statement for the company: :company. The voice tone is: :tone. The company is about :description.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'newsletter','name' => 'Newsletter','icon' => 'feed','category_id' => 'marketing','description' => 'Generate engaging and comprehensive newsletters.','prompt' => 'Generate in the :language language, a newsletter with the subject: :subject. The company is: :company. The voice tone is: :tone.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'pain-agitate-solution','name' => 'Pain-Agitate-Solution','icon' => 'science','category_id' => 'marketing','description' => 'Generate high-converting sales copy using the PAS formula.','prompt' => 'Generate in the :language language, a Pain-Agitate-Solution for a product called: product. The product is about: :description.','views' => '1','created_at' => '2023-03-03 12:01:42','updated_at' => '2023-03-05 01:09:09'],
            ['user_id' => '0','slug' => 'paragraph','name' => 'Paragraph','icon' => 'subject','category_id' => 'content','description' => 'Generate paragraphs, focused on keywords, about any topic.','prompt' => 'Generate in the :language language, a paragraph about :description. Include the keywords: :keywords.','views' => '1','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'press-release','name' => 'Press release','icon' => 'newspaper','category_id' => 'marketing','description' => 'Generate comprehensive and informative press releases.','prompt' => 'Generate in the :language language, a press release for the company: :company. The voice tone is: :tone. The company is about :description. The press release subject is about: :subject.','views' => '1','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'social-post','name' => 'Social post','icon' => 'chat','category_id' => 'social','description' => 'Generate social posts ready to be published on social platforms.','prompt' => 'Generate in the :language language, a social post about: :description. The voice tone is: :tone.','views' => '3','created_at' => '2023-03-03 12:01:42','updated_at' => '2023-03-05 04:22:16'],
            ['user_id' => '0','slug' => 'social-post-caption','name' => 'Social post caption','icon' => 'reviews','category_id' => 'social','description' => 'Generate social post captions ready to grab attention.','prompt' => 'Generate in the :language language, a social post caption about: :description. The voice tone is: :tone.','views' => '511','created_at' => '2023-03-03 12:01:42','updated_at' => '2023-03-05 05:33:41'],
            ['user_id' => '0','slug' => 'startup-ideas','name' => 'Startup ideas','icon' => 'tips-and-updates','category_id' => 'marketing','description' => 'Generate innovative startup ideas based on domains.','prompt' => 'Generate in the :language language, startup ideas in the folloing domains: :domains.','views' => '0','created_at' => '2023-03-03 12:01:42','updated_at' => '2023-03-04 19:19:44'],
            ['user_id' => '0','slug' => 'startup-names','name' => 'Startup names','icon' => 'lightbulb','category_id' => 'marketing','description' => 'Generate creative startup names based on the description and keywords.','prompt' => 'Generate in the :language language, a list of startup names that include any of the keywords: :keywords. The startup is about: :description.','views' => '255','created_at' => '2023-03-03 12:01:42','updated_at' => '2023-03-04 23:30:19'],
            ['user_id' => '0','slug' => 'subheadline','name' => 'Subheadline','icon' => 'subheadline','category_id' => 'website','description' => 'Generate engaging subheadlines for products and services.','prompt' => 'Generate in the :language language, a subheadline for a product named: :product. The product is about: :description. The audience of the product will include: :audience. The voice tone is: :tone.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'testimonial','name' => 'Testimonial','icon' => 'format-quote','category_id' => 'website','description' => 'Generate testimonials based on the name and description of a product or service.','prompt' => 'Generate in the :language language, a testimonial of about 25 words for a product named: :product. The product is about: :description.','views' => '15','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-03-05 17:04:26'],
            ['user_id' => '0','slug' => 'tweet','name' => 'Tweet','icon' => 'twitter','category_id' => 'social','description' => 'Generate engaging tweets based on a description.','prompt' => 'Generate in the :language language, a tweet about: :description.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'twitter-thread','name' => 'Twitter thread','icon' => 'twitter-thread','category_id' => 'social','description' => 'Generate engaging twitter threads based on a description.','prompt' => 'Generate in the :language language, a twitter thread about: :description.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'value-proposition','name' => 'Value proposition','icon' => 'diamond','category_id' => 'marketing','description' => 'Generate value propositions for a product or service.','prompt' => 'Generate in the :language language, a value proposition for the product: :product. The audience is: :audience. The voice tone is: :tone. The company is about :description.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'video-description','name' => 'Video description','icon' => 'video-description','category_id' => 'social','description' => 'Generate compelling video descriptions based on a description.','prompt' => 'Generate in the :language language, a video description about: :description.','views' => '1','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'video-script','name' => 'Video script','icon' => 'subtitle','category_id' => 'social','description' => 'Generate detailed video scripts based on a description.','prompt' => 'Generate in the :language language, a video script for a video about: :description. The voice tone is: :tone.','views' => '127','created_at' => '2023-03-03 12:01:42','updated_at' => '2023-03-04 18:23:45'],
            ['user_id' => '0','slug' => 'video-tags','name' => 'Video tags','icon' => 'video-tags','category_id' => 'social','description' => 'Generate video tags for videos based on the video title.','prompt' => 'Generate in the :language language, tags separated by comma, for the video called: :title.','views' => '3','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-03-03 13:51:37'],
            ['user_id' => '0','slug' => 'video-title','name' => 'Video title','icon' => 'video-title','category_id' => 'social','description' => 'Generate engaging video titles based on the video description.','prompt' => 'Generate in the :language language, a video title about: :description.','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13'],
            ['user_id' => '0','slug' => 'vision-statement','name' => 'Vision statement','icon' => 'grade','category_id' => 'marketing','description' => 'Generate comprehensive and informative vision statements.','prompt' => 'Generate in the :language language, a vision statement for the company: :company. The voice tone is: :tone. The company is about :description','views' => '0','created_at' => '2023-02-26 01:11:13','updated_at' => '2023-02-26 01:11:13']
        ]);

        foreach (DB::table('templates')->select('*')->cursor() as $template) {
            DB::table('documents')->where('template_id', $template->slug)->update(['template_id' => $template->id]);
        }

        DB::table('settings')->insert([
            ['name' => 'openai_default_language', 'value' => 'en'],
        ]);

        DB::table('settings')->where('name', '=', 'openai_completions_model')->update(['value' => 'gpt-3.5-turbo']);

        DB::table('categories')->where('id', 'other')->update(['id' => 'custom', 'name' => 'Custom']);

        foreach (DB::table('plans')->select('*')->cursor() as $row) {
            $features = json_decode($row->features);

            DB::statement("UPDATE `plans` SET `features` = :features WHERE `id` = :id", ['features' => json_encode(['words' => $features->words, 'documents' => $features->documents, 'images' => $features->images, 'templates' => $features->templates, 'custom_templates' => 1, 'data_export' => $features->data_export, 'api' => $features->api]), 'id' => $row->id]);
        }
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
