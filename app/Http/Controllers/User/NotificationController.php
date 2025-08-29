<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $data = ['title' => 'Notification', 'notifications' => request()->user()->notifications()->get()];
        return view('user.notification.index');
    }

    public function markAsRead(Request $request) // Not implemented yet
    {
        $request->user()->unreadNotifications->where('id', $request->id)->markAsRead();
        return redirect()->back();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return redirect()->back();
    }
}
