<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreChatRequest;
use App\Http\Requests\API\UpdateChatRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Traits\ChatTrait;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    use ChatTrait;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name']) ? $request->input('search_by') : 'name';
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        return ChatResource::collection(Chat::where('user_id', $request->user()->id)
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->when(isset($favorite) && is_numeric($favorite), function ($query) use ($favorite) {
                return $query->ofFavorite($favorite);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'favorite' => $favorite, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]))
            ->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreChatRequest $request
     * @return ChatResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreChatRequest $request)
    {
        try {
            $created = $this->chatStore($request, $request->input('description'));

            if ($created) {
                return ChatResource::make($created);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('An unexpected error has occurred, please try again.'),
                'status' => 500
            ], 500);
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
     * @return ChatResource|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $chat = Chat::where([['id', '=', $id], ['user_id', $request->user()->id]])->first();

        if ($chat) {
            return ChatResource::make($chat);
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateChatRequest $request
     * @param $id
     * @return ChatResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateChatRequest $request, $id)
    {
        $chat = Chat::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->first();

        if ($chat) {
            $updated = $this->chatUpdate($request, $chat);

            if ($updated) {
                return ChatResource::make($updated);
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
        $chat = Chat::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->first();

        if ($chat) {
            $chat->delete();

            return response()->json([
                'id' => $chat->id,
                'object' => 'chat',
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
