<?php

namespace App\Http\Controllers\Api\Dashboard\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Agent\AgentRequest;
use App\Http\Requests\Api\Dashboard\ChangeStatus\ChangeStatusRequest;
use App\Http\Resources\Api\Dashboard\Agent\AgentResource;
use App\Models\Agent;
use App\Notifications\Dashboard\ChangeStatus\AdminChangeStatusNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $agents = Agent::when($request->type, function ($query) use($request) {
            $query->where('type', $request->type);
        })->when(! isset($request->type), function ($query) use($request) {
            $query->where('type', 'required_agent_or_distrebutor');
        })->when($request->keyword, function($query) use($request){
            $query->where('name', 'LIKE', '%'.$request->keyword.'%')
            ->orWhere('desc', 'LIKE', '%'.$request->keyword.'%');
        })->when($request->category_id, function ($query) use ($request) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('id', $request->category_id);
            });
        })->when($request->country_id, function ($query) use ($request) {
            $query->whereHas('user', function ($query) use ($request) {
                $query->whereHas('profile', function ($query) use ($request) {
                    $query->where('country_id', $request->country_id);
                });
            });
        })->latest()->paginate();

        return AgentResource::collection($agents)->additional(['status' => true, 'message' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AgentRequest $request)
    {
        DB::beginTransaction();
        try {
            $agent = Agent::create($request->safe()->except('category_ids') + ['status' => 'admin_accept']);
            $agent->categories()->attach($request->validated('category_ids'));
            DB::commit();
            return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.create.successfully')]);
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
        $agent = Agent::findOrFail($id);
        return (new AgentResource($agent))->additional(['status' => true, 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AgentRequest $request, $id)
    {
        $agent = Agent::findOrFail($id);
        DB::beginTransaction();
        try {
            $agent->update($request->safe()->except('category_ids'));
            $agent->categories()->sync($request->category_ids);
            DB::commit();
            return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.update.successfully')]);
        } catch (Exception $e) {
            DB::rollBack();
            info($e->getMessage());
            return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.update.fail')], 422);
        }
    }

    public function changeStatus(ChangeStatusRequest $request, Agent $agent)
    {
        $agent->update(['status' => $request->status]);
        if ($agent->user)
        {
            Notification::send($agent->user, new AdminChangeStatusNotification($agent->id, 'agent', $request->status, ['database', 'broadcast']));
        }
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
        $agent = Agent::findOrFail($id);
        if ($agent->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }

    public function deleteAgentMedia($agent, $media)
    {
        $agent = Agent::findOrFail($agent);
        $media  = $agent->media()->findOrFail($media);
        $media->delete();
        if (file_exists(storage_path('app/public/images/'.$media->media))){
            File::delete(storage_path('app/public/images/'.$media->media));
        }
        return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
    }
}
