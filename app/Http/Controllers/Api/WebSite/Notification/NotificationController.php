<?php

namespace App\Http\Controllers\Api\WebSite\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WebSite\Notification\NotificationResource;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth('api')->user()->notifications()->paginate(20);
        auth('api')->user()->notifications()->update(['read_at' => now()]);
        return NotificationResource::collection($notifications)->additional(['status' => true, 'message' => '']);
    }

    public function show($id)
    {
        $notification = auth('api')->user()->notifications()->findOrFail($id);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }
        return (new NotificationResource($notification))->additional(['status' => true, 'message' => '']);
    }

    public function destroy($id)
    {
        $notification = auth('api')->user()->notifications()->findOrFail($id);
        $notification->delete();
        return response()->json(['status' => true, 'data' => null, 'message' => trans('api.messages.deleted_successfully')]);
    }

    public function deleteAllNotifications()
    {
        $notifications = auth('api')->user()->notifications()->get();

        if (count($notifications) > 0)
        {
            $notifications->delete();
            return response(['status' => true, 'message' => trans('api.messages.deleted_successfully'), 'data' => null], 200);
        }
        return response(['status' => false, 'message' => trans('لا توجد بيانات'), 'data' => null], 422);
    }
}
