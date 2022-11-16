<?php

namespace App\Http\Controllers\Api\WebSite\Country;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WebSite\Country\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Country::when($request->keyword, function($q) use($request){
            $q->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('currency', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('slug', '%'.$request->keyword.'%');
        })->latest()->paginate();

        return CountryResource::collection($categories)->additional(['status' => true, 'message' => null]);
    }

    public function withoutPagination(Request $request)
    {
        $categories = Country::when($request->keyword, function($q) use($request){
            $q->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('currency', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('slug', '%'.$request->keyword.'%');
        })->latest()->get();

        return CountryResource::collection($categories)->additional(['status' => true, 'message' => null]);
    }
}
