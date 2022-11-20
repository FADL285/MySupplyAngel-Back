<?php

namespace App\Http\Controllers\Api\Dashboard\Expiration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\ChangeStatus\ChangeStatusRequest;
use App\Http\Requests\Api\Dashboard\Expiration\ExpirationChangeRequest;
use App\Http\Requests\Api\Dashboard\Expiration\ExpirationRequest;
use App\Http\Resources\Api\Dashboard\Expiration\ExpirationResource;
use App\Models\Expiration;
use App\Notifications\Dashboard\ChangeStatus\AdminChangeStatusNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

class ExpirationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $expirations = Expiration::when($request->keyword, function($query) use($request){
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

        return ExpirationResource::collection($expirations)->additional(['status' => true, 'message' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExpirationRequest $request)
    {
        DB::beginTransaction();
        try {
            $expiration = Expiration::create($request->safe()->except('category_ids') + ['status' => 'admin_accept']);
            $expiration->categories()->attach($request->validated('category_ids'));
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
        $expiration = Expiration::findOrFail($id);
        return (new ExpirationResource($expiration))->additional(['status' => true, 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExpirationRequest $request, $id)
    {
        $expiration = Expiration::where('user_id', auth('api')->id())->findOrFail($id);
        DB::beginTransaction();
        try {
            $expiration->update($request->safe()->except('category_ids'));
            $expiration->categories()->sync($request->category_ids);
            DB::commit();
            return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.update.successfully')]);
        } catch (Exception $e) {
            DB::rollBack();
            info($e->getMessage());
            return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.update.fail')], 422);
        }
    }

    public function changeStatus(ChangeStatusRequest $request, Expiration $expiration)
    {
        $expiration->update(['status' => $request->status]);
        if ($expiration->user)
        {
            Notification::send($expiration->user, new AdminChangeStatusNotification($expiration->id, 'expiration', $request->status, ['database', 'broadcast']));
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
        $expiration = Expiration::findOrFail($id);
        if ($expiration->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }

    public function deleteExpirationMedia($expiration, $media)
    {
        $expiration = Expiration::findOrFail($expiration);
        $media  = $expiration->media()->findOrFail($media);
        $media->delete();
        if (file_exists(storage_path('app/public/images/'.$media->media))){
            File::delete(storage_path('app/public/images/'.$media->media));
        }
        return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
    }
}
