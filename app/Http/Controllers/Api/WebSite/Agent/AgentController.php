<?php

namespace App\Http\Controllers\Api\WebSite\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebSite\Agent\AgentRequest;
use App\Http\Resources\Api\WebSite\Agent\AgentResource;
use App\Models\Agent;
use App\Models\AgentFavorite;
use App\Models\User;
use App\Notifications\Website\Agent\AgentNotification;
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
        $agents = Agent::where(['status' => 'admin_accept'])->when($request->type, function ($query) use($request) {
            $query->where('type', $request->type);
        })->when(! isset($request->type), function ($query) use($request) {
            $query->where('type', 'required_agent_or_distrebutor');
        })->when($request->keyword, function($query) use($request){
            $query->where('title', 'LIKE', '%'.$request->keyword.'%')
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function myAgents(Request $request)
    {
        $agents = Agent::when($request->filter == 'my_agents', function ($query) {
            $query->where('user_id', auth('api')->id());
        })->when($request->filter == 'my_offers', function ($query) {
            $query->where('status', 'admin_accept')->whereHas('offers', function ($query) {
                $query->where('user_id', auth('api')->id());
            });
        })->when($request->filter == 'all', function ($query) {
            $query->where('user_id', auth('api')->id())->orWhereHas('offers', function ($query) {
                $query->where('user_id', auth('api')->id());
            });
        })->when(! in_array($request->filter, ['my_agents', 'my_offers', 'all']), function ($query) {
            $query->where('user_id', auth('api')->id());
        })->when($request->keyword, function($query) use($request){
            $query->where('title', 'LIKE', '%'.$request->keyword.'%')
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
            $agent = Agent::create($request->safe()->except('category_ids') + ['user_id' => auth('api')->id(), 'status' => setting('accepted') != 'automatic' ? 'pending' : 'admin_accept']);
            $agent->categories()->attach($request->validated('category_ids'));
            $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();
            Notification::send($admins, new AgentNotification($agent->id, 'new_agent', ['database', 'broadcast']));
            DB::commit();
            return response()->json(['status' => true, 'data' => null, 'message' => trans('website.create.successfully')]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'data' => null, 'messages' => trans('website.create.fail')], 422);
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
        if ($agent->user_id == auth('api')->id() or $agent->status == 'admin_accept') {
            return (new AgentResource($agent))->additional(['status' => true, 'message' => '']);
        }
        return response()->json(['status' => false, 'data' => null, 'message' => 'لم يتم العثور علي بيانات.']);
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
        $agent = Agent::where('user_id', auth('api')->id())->findOrFail($id);
        DB::beginTransaction();
        try {
            $agent->update($request->safe()->except('category_ids'));
            $agent->categories()->sync($request->category_ids);
            DB::commit();
            return response()->json(['status' => true, 'data' => null, 'message' => trans('website.update.successfully')]);
        } catch (Exception $e) {
            DB::rollBack();
            info($e->getMessage());
            return response()->json(['status' => false, 'data' => null, 'messages' => trans('website.update.fail')], 422);
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
        $agent = Agent::where('user_id', auth('api')->id())->findOrFail($id);
        if ($agent->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('website.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('website.delete.fail')], 422);
    }

    public function deleteAgentMedia($agent, $media)
    {
        $agent = Agent::where('user_id', auth('api')->id())->findOrFail($agent);
        $media  = $agent->media()->findOrFail($media);
        $media->delete();
        if (file_exists(storage_path('app/public/images/'.$media->media))){
            File::delete(storage_path('app/public/images/'.$media->media));
        }
        return response()->json(['status' => true, 'data' => null, 'messages' => trans('website.delete.successfully')]);
    }

    public function toggelToFavorite($id)
    {
        $user_id = auth('api')->id();
        $agent = Agent::where(['status' => 'admin_accept'])->findOrFail($id);
        $agent_favorite = AgentFavorite::where(['user_id' => $user_id, 'agent_id' => $agent->id])->first();
        $agent_favorite ? $agent_favorite->delete() : AgentFavorite::create(['user_id' => $user_id, 'agent_id' => $agent->id]);

        return response()->json(['status' => true, 'data' => ['is_favorite' => $agent_favorite ? false : true], 'messages' => trans('website.create.successfully')]);
    }

    public function favorite(Request $request)
    {
        $agents_favorite = auth('api')->user()->agentFavorites()
        ->when($request->keyword, function($query) use($request){
            $query->where('title', 'LIKE', '%'.$request->keyword.'%')
            ->orWhere('desc', 'LIKE', '%'.$request->keyword.'%');
        })->when($request->category_id, function ($query) use ($request) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('id', $request->category_id);
            });
        })->latest()->paginate();

        return AgentResource::collection($agents_favorite)->additional(['status' => true, 'message' => '']);
    }
}
