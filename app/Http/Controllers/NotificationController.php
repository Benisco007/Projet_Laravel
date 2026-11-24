<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Liste des notifications de l'utilisateur connecté (lues et non lues).
     */
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(15);

        return view('validation.notifications', [
            'notifications' => $notifications,
            'nombreNonLues' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marque une notification comme lue puis redirige vers la liste.
     */
    public function marquerLue(Request $request, string $notificationId): RedirectResponse
    {
        $request->user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail()
            ->markAsRead();

        return redirect()->route('validation.notifications.index');
    }

    public function toutMarquerLu(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
