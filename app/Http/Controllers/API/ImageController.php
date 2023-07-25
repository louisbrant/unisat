<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreImageRequest;
use App\Http\Requests\API\UpdateImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    use ImageTrait;

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
        $resolution = $request->input('resolution');
        $style = $request->input('style');
        $medium = $request->input('medium');
        $filter = $request->input('filter');
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        return ImageResource::collection(Image::where('user_id', $request->user()->id)
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->when($resolution, function ($query) use ($resolution) {
                return $query->ofResolution($resolution);
            })
            ->when($style, function ($query) use ($style) {
                return $query->ofStyle($style);
            })
            ->when($medium, function ($query) use ($medium) {
                return $query->ofMedium($medium);
            })
            ->when($filter, function ($query) use ($filter) {
                return $query->ofFilter($filter);
            })
            ->when(isset($favorite) && is_numeric($favorite), function ($query) use ($favorite) {
                return $query->ofFavorite($favorite);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'style' => $style, 'medium' => $medium, 'filter' => $filter, 'resolution' => $resolution, 'favorite' => $favorite, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]))
            ->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreImageRequest $request
     * @return ImageResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreImageRequest $request)
    {
        if (!$request->input('variations')) {
            try {
                $created = $this->imageStore($request, $request->input('description'));

                if ($created) {
                    return ImageResource::make($created);
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
     * @return ImageResource|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $image = Image::where([['id', '=', $id], ['user_id', $request->user()->id]])->first();

        if ($image) {
            return ImageResource::make($image);
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateImageRequest $request
     * @param $id
     * @return ImageResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateImageRequest $request, $id)
    {
        $image = Image::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->first();

        if ($image) {
            $updated = $this->imageUpdate($request, $image);

            if ($updated) {
                return ImageResource::make($updated);
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
        $image = Image::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->first();

        if ($image) {
            $image->delete();

            return response()->json([
                'id' => $image->id,
                'object' => 'image',
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
