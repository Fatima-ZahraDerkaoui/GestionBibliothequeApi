<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        return response()->json(
            Notification::where('user_id', Auth::id())->get()
        );
    }

    public function markAsRead($id)
    {
        $notif = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notif->is_read = true;
        $notif->save();

        return response()->json(['message' => 'Notification marquée comme lue']);
    }

     public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        return response()->json(['message' => 'Notification deleted successfully']);
    }
}
