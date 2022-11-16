<?php

namespace App\Http\Controllers\Api\Dashboard\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Client\ClientRequest;
use App\Http\Resources\Api\Dashboard\Client\ClientResource;
use App\Http\Resources\Api\Dashboard\Client\SelectClientResource;
use App\Http\Resources\Api\Dashboard\Client\SimpleClientResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clients = User::where('user_type', 'client')
        ->whereHas('profile', function($query) use($request) {
            $query->when($request->country_id, function($query) use($request){
                $query->where('country_id', $request->country_id);
            })->when($request->city_id, function($query) use($request) {
                $query->where('city_id', $request->city_id);
            });
        })->when($request->keyword, function($query) use($request){
            $query->where(function($query) use($request){
                $query->where('fullname', 'like', '%' . $request->keyword . '%')
                ->orWhere('email', 'like', '%' . $request->keyword . '%')
                ->orWhere('phone', 'like', '%' . $request->keyword . '%');
            });
        })->when($request->start_date && $request->end_date, function($query) use($request){
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        })->latest()->paginate(10);

        return SimpleClientResource::collection($clients)->additional(['status' => true, 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientRequest $request)
    {
        DB::beginTransaction();
        try{
            $client = User::create($request->safe()->except(['avatar', 'country_id', 'city_id']) + ["user_type" => "client"]);
            $client->profile()->create($request->safe()->only(['country_id', 'city_id']));
            $client->wallet()->create();
            DB::commit();
            return ClientResource::make($client->fresh())->additional(['status' => true, 'message' => trans('dashboard.create.success')]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.create.fail')], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = User::where('user_type', 'client')->findOrFail($id);
        return ClientResource::make($client)->additional(['status' => true, 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClientRequest $request, $id)
    {
        $client = User::where('user_type', 'client')->findOrFail($id);

        DB::beginTransaction();
        try{
            $client->update($request->safe()->except(['avatar', 'country_id', 'city_id']));
            $client->profile()->updateOrCreate(['user_id' => $client->id], $request->safe()->only(['country_id', 'city_id']));
            DB::commit();
            return ClientResource::make($client->fresh())->additional(['status' => true, 'message' => trans('dashboard/api.update.success')]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard/api.update.fail')], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('user_type', 'client')->findOrFail($id);

        if ($user->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.success')]);
        }

        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }

    public function clientsWithoutPagination()
    {
        $clients = User::where('user_type', 'client')->latest()->get();

        return SelectClientResource::collection($clients)->additional(['status' => true, 'message' => '']);
    }
}
