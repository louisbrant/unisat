<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTranscriptionRequest;
use App\Http\Requests\UpdateTranscriptionRequest;
use App\Models\Transcription;
use App\Traits\TranscriptionTrait;
use Illuminate\Http\Request;

class TranscriptionController extends Controller
{
    use TranscriptionTrait;

    /**
     * List the Transcriptions.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'result']) ? $request->input('search_by') : 'name';
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $transcriptions = Transcription::where('user_id', $request->user()->id)
            ->when($search, function ($query) use ($search, $searchBy) {
                if ($searchBy == 'result') {
                    return $query->searchResult($search);
                }
                return $query->searchName($search);
            })
            ->when(isset($favorite) && is_numeric($favorite), function ($query) use ($favorite) {
                return $query->ofFavorite($favorite);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'favorite' => $favorite, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        return view('transcriptions.container', ['view' => 'list', 'transcriptions' => $transcriptions]);
    }

    /**
     * Show the create Transcription form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('transcriptions.container', ['view' => 'new']);
    }

    /**
     * Show the edit Transcription form.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $transcription = Transcription::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        return view('transcriptions.container', ['view' => 'edit', 'transcription' => $transcription]);
    }

    /**
     * Show the Transcription.
     */
    public function show(Request $request, $id)
    {
        $transcription = Transcription::where([['id', $id]])->firstOrFail();

        if (!$request->user() || $request->user()->id != $transcription->user_id && $request->user()->role == 0) {
            abort(403);
        }

        return view('transcriptions.container', ['view' => 'show', 'transcription' => $transcription]);
    }

    /**
     * Store the Transcription.
     *
     * @param StoreTranscriptionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTranscriptionRequest $request)
    {
        try {
            $this->transcriptionStore($request);
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return redirect()->route('transcriptions')->with('success', __(':name has been created.', ['name' => $request->input('name')]));
    }

    /**
     * Update the Transcription.
     *
     * @param UpdateTranscriptionRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTranscriptionRequest $request, $id)
    {
        $transcription = Transcription::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        $this->transcriptionUpdate($request, $transcription);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Transcription.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        $transcription = Transcription::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        $transcription->delete();

        return redirect()->route('transcriptions')->with('success', __(':name has been deleted.', ['name' => $transcription->name]));
    }
}
