<?php

namespace App\Http\Controllers\Api\Dashboard\City;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\City\CityRequest;
use App\Http\Resources\Api\Dashboard\City\CityResource;
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
        $cities = City::when($request->keyword, function($q) use($request){
            $q->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('slug', '%'.$request->keyword.'%');
        })->latest()->paginate();

        return CityResource::collection($cities)->additional(['status' => true, 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {
        $city = City::create($request->validated());

        return response()->json(['status' => true, 'message' => trans('dashboard.create.successfully')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $city = City::findOrFail($id);

        return (new CityResource($city))->additional(['status' => true, 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CityRequest $request, $id)
    {
        $city = City::findOrFail($id);
        $city->update($request->validated());

        return response()->json(['status' => true, 'message' => trans('dashboard.update.successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);

        if ($city->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }

        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
