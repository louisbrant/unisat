<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessAboutUsRequest;
use App\Http\Requests\ProcessAdvertisementRequest;
use App\Http\Requests\ProcessArticleRequest;
use App\Http\Requests\ProcessBlogIntroRequest;
use App\Http\Requests\ProcessBlogOutlineRequest;
use App\Http\Requests\ProcessBlogOutroRequest;
use App\Http\Requests\ProcessBlogParagraphRequest;
use App\Http\Requests\ProcessBlogPostRequest;
use App\Http\Requests\ProcessBlogSectionRequest;
use App\Http\Requests\ProcessBlogTagsRequest;
use App\Http\Requests\ProcessBlogTalkingPointsRequest;
use App\Http\Requests\ProcessBlogTitleRequest;
use App\Http\Requests\ProcessContentGrammarRequest;
use App\Http\Requests\ProcessContentSummaryRequest;
use App\Http\Requests\ProcessFaqRequest;
use App\Http\Requests\ProcessFreestyleRequest;
use App\Http\Requests\ProcessHashtagsRequest;
use App\Http\Requests\ProcessHowItWorksRequest;
use App\Http\Requests\ProcessBlogListicleRequest;
use App\Http\Requests\ProcessJobDescriptionRequest;
use App\Http\Requests\ProcessMetaDescriptionRequest;
use App\Http\Requests\ProcessMetaKeywordsRequest;
use App\Http\Requests\ProcessMissionStatementRequest;
use App\Http\Requests\ProcessNewsletterRequest;
use App\Http\Requests\ProcessContentRewriteRequest;
use App\Http\Requests\ProcessPainAgitateSolutionRequest;
use App\Http\Requests\ProcessParagraphRequest;
use App\Http\Requests\ProcessCallToActionRequest;
use App\Http\Requests\ProcessPressReleaseRequest;
use App\Http\Requests\ProcessProductSheetRequest;
use App\Http\Requests\ProcessProsConsRequest;
use App\Http\Requests\ProcessPushNotificationRequest;
use App\Http\Requests\ProcessReviewRequest;
use App\Http\Requests\ProcessShowRequest;
use App\Http\Requests\ProcessSocialPostCaptionRequest;
use App\Http\Requests\ProcessSocialPostRequest;
use App\Http\Requests\ProcessStartupIdeasRequest;
use App\Http\Requests\ProcessStartupNamesRequest;
use App\Http\Requests\ProcessTestimonialRequest;
use App\Http\Requests\ProcessHeadlineRequest;
use App\Http\Requests\ProcessSubheadlineRequest;
use App\Http\Requests\ProcessTweetRequest;
use App\Http\Requests\ProcessTwitterThreadRequest;
use App\Http\Requests\ProcessValuePropositionRequest;
use App\Http\Requests\ProcessVideoDescriptionRequest;
use App\Http\Requests\ProcessVideoScriptRequest;
use App\Http\Requests\ProcessVideoTagsRequest;
use App\Http\Requests\ProcessVideoTitleRequest;
use App\Http\Requests\ProcessVisionStatementRequest;
use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Models\Template;
use App\Models\Category;
use App\Traits\DocumentTrait;
use App\Traits\TemplateTrait;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    use TemplateTrait, DocumentTrait;

    /**
     * List the Templates.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $templates = Template::with('category')->whereIn('user_id', [0, $request->user()->id])->orderByRaw("FIELD(category_id, 'website', 'marketing', 'social', 'custom') ASC, `name` ASC")->paginate(500);

        return view('templates.list', ['templates' => $templates]);
    }

    /**
     * Show the create Template form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('templates.container', ['view' => 'new']);
    }

    /**
     * Show the edit Template form.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $template = Template::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        return view('templates.container', ['view' => 'edit', 'template' => $template]);
    }

    /**
     * Show the Template.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $template = Template::where('id', '=', $id)->whereIn('user_id', [0, $request->user()->id])->firstOrFail();

        return view('templates.container', ['view' => 'show', 'template' => $template]);
    }

    /**
     * Store the Template.
     *
     * @param StoreTemplateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTemplateRequest $request)
    {
        $this->templateStore($request);

        return redirect()->route('templates')->with('success', __(':name has been created.', ['name' => $request->input('name')]));
    }

    /**
     * Update the Template.
     *
     * @param UpdateTemplateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTemplateRequest $request, $id)
    {
        $template = Template::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        $this->templateUpdate($request, $template);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Process the Template.
     *
     * @param ProcessShowRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processShow(ProcessShowRequest $request, $id)
    {
        $template = Template::where('id', '=', $id)->whereIn('user_id', [0, $request->user()->id])->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($request->input('prompt')));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'show', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'prompt' => $request->input('prompt'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Delete the Template.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        $template = Template::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        $template->delete();

        return redirect()->route('templates')->with('success', __(':name has been deleted.', ['name' => $template->name]));
    }

    /**
     * Show the Article form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function article(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'article', 'template' => $template]);
    }

    /**
     * Process the Article.
     *
     * @param ProcessArticleRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processArticle(ProcessArticleRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'keywords' => $request->input('keywords'), 'subheadings' => $request->input('subheadings'), 'length' => $request->input('length')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'article', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'keywords' => $request->input('keywords'), 'subheadings' => $request->input('subheadings'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language'), 'length' => $request->input('length')]);
    }

    /**
     * Show the Paragraph form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function paragraph(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'paragraph', 'template' => $template]);
    }

    /**
     * Process the Paragraph.
     *
     * @param ProcessParagraphRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processParagraph(ProcessParagraphRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description'), 'keywords' => $request->input('keywords')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'paragraph', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'keywords' => $request->input('keywords'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Blog Post form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogPost(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'blog-post', 'template' => $template]);
    }

    /**
     * Process the Blog Post.
     *
     * @param ProcessParagraphRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processBlogPost(ProcessBlogPostRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description'), 'keywords' => $request->input('keywords'), 'length' => $request->input('length')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'blog-post', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'keywords' => $request->input('keywords'), 'length' => $request->input('length'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Blog Paragraph form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogParagraph(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'blog-paragraph', 'template' => $template]);
    }

    /**
     * Process the Blog Paragraph.
     *
     * @param ProcessParagraphRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processBlogParagraph(ProcessBlogParagraphRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'subheading' => $request->input('subheading')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'blog-paragraph', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'subheading' => $request->input('subheading'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Blog Title Generator form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogTitle(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'blog-title', 'template' => $template]);
    }

    /**
     * Process the Blog Title.
     *
     * @param ProcessBlogTitleRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processBlogTitle(ProcessBlogTitleRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'content' => $request->input('content')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'blog-title', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('content'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Blog Section form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogSection(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'blog-section', 'template' => $template]);
    }

    /**
     * Process the Blog Section.
     *
     * @param ProcessBlogSectionRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processBlogSection(ProcessBlogSectionRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'subheading' => $request->input('subheading')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'blog-section', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'subheading' => $request->input('subheading'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Blog Intro form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogIntro(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'blog-intro', 'template' => $template]);
    }

    /**
     * Process the Blog Intro.
     *
     * @param ProcessBlogIntroRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processBlogIntro(ProcessBlogIntroRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'content' => $request->input('content')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'blog-intro', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'content' => $request->input('content'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Blog Outro form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogOutro(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'blog-outro', 'template' => $template]);
    }

    /**
     * Process the Blog Outro.
     *
     * @param ProcessBlogOutroRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processBlogOutro(ProcessBlogOutroRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'content' => $request->input('content')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'blog-outro', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'content' => $request->input('content'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Blog Outline form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogOutline(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'blog-outline', 'template' => $template]);
    }

    /**
     * Process the Blog Outline.
     *
     * @param ProcessBlogOutlineRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processBlogOutline(ProcessBlogOutlineRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'content' => $request->input('content')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'blog-outline', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'content' => $request->input('content'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Blog Talking Points form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogTalkingPoints(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'blog-talking-points', 'template' => $template]);
    }

    /**
     * Process the Blog Talking Points.
     *
     * @param ProcessParagraphRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processBlogTalkingPoints(ProcessBlogTalkingPointsRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'subheading' => $request->input('subheading')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'blog-talking-points', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'subheading' => $request->input('subheading'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Content Rewrite form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contentRewrite(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'content-rewrite', 'template' => $template]);
    }

    /**
     * Process the Content Rewrite.
     *
     * @param ProcessParagraphRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processContentRewrite(ProcessContentRewriteRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'content' => $request->input('content')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'content-rewrite', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'content' => $request->input('content'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Content Summary form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contentSummary(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'content-summary', 'template' => $template]);
    }

    /**
     * Process the Content Summary.
     *
     * @param ProcessParagraphRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processContentSummary(ProcessContentSummaryRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'content' => $request->input('content')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'content-summary', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'content' => $request->input('content'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Headline form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function headline(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'headline', 'template' => $template]);
    }

    /**
     * Process the Headline.
     *
     * @param ProcessHeadlineRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processHeadline(ProcessHeadlineRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'headline', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Subheadline form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subheadline(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'subheadline', 'template' => $template]);
    }

    /**
     * Process the Subheadline.
     *
     * @param ProcessSubheadlineRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processSubheadline(ProcessSubheadlineRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documentss = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'subheadline', 'template' => $template, 'documents' => $documentss, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Call to Action form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function callToAction(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'call-to-action', 'template' => $template]);
    }

    /**
     * Process the Call to Action.
     *
     * @param ProcessCallToActionRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processCallToAction(ProcessCallToActionRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'call-to-action', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Testimonial form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function testimonial(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'testimonial', 'template' => $template]);
    }

    /**
     * Process the Testimonial.
     *
     * @param ProcessTestimonialRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processTestimonial(ProcessTestimonialRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'testimonial', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Meta Description form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function metaDescription(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'meta-description', 'template' => $template]);
    }

    /**
     * Process the Meta Description.
     *
     * @param ProcessMetaDescriptionRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processMetaDescription(ProcessMetaDescriptionRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'keywords' => $request->input('keywords'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'meta-description', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'keywords' => $request->input('keywords'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the About Us form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function aboutUs(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'about-us', 'template' => $template]);
    }

    /**
     * Process the About Us.
     *
     * @param ProcessAboutUsRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processAboutUs(ProcessAboutUsRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'about-us', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Advertisement form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function advertisement(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'advertisement', 'template' => $template]);
    }

    /**
     * Process the Advertisement.
     *
     * @param ProcessAdvertisementRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processAdvertisement(ProcessAdvertisementRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'audience' => $request->input('audience')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'advertisement', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'audience' => $request->input('audience'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Newsletter form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function newsletter(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'newsletter', 'template' => $template]);
    }

    /**
     * Process the Newsletter.
     *
     * @param ProcessNewsletterRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processNewsletter(ProcessNewsletterRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'company' => $request->input('company'), 'subject' => $request->input('subject'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'newsletter', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'company' => $request->input('company'), 'subject' => $request->input('subject'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Mission Statement form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function missionStatement(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'mission-statement', 'template' => $template]);
    }

    /**
     * Process the Mission Statement.
     *
     * @param ProcessMissionStatementRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processMissionStatement(ProcessMissionStatementRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'company' => $request->input('company'), 'description' => $request->input('description'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'mission-statement', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'company' => $request->input('company'), 'description' => $request->input('description'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Vision Statement form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visionStatement(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'vision-statement', 'template' => $template]);
    }

    /**
     * Process the Vision Statement.
     *
     * @param ProcessVisionStatementRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processVisionStatement(ProcessVisionStatementRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'company' => $request->input('company'), 'description' => $request->input('description'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'vision-statement', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'company' => $request->input('company'), 'description' => $request->input('description'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Press Release form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pressRelease(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'press-release', 'template' => $template]);
    }

    /**
     * Process the Press Release.
     *
     * @param ProcessPressReleaseRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processPressRelease(ProcessPressReleaseRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'company' => $request->input('company'), 'description' => $request->input('description'), 'subject' => $request->input('subject'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'press-release', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'company' => $request->input('company'), 'description' => $request->input('description'), 'subject' => $request->input('subject'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Value Proposition form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function valueProposition(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'value-proposition', 'template' => $template]);
    }

    /**
     * Process the Value Proposition.
     *
     * @param ProcessValuePropositionRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processValueProposition(ProcessValuePropositionRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'value-proposition', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Hashtags form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function hashtags(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'hashtags', 'template' => $template]);
    }

    /**
     * Process the Hashtags.
     *
     * @param ProcessHashtagsRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processHashtags(ProcessHashtagsRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'hashtags', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Tweet form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tweet(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'tweet', 'template' => $template]);
    }

    /**
     * Process the Tweet.
     *
     * @param ProcessTweetRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processTweet(ProcessTweetRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'tweet', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Twitter Thread form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function twitterThread(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'twitter-thread', 'template' => $template]);
    }

    /**
     * Process the Twitter Thread.
     *
     * @param ProcessTwitterThreadRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processTwitterThread(ProcessTwitterThreadRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'twitter-thread', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Video Title form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videoTitle(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'video-title', 'template' => $template]);
    }

    /**
     * Process the Video Title.
     *
     * @param ProcessVideoTitleRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processVideoTitle(ProcessVideoTitleRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'video-title', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Video Description form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videoDescription(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'video-description', 'template' => $template]);
    }

    /**
     * Process the Video Description.
     *
     * @param ProcessVideoDescriptionRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processVideoDescription(ProcessVideoDescriptionRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'video-description', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Video Tags form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videoTags(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'video-tags', 'template' => $template]);
    }

    /**
     * Process the Video Tags.
     *
     * @param ProcessVideoTagsRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processVideoTags(ProcessVideoTagsRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'video-tags', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Freestyle form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function freestyle(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'freestyle', 'template' => $template]);
    }

    /**
     * Process the Freestyle.
     *
     * @param ProcessFreestyleRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processFreestyle(ProcessFreestyleRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['prompt' => $request->input('prompt')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'freestyle', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'prompt' => $request->input('prompt'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the FAQ form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function faq(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'faq', 'template' => $template]);
    }

    /**
     * Process the FAQ.
     *
     * @param ProcessFaqRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processFaq(ProcessFaqRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'faq', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the How It Works form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function howItWorks(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'how-it-works', 'template' => $template]);
    }

    /**
     * Process the How It Works.
     *
     * @param ProcessHowItWorksRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processHowItWorks(ProcessHowItWorksRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'how-it-works', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Meta Keywords form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function metaKeywords(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'meta-keywords', 'template' => $template]);
    }

    /**
     * Process the Meta Keywords.
     *
     * @param ProcessMetaKeywordsRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processMetaKeywords(ProcessMetaKeywordsRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'meta-keywords', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Video Script form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videoScript(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'video-script', 'template' => $template]);
    }

    /**
     * Process the Video Script.
     *
     * @param ProcessVideoScriptRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processVideoScript(ProcessVideoScriptRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'video-script', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Startup Names form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function startupNames(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'startup-names', 'template' => $template]);
    }

    /**
     * Process the Startup Names.
     *
     * @param ProcessStartupNamesRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processStartupNames(ProcessStartupNamesRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description'), 'keywords' => $request->input('keywords')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'startup-names', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'keywords' => $request->input('keywords'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Startup Ideas form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function startupIdeas(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'startup-ideas', 'template' => $template]);
    }

    /**
     * Process the Startup Ideas.
     *
     * @param ProcessStartupIdeasRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processStartupIdeas(ProcessStartupIdeasRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'domains' => $request->input('domains')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'startup-ideas', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Pain-Agitate-Solution form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function painAgitateSolution(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'pain-agitate-solution', 'template' => $template]);
    }

    /**
     * Process the Pain-Agitate-Solution.
     *
     * @param ProcessTestimonialRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processPainAgitateSolution(ProcessPainAgitateSolutionRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'pain-agitate-solution', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Social Post form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function socialPost(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'social-post', 'template' => $template]);
    }

    /**
     * Process the Social Post.
     *
     * @param ProcessSocialPostRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processSocialPost(ProcessSocialPostRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'social-post', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Social Post Caption form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function socialPostCaption(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'social-post-caption', 'template' => $template]);
    }

    /**
     * Process the Social Post Caption.
     *
     * @param ProcessSocialPostCaptionRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processSocialPostCaption(ProcessSocialPostCaptionRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'social-post-caption', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Product Sheet form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productSheet(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'product-sheet', 'template' => $template]);
    }

    /**
     * Process the Product Sheet.
     *
     * @param ProcessProductSheetRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processProductSheet(ProcessProductSheetRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'product-sheet', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Welcome Email form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function welcomeEmail(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'welcome-email', 'template' => $template]);
    }

    /**
     * Process the Welcome Email.
     *
     * @param ProcessProductSheetRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processWelcomeEmail(ProcessProductSheetRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'welcome-email', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'tone' => $request->input('tone'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Push Notification form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pushNotification(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'push-notification', 'template' => $template]);
    }

    /**
     * Process the Push Notification.
     *
     * @param ProcessPushNotificationRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processPushNotification(ProcessPushNotificationRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'push-notification', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Blog Listicle form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogListicle(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'blog-listicle', 'template' => $template]);
    }

    /**
     * Process the Blog Listicle.
     *
     * @param ProcessBlogListicleRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processBlogListicle(ProcessBlogListicleRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'content' => $request->input('content')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'blog-listicle', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'content' => $request->input('content'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Content Grammar form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contentGrammar(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'content-grammar', 'template' => $template]);
    }

    /**
     * Process the Content Grammar.
     *
     * @param ProcessParagraphRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processContentGrammar(ProcessContentGrammarRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'content' => $request->input('content')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'content-grammar', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'content' => $request->input('content'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Blog Tags form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogTags(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'blog-tags', 'template' => $template]);
    }

    /**
     * Process the Blog Tags.
     *
     * @param ProcessBlogTagsRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processBlogTags(ProcessBlogTagsRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'content' => $request->input('content')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'blog-tags', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'content' => $request->input('content'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Pros and Cons form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function prosCons(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'pros-cons', 'template' => $template]);
    }

    /**
     * Process the Pros and Cons.
     *
     * @param ProcessTestimonialRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processProsCons(ProcessProsConsRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'pros-cons', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Google Advertisement form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function googleAdvertisement(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'google-advertisement', 'template' => $template]);
    }

    /**
     * Process the Google Advertisement.
     *
     * @param ProcessFaqRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processGoogleAdvertisement(ProcessFaqRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'google-advertisement', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Facebook Advertisement form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function facebookAdvertisement(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'facebook-advertisement', 'template' => $template]);
    }

    /**
     * Process the Facebook Advertisement.
     *
     * @param ProcessFaqRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processFacebookAdvertisement(ProcessFaqRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'facebook-advertisement', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Job Description form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function jobDescription(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'job-description', 'template' => $template]);
    }

    /**
     * Process the Job Description.
     *
     * @param ProcessJobDescriptionRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processJobDescription(ProcessJobDescriptionRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'position' => $request->input('position'), 'company' => $request->input('company'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'job-description', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'position' => $request->input('position'), 'company' => $request->input('company'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Review form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function review(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'review', 'template' => $template]);
    }

    /**
     * Process the Review.
     *
     * @param ProcessTestimonialRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processReview(ProcessReviewRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'product' => $request->input('product'), 'description' => $request->input('description')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'review', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Show the Feature Section form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function featureSection(Request $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();

        return view('templates.container', ['view' => 'feature-section', 'template' => $template]);
    }

    /**
     * Process the Feature Section.
     *
     * @param ProcessHowItWorksRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processFeatureSection(ProcessHowItWorksRequest $request)
    {
        $template = Template::where('slug', $request->segment(2))->firstOrFail();
        $template->views += 1;
        $template->save();

        try {
            $documents = $this->documentsStore($request, __($template->prompt, ['language' => mb_strtolower(config('languages')[$request->input('language')]['iso']), 'title' => $request->input('title'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'tone' => $request->input('tone')]));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('templates.container', ['view' => 'feature-section', 'template' => $template, 'documents' => $documents, 'name' => $request->input('name'), 'title' => $request->input('title'), 'product' => $request->input('product'), 'description' => $request->input('description'), 'audience' => $request->input('audience'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language'), 'tone' => $request->input('tone')]);
    }
}
