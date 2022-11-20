<?php

namespace App\Http\Controllers\Api\Dashboard\Tender;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\ChangeStatus\ChangeStatusRequest;
use App\Http\Requests\Api\Dashboard\Tender\TenderRequest;
use App\Http\Resources\Api\Dashboard\Tender\TenderResource;
use App\Models\Tender;
use App\Notifications\Dashboard\ChangeStatus\AdminChangeStatusNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tenders = Tender::where('status', 'admin_accept')
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TenderRequest $request)
    {
        DB::beginTransaction();
        try {
            $tender = Tender::create($request->safe()->except('category_ids') + ['status' => 'admin_accept']);
            $tender->categories()->attach($request->validated('category_ids'));
            DB::commit();
            return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.create.successfully')]);
        } catch (Exception $e) {
            DB::rollBack();
            info($e->getMessage());
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
        $tender = Tender::findOrFail($id);
        DB::beginTransaction();
        try {
            $tender->update($request->safe()->except('category_ids'));
            $tender->categories()->sync($request->category_ids);
            DB::commit();
            return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.update.successfully')]);
        } catch (Exception $e) {
            DB::rollBack();
            info($e->getMessage());
            return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.update.fail')], 422);
        }
    }

    public function changeStatus(ChangeStatusRequest $request, Tender $tender)
    {
        $tender->update(['status' => $request->status]);
        if ($tender->user)
        {
            Notification::send($tender->user, new AdminChangeStatusNotification($tender->id, 'tender', $request->status, ['database', 'broadcast']));
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
        $tender = Tender::findOrFail($id);
        if ($tender->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }

    public function deleteTenderMedia($tender, $media)
    {
        $tender = Tender::findOrFail($tender);
        $media  = $tender->media()->findOrFail($media);
        $media->delete();
        if (file_exists(storage_path('app/public/images/'.$media->media))){
            File::delete(storage_path('app/public/images/'.$media->media));
        }
        return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
    }
}
