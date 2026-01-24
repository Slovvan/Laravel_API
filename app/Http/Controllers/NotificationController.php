<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(15);
        
        return view('notifications.index', compact('notifications'));
    }

    public function unreadCount()
    {
        $count = auth()->user()->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        // Redirigir a la URL de la notificación
        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }
        
        return redirect()->back();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
    
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification supprimée.');
    }
}