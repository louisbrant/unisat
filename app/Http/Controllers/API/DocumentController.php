<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreDocumentRequest;
use App\Http\Requests\API\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Traits\DocumentTrait;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    use DocumentTrait;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
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

        return DocumentResource::collection(Document::with('template')
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
            ->appends(['search' => $search, 'template_id' => $templateId, 'favorite' => $favorite, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]))
            ->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDocumentRequest $request
     * @return DocumentResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreDocumentRequest $request)
    {
        if (!$request->input('variations')) {
            try {
                $created = $this->documentStore($request, $request->input('prompt'));

                if ($created) {
                    return DocumentResource::make($created);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'message' => __('An unexpected error has occurred, please try again.') . $e->getMessage(),
                    'status' => 500
                ], 500);
            }
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return DocumentResource|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $document = Document::where([['id', '=', $id], ['user_id', $request->user()->id]])->first();

        if ($document) {
            return DocumentResource::make($document);
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDocumentRequest $request
     * @param $id
     * @return DocumentResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateDocumentRequest $request, $id)
    {
        $document = Document::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->first();

        if ($document) {
            $updated = $this->documentUpdate($request, $document);

            if ($updated) {
                return DocumentResource::make($updated);
            }
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        $document = Document::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->first();

        if ($document) {
            $document->delete();

            return response()->json([
                'id' => $document->id,
                'object' => 'document',
                'deleted' => true,
                'status' => 200
            ], 200);
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }
}
