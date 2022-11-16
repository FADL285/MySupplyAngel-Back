<?php

namespace App\Http\Controllers\Api\WebSite\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WebSite\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::when($request->keyword, function($query) use($request){
            $query->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('desc', '%'.$request->keyword.'%');
        })->latest()->paginate();

        return CategoryResource::collection($categories)->additional(['status' => true, 'message' => null]);
    }

    public function withoutPagination(Request $request)
    {
        $categories = Category::when($request->keyword, function($query) use($request){
            $query->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('desc', '%'.$request->keyword.'%');
        })->latest()->get();

        return CategoryResource::collection($categories)->additional(['status' => true, 'message' => null]);
    }
}
