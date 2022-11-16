<?php

namespace App\Http\Controllers\Api\Dashboard\Country;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Country\CountryRequest;
use App\Http\Resources\Api\Dashboard\Country\CountryResource;
use App\Models\Country;
use App\Models\User;
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
        $countries = Country::when($request->keyword, function($q) use($request){
            $q->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('currency', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('slug', '%'.$request->keyword.'%');
        })->latest()->paginate();

        return CountryResource::collection($countries)->additional(['status' => true, 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CountryRequest $request)
    {
        $country = Country::create($request->safe()->except(['image']));
        return CountryResource::make($country)->additional(['status' => true, 'message' => trans('dashboard.create.success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $country = Country::findOrFail($id);
        return CountryResource::make($country)->additional(['status' => true, 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CountryRequest $request, $id)
    {
        $country = Country::findOrFail($id);

        if($request->phone_code && $request->phone_code != null && $request->phone_code != $country->phonecode)
        {
            User::where('phone_code', $country->phonecode)->update(['phone_code' => $request->phone_code]);
        }

        $country->update($request->safe()->except(['image']));

        return CountryResource::make($country)->additional(['status' => true, 'message' => trans('dashboard.update.success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);

        if ($country->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.success')]);
        }

        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
