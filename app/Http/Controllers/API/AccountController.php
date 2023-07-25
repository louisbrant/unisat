<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display the resource.
     *
     * @param Request $request
     * @return AccountResource|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->user()) {
            return AccountResource::make($request->user());
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }
}
