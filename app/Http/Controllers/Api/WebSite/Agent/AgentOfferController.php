<?php

namespace App\Http\Controllers\Api\WebSite\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebSite\Agent\AgentOfferRequest;
use App\Http\Resources\Api\WebSite\Agent\AgentOfferResource;
use App\Http\Resources\Api\WebSite\Agent\AgentResource;
use App\Models\Agent;
use App\Models\AgentOffer;
use App\Notifications\Website\Agent\AgentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

class AgentOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $agent = Agent::where(['user_id' => auth('api')->id(), 'type' => 'required_agent_or_distrebutor'])->findOrFail($id);
        $offers = $agent->offers()->when($request->keyword, function($query) use($request){
            $query->Where('desc', 'LIKE', '%'.$request->keyword.'%');
        })->latest()->get();
        return AgentOfferResource::collection($offers)->additional(['status' => true, 'message' => null]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function myAgentOffers(Request $request)
    {
        $my_agents_offer_id = AgentOffer::Where('user_id', auth('api')->id())->pluck('agent_id')->toArray();
        $agents = Agent::where('user_id', '!=', auth('api')->id())->whereIn('id', $my_agents_offer_id)->get();
        return AgentResource::collection($agents)->additional(['status' => true, 'message' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AgentOfferRequest $request, $id)
    {
        $user_id = auth('api')->id();
        $agent = Agent::where('user_id', '!=', $user_id)->where(['status' => 'admin_accept', 'type' => 'required_agent_or_distrebutor'])->where('expiry_date', '>', now())->findOrFail($id);
        $agent->offers()->create( ['desc' => $request->validated('desc'), 'user_id' => $user_id]);
        Notification::send($agent->user, new AgentNotification($agent->id, 'add_offer', ['database', 'broadcast']));
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.create.successfully')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AgentOfferRequest $request, $agent, $offer)
    {
        $user_id = auth('api')->id();
        $agent = Agent::where('user_id', '!=', $user_id)->where('type', 'required_agent_or_distrebutor')->findOrFail($agent, $offer);
        $agent_offer = $agent->offers()->findOrFail($offer);
        $agent_offer->update(['desc' => $request->validated('desc')]);
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.update.successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($agent, $agent_offer)
    {
        $agent_offer = AgentOffer::where(['agent_id' => $agent, 'user_id' => auth('api')->id()])->findOrFail($agent_offer);
        if ($agent_offer->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('website.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('website.delete.fail')], 422);
    }

    public function deleteAgentOfferMedia($agent, $offer, $media)
    {
        $agent = Agent::where('type', 'potential_agent_or_potential_distrebutor')->findOrFail($agent);
        $offer  = $agent->offers()->where('user_id', auth('api')->id())->findOrFail($offer);
        $media  = $offer->media()->findOrFail($media);
        $media->delete();
        if (file_exists(storage_path('app/public/images/'.$media->media))){
            File::delete(storage_path('app/public/images/'.$media->media));
        }
        return response()->json(['status' => true, 'data' => null, 'messages' => trans('website.delete.successfully')]);
    }
}
