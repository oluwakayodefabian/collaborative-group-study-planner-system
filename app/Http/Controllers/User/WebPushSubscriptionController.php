<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WebPushSubscription;
use Illuminate\Http\Request;

class WebPushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        WebPushSubscription::create([
            'user_id' => $request->user()->id,
            'data' => $request->subscription,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Push Notification Subscription Saved']);
    }

    public function destroy(Request $request, $endpoint)
    {
        $request->user()->deletePushSubscription($endpoint);

        return response()->json(['status' => 'success', 'message' => 'Push Notification Subscription Deleted']);
    }
}
