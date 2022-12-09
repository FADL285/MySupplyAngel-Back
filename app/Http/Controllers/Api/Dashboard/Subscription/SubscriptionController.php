<?php

namespace App\Http\Controllers\Api\Dashboard\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Subscription\changeSubscriptionStatusRequest;
use App\Http\Resources\Api\Dashboard\Subscription\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = Subscription::when($request->status, function ($query) use($request) {
            $query->where('status', $request->status);
        })->when($request->user_id, function ($query) use($request) {
            $query->where('user_id', $request->user_id);
        })->get();

        return SubscriptionResource::collection($subscriptions)->additional(['status' => true, 'message' => '']);
    }

    public function changeStatus(changeSubscriptionStatusRequest $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        $data = [
            'status'   => 'unpaid',
            'start_at' => null,
            'end_at'   => null,
        ];

        if (in_array($request->status, ['free', 'paid']))
        {
            $data = [
                'status'   => $request->status,
                'start_at' => now(),
                'end_at'   => now()->addMonths($subscription->package->duration_by_month),
            ];
        }
        $subscription->update($data);
        return response()->json(['status' => true, 'data' => null, 'message' => 'تم التعديل بنجاح.']);
    }
}
