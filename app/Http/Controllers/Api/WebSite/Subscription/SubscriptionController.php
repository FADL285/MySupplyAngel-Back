<?php

namespace App\Http\Controllers\Api\WebSite\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WebSite\User\UserResource;
use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function subscrip(Request $request)
    {
        $user = auth('api')->user();
        $package = Package::findOrFail($request->package_id);

        if ($package->type == 'free')
        {
            Subscription::create([
                'user_id'    => $user->id,
                'package_id' => $package->id,
                'start_at'   => now(),
                'end_at'     => now()->addMonths($package->duration_by_month),
                'status'     => 'free',
            ]);
        }
        else
        {
            Subscription::create([
                'user_id'    => $user->id,
                'package_id' => $package->id
            ]);
        }

        return (new UserResource(auth('api')->user()))->additional(['status' => true, 'message' => 'تم الاشتراك بنجاح.']);
    }
}
