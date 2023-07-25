<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\Template;
use App\Traits\DocumentTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Csv as CSV;

class DocumentController extends Controller
{
    use DocumentTrait;

    /**
     * List the Documents.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'result']) ? $request->input('search_by') : 'name';
        $templateId = $request->input('template_id');
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $documents = Document::with('template')
            ->where('user_id', $request->user()->id)
            ->when($search, function ($query) use ($search, $searchBy) {
                if ($searchBy == 'result') {
                    return $query->searchResult($search);
                }
                return $query->searchName($search);
            })
            ->when(isset($templateId), function ($query) use ($templateId) {
                return $query->ofTemplate($templateId);
            })
            ->when(isset($favorite) && is_numeric($favorite), function ($query) use ($favorite) {
                return $query->ofFavorite($favorite);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'template_id' => $templateId, 'favorite' => $favorite, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        $templates = Template::all();

        return view('documents.container', ['view' => 'list', 'documents' => $documents, 'templates' => $templates]);
    }

    /**
     * Show the create Document form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('documents.container', ['view' => 'new']);
    }

    /**
     * Show the edit Document form.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $document = Document::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        return view('documents.container', ['view' => 'edit', 'document' => $document]);
    }

    /**
     * Show the Document.
     */
    public function show(Request $request, $id)
    {
        $document = Document::where([['id', $id]])->firstOrFail();

        if (!$request->user() || $request->user()->id != $document->user_id && $request->user()->role == 0) {
            abort(403);
        }

        return view('documents.container', ['view' => 'show', 'document' => $document]);
    }

    /**
     * Store the Document.
     *
     * @param StoreDocumentRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function store(StoreDocumentRequest $request)
    {
        try {
            $documents = $this->documentsStore($request, $request->input('prompt'));
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('documents.container', ['view' => 'new', 'documents' => $documents, 'name' => $request->input('name'), 'prompt' => $request->input('prompt'), 'creativity' => $request->input('creativity'), 'variations' => $request->input('variations'), 'language' => $request->input('language')]);
    }

    /**
     * Update the Document.
     *
     * @param UpdateDocumentRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDocumentRequest $request, $id)
    {
        $document = Document::where([['id', $id]])->firstOrFail();

        if (!$request->user() || $request->user()->id != $document->user_id && $request->user()->role == 0) {
            abort(403);
        }

        $this->documentUpdate($request, $document);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Document.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        $document = Document::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        $document->delete();

        return redirect()->route('documents')->with('success', __(':name has been deleted.', ['name' => $document->name]));
    }

    /**
     * Export the Documents.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws CSV\CannotInsertRecord
     */
    public function export(Request $request)
    {
        if ($request->user()->cannot('dataExport', ['App\Models\User'])) {
            abort(403);
        }

        $now = Carbon::now();
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'result']) ? $request->input('search_by') : 'name';
        $templateId = $request->input('template_id');
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';

        $documents = Document::with('template')
            ->where('user_id', $request->user()->id)
            ->when($search, function ($query) use ($search, $searchBy) {
                if ($searchBy == 'result') {
                    return $query->searchResult($search);
                }
                return $query->searchName($search);
            })
            ->when(isset($templateId), function ($query) use ($templateId) {
                return $query->ofTemplate($templateId);
            })
            ->when(isset($favorite) && is_numeric($favorite), function ($query) use ($favorite) {
                return $query->ofFavorite($favorite);
            })
            ->orderBy($sortBy, $sort)
            ->get();

        $content = CSV\Writer::createFromFileObject(new \SplTempFileObject);

        // Generate the header
        $content->insertOne([__('Type'), __('Documents')]);
        $content->insertOne([__('Date'), $now->tz($request->user()->timezone ?? config('app.timezone'))->format(__('Y-m-d')) . ' ' . $now->tz($request->user()->timezone ?? config('app.timezone'))->format('H:i:s') . ' (' . $now->tz($request->user()->timezone ?? config('app.timezone'))->getOffsetString() . ')']);
        $content->insertOne([__('URL'), $request->fullUrl()]);
        $content->insertOne([__(' ')]);

        // Generate the content
        $content->insertOne([__('ID'), __('Name'), __('Result'), __('Words'), __('Favorite'), __('Updated at'), __('Created at')]);
        foreach ($documents as $document) {
            $content->insertOne([$document->id, $document->name, $document->result, $document->words, $document->favorite, $document->updated_at->tz($request->user()->timezone ?? config('app.timezone'))->format(__('Y-m-d')), $document->created_at->tz($request->user()->timezone ?? config('app.timezone'))->format(__('Y-m-d'))]);
        }

        return response((string) $content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . formatTitle([__('Documents'), config('settings.title')]) . '.csv"',
        ]);
    }
}
