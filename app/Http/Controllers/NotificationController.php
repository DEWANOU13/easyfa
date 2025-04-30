<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    //
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(10);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request)
{
    $notificationId = $request->input('notification_id');
    $notification = Auth::user()->notifications()->find($notificationId);

    if ($notification) {
        $notification->markAsRead();
    }

    return response()->json(['message' => 'Notification marked as read']);
}
}
