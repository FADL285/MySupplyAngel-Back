<?php

namespace App\Http\Controllers\Api\WebSite\City;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WebSite\City\CityResource;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = City::when($request->keyword, function($q) use($request){
            $q->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('slug', '%'.$request->keyword.'%');
        })->latest()->paginate();

        return CityResource::collection($categories)->additional(['status' => true, 'message' => null]);
    }

    public function withoutPagination(Request $request)
    {
        $categories = City::when($request->keyword, function($q) use($request){
            $q->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('slug', '%'.$request->keyword.'%');
        })->latest()->get();

        return CityResource::collection($categories)->additional(['status' => true, 'message' => null]);
    }
}
