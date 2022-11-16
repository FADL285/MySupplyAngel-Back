<?php

namespace App\Http\Controllers\Api\WebSite\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebSite\Profile\AddPreviousWorkRequest;
use App\Http\Requests\Api\WebSite\Profile\EditEmailRequest;
use App\Http\Requests\Api\WebSite\Profile\EditPasswordRequest;
use App\Http\Requests\Api\WebSite\Profile\EditPhoneRequest;
use App\Http\Requests\Api\WebSite\Profile\EditProfileRequest;
use App\Http\Resources\Api\WebSite\User\UserResource;
use App\Models\AppMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return (new UserResource(auth('api')->user()))->additional(['status' => true, 'message' => '']);
    }

    public function editProfile(EditProfileRequest $request)
    {
        $user = auth('api')->user();
        $user->update(Arr::except($request->validated(), ['country_id', 'city_id', 'company_name', 'company_address', 'commercial_register_num', 'tax_card_num', 'categories']));
        $user->profile()->create(Arr::only($request->validated(), ['country_id', 'city_id']));
        $user->company()->create(Arr::only($request->validated(), ['company_name', 'company_address', 'commercial_register_num', 'tax_card_num']));
        $user->categories()->sync($request->validated('categories'));
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.update.successfully')]);
    }

    public function editPhone(EditPhoneRequest $request)
    {
        $user = auth('api')->user();
        $user->update($request->validated());
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.update.successfully')]);
    }

    public function editEmail(EditEmailRequest $request)
    {
        $user = auth('api')->user();
        $user->update($request->validated());
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.update.successfully')]);
    }

    public function editPassword(EditPasswordRequest $request)
    {
        $user = auth('api')->user();
        $user->update($request->validated());
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.update.successfully')]);
    }

    public function addPreviousWork(AddPreviousWorkRequest $request)
    {
        $user = auth('api')->user();
        if (request()->hasFile('file')) {
            $file = AppMedia::where(['app_mediaable_type' => 'App\Models\User', 'app_mediaable_id' => $user->id , 'media_type' => 'file', 'optional' => 'previous_work'])->first();
            if ($file) {
                if (file_exists(storage_path('app/public/images/'.$file->media))){
                    File::delete(storage_path('app/public/images/'.$file->media));
                }
                $file->delete();
            }
            $file = request()->file('file')->store('/users', 'uploads');
            $user->media()->create(['media' => $file, 'media_type' => 'file', 'optional' => 'previous_work']);
        }
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.update.successfully')]);
    }

    public function deletePreviousWork()
    {
        $user = auth('api')->user();
        $file = AppMedia::where(['app_mediaable_type' => 'App\Models\User', 'app_mediaable_id' => $user->id , 'media_type' => 'file', 'optional' => 'previous_work'])->first();
        if ($file) {
            if (file_exists(storage_path('app/public/images/'.$file->media))){
                File::delete(storage_path('app/public/images/'.$file->media));
            }
            $file->delete();
            return response()->json(['status' => true, 'data' => null, 'message' => trans('website.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'message' => trans('website.error.something_went_wrong')]);
    }

    public function deleteMyAccount()
    {
        $user = auth('api')->user();
        $user->delete();
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.deleted.successfully')]);
    }
}
