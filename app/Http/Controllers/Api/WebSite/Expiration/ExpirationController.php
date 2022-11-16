<?php

namespace App\Http\Controllers\Api\WebSite\Expiration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebSite\Expiration\ExpirationRequest;
use App\Http\Resources\Api\WebSite\Expiration\ExpirationResource;
use App\Models\Expiration;
use App\Models\FavoriteExpiration;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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
        $expirations = Expiration::where('user_id', auth('api')->id())
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
        $expiration = Expiration::where('user_id', auth('api')->id())->findOrFail($id);
        if ($expiration->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }

    public function deleteExpirationMedia($expiration, $media)
    {
        $expiration = Expiration::where('user_id', auth('api')->id())->findOrFail($expiration);
        $media  = $expiration->media()->findOrFail($media);
        $media->delete();
        if (file_exists(storage_path('app/public/images/'.$media->media))){
            File::delete(storage_path('app/public/images/'.$media->media));
        }
        return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
    }

    public function toggelToFavorite($id)
    {
        $user_id = auth('api')->id();
        $expiration = Expiration::findOrFail($id);
        $favorite_expiration = FavoriteExpiration::where(['user_id' => $user_id, 'expiration_id' => $expiration->id])->first();
        $favorite_expiration ? $favorite_expiration->delete() : FavoriteExpiration::create(['user_id' => $user_id, 'expiration_id' => $expiration->id]);

        return response()->json(['status' => true, 'data' => ['is_favorite' => $favorite_expiration ? false : true], 'messages' => trans('dashboard.create.successfully')]);
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
        })->latest()->get();

        return ExpirationResource::collection($favorite_expirations)->additional(['status' => true, 'message' => '']);
    }
}
