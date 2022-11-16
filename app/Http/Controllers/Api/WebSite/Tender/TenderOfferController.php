<?php

namespace App\Http\Controllers\Api\WebSite\Tender;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebSite\Tender\TenderOfferRequest;
use App\Http\Resources\Api\WebSite\Tender\TenderOfferResource;
use App\Http\Resources\Api\WebSite\Tender\TenderResource;
use App\Models\Tender;
use App\Models\TenderOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TenderOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $tender = Tender::where('user_id', auth('api')->id())->findOrFail($id);
        $offers = $tender->offers()->when($request->keyword, function($query) use($request){
            $query->Where('desc', 'LIKE', '%'.$request->keyword.'%');
        })->latest()->get();
        return TenderOfferResource::collection($offers)->additional(['status' => true, 'message' => null]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function myTenderOffers(Request $request)
    {
        $my_tenders_offer_id = TenderOffer::Where('user_id', auth('api')->id())->pluck('tender_id')->toArray();
        $tenders = Tender::where('user_id', '!=', auth('api')->id())->whereIn('id', $my_tenders_offer_id)->get();
        return TenderResource::collection($tenders)->additional(['status' => true, 'message' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TenderOfferRequest $request, $id)
    {
        $user_id = auth('api')->id();
        $tender = Tender::where('user_id', '!=', $user_id)->where('status', 'admin_accept')->where('expiry_date', '>', now())->findOrFail($id);
        $tender->offers()->create( ['desc' => $request->validated('desc'), 'user_id' => $user_id]);
        return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.create.successfully')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TenderOfferRequest $request, $tender, $offer)
    {
        $user_id = auth('api')->id();
        $tender = Tender::where('user_id', '!=', $user_id)->findOrFail($tender, $offer);
        $tender_offer = $tender->offers()->findOrFail($offer);
        $tender_offer->update(['desc' => $request->validated('desc')]);
        return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.update.successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($tender, $tender_offer)
    {
        $tender_offer = TenderOffer::where(['tender_id' => $tender, 'user_id' => auth('api')->id()])->findOrFail($tender_offer);
        if ($tender_offer->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }

    public function deleteTenderOfferMedia($tender, $offer, $media)
    {
        $tender = Tender::findOrFail($tender);
        $offer  = $tender->offers()->where('user_id', auth('api')->id())->findOrFail($offer);
        $media  = $offer->media()->findOrFail($media);
        $media->delete();
        if (file_exists(storage_path('app/public/images/'.$media->media))){
            File::delete(storage_path('app/public/images/'.$media->media));
        }
        return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
    }
}
