<?php

namespace App\Http\Controllers\Api\WebSite\Tender;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebSite\Tender\TenderRequest;
use App\Http\Resources\Api\WebSite\Tender\TenderResource;
use App\Models\FavoriteTender;
use App\Models\Tender;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tenders = Tender::where('status', 'admin_accept')->where('expiry_date', '>', now())
        ->when($request->keyword, function($query) use($request){
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

        return TenderResource::collection($tenders)->additional(['status' => true, 'message' => null]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function myTenders(Request $request)
    {
        $tenders = Tender::when($request->type == 'my_tenders', function ($query) {
            $query->where('user_id', auth('api')->id());
        })->when($request->type == 'my_offers', function ($query) {
            $query->whereHas('offers', function ($query) {
                $query->where('user_id', auth('api')->id());
            });
        })->when($request->type == 'all', function ($query) {
            $query->where('user_id', auth('api')->id())->orWhereHas('offers', function ($query) {
                $query->where('user_id', auth('api')->id());
            });
        })->when(! in_array($request->type, ['my_tenders', 'my_offers', 'all']), function ($query) {
            $query->where('user_id', auth('api')->id());
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

        return TenderResource::collection($tenders)->additional(['status' => true, 'message' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TenderRequest $request)
    {
        DB::beginTransaction();
        try {
            $tender = Tender::create($request->safe()->except('category_ids') + ['user_id' => auth('api')->id(), 'status' => 'pending']);
            $tender->categories()->attach($request->validated('category_ids'));
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
        $tender = Tender::findOrFail($id);
        return (new TenderResource($tender))->additional(['status' => true, 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TenderRequest $request, $id)
    {
        $tender = Tender::where('user_id', auth('api')->id())->findOrFail($id);
        DB::beginTransaction();
        try {
            $tender->update($request->safe()->except('category_ids'));
            $tender->categories()->sync($request->category_ids);
            DB::commit();
            return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.update.successfully')]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.update.fail')], 422);
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
        $tender = Tender::where('user_id', auth('api')->id())->findOrFail($id);
        if ($tender->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }

    public function deleteTenderMedia($tender, $media)
    {
        $tender = Tender::where('user_id', auth('api')->id())->findOrFail($tender);
        $media  = $tender->media()->findOrFail($media);
        $media->delete();
        if (file_exists(storage_path('app/public/images/'.$media->media))){
            File::delete(storage_path('app/public/images/'.$media->media));
        }
        return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
    }

    public function toggelToFavorite($id)
    {
        $user_id = auth('api')->id();
        $tender = Tender::findOrFail($id);
        $favorite_tender = FavoriteTender::where(['user_id' => $user_id, 'tender_id' => $tender->id])->first();
        $favorite_tender ? $favorite_tender->delete() : FavoriteTender::create(['user_id' => $user_id, 'tender_id' => $tender->id]);

        return response()->json(['status' => true, 'data' => ['is_favorite' => $favorite_tender ? false : true], 'messages' => trans('dashboard.create.successfully')]);
    }

    public function favorite(Request $request)
    {
        $favorite_tenders = auth('api')->user()->tendersFavorite()
        ->when($request->keyword, function($query) use($request){
            $query->where('name', 'LIKE', '%'.$request->keyword.'%')
            ->orWhere('desc', 'LIKE', '%'.$request->keyword.'%');
        })->when($request->category_id, function ($query) use ($request) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('id', $request->category_id);
            });
        })->latest()->get();

        return TenderResource::collection($favorite_tenders)->additional(['status' => true, 'message' => '']);
    }
}
