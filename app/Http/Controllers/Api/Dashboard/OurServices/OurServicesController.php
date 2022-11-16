<?php

namespace App\Http\Controllers\Api\Dashboard\OurServices;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\OurServices\OurServicesRequest;
use App\Http\Resources\Api\Dashboard\OurServices\OurServicesResource;
use App\Models\OurServices;
use Illuminate\Http\Request;

class OurServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $our_services = OurServices::all();
        return OurServicesResource::collection($our_services)->additional(['status' => true, 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OurServicesRequest $request)
    {
        $our_service = OurServices::create($request->validated());
        return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.create.successfully')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $our_service = OurServices::findOrFail($id);
        return (new OurServicesResource($our_service))->additional(['status' => true, 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OurServicesRequest $request, $id)
    {
        $our_service = OurServices::findOrFail($id);
        $our_service->update($request->validated());
        return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.update.successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $our_service = OurServices::findOrFail($id);
        if ($our_service->delete()) {
            if (file_exists(storage_path('app/public/images/our_services/' . $this->avatar))) {
                unlink(storage_path('app/public/images/our_services/' . $this->avatar));
            }
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
