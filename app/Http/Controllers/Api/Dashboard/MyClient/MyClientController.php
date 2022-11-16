<?php

namespace App\Http\Controllers\Api\Dashboard\MyClient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\MyClient\MyClientRequest;
use App\Http\Resources\Api\Dashboard\MyClient\MyClientResource;
use App\Models\MyClient;
use Illuminate\Http\Request;

class MyClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $my_clients = MyClient::all();
        return MyClientResource::collection($my_clients)->additional(['status' => true, 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MyClientRequest $request)
    {
        $my_client = MyClient::create($request->validated());
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
        $my_client = MyClient::findOrFail($id);
        return (new MyClientResource($my_client))->additional(['status' => true, 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MyClientRequest $request, $id)
    {
        $my_client = MyClient::findOrFail($id);
        $my_client->update($request->validated());
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
        $my_client = MyClient::findOrFail($id);
        if ($my_client->delete()) {
            if (file_exists(storage_path('app/public/images/my_clients/' . $this->avatar))) {
                unlink(storage_path('app/public/images/my_clients/' . $this->avatar));
            }
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
