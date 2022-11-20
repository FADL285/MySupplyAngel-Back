<?php

namespace App\Http\Controllers\Api\WebSite\Expiration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebSite\Expiration\ExpirationRequest;
use App\Http\Resources\Api\WebSite\Expiration\ExpirationResource;
use App\Models\Expiration;
use App\Models\FavoriteExpiration;
use App\Models\User;
use App\Notifications\Website\Expiration\ExpirationNotification;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function myExpirations(Request $request)
    {
        $expirations = Expiration::when($request->filter == 'my_expirations', function ($query) {
            $query->where('user_id', auth('api')->id());
        })->when($request->filter == 'my_offers', function ($query) {
            $query->whereHas('offers', function ($query) {
                $query->where('user_id', auth('api')->id());
            });
        })->when($request->filter == 'all', function ($query) {
            $query->where('user_id', auth('api')->id())->orWhereHas('offers', function ($query) {
                $query->where('user_id', auth('api')->id());
            });
        })->when(! in_array($request->filter, ['my_expirations', 'my_offers', 'all']), function ($query) {
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
            $expiration = Expiration::create($request->safe()->except('category_ids') + ['user_id' => auth('api')->id(), 'status' => 'pending']);
            $expiration->categories()->attach($request->validated('category_ids'));
            $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();
            Notification::send($admins, new ExpirationNotification($expiration->id, 'new_expiration', ['database', 'broadcast']));
            DB::commit();
            return response()->json(['status' => true, 'data' => null, 'message' => trans('website.create.successfully')]);
        } catch (Exception $e) {
            DB::rollBack();
            info($e->getMessage());
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
        $expiration = Expiration::where('user_id', auth('api')->id())->findOrFail($id);
        if ($expiration->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('website.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('website.delete.fail')], 422);
    }

    public function deleteExpirationMedia($expiration, $media)
    {
        $expiration = Expiration::where('user_id', auth('api')->id())->findOrFail($expiration);
        $media  = $expiration->media()->findOrFail($media);
        $media->delete();
        if (file_exists(storage_path('app/public/images/'.$media->media))){
            File::delete(storage_path('app/public/images/'.$media->media));
        }
        return response()->json(['status' => true, 'data' => null, 'messages' => trans('website.delete.successfully')]);
    }

    public function toggelToFavorite($id)
    {
        $user_id = auth('api')->id();
        $expiration = Expiration::findOrFail($id);
        $favorite_expiration = FavoriteExpiration::where(['user_id' => $user_id, 'expiration_id' => $expiration->id])->first();
        $favorite_expiration ? $favorite_expiration->delete() : FavoriteExpiration::create(['user_id' => $user_id, 'expiration_id' => $expiration->id]);

        return response()->json(['status' => true, 'data' => ['is_favorite' => $favorite_expiration ? false : true], 'messages' => trans('website.create.successfully')]);
    }

    public function favorite(Request $request)
    {
        $favorite_expirations = auth('api')->user()->expirationsFavorite()
        ->when($request->keyword, function($query) use($request){
            $query->where('name', 'LIKE', '%'.$request->keyword.'%')
            ->orWhere('desc', 'LIKE', '%'.$request->keyword.'%');
        })->when($request->category_id, function ($query) use ($request) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('id', $request->category_id);
            });
        })->latest()->paginate();

        return ExpirationResource::collection($favorite_expirations)->additional(['status' => true, 'message' => '']);
    }
}
