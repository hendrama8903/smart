<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function data(): JsonResponse
    {
        $user  = Auth::user();
        $notifs = $user->unreadNotifications()->latest()->take(20)->get();

        $items = $notifs->map(fn ($n) => array_merge($n->data, [
            'id'     => $n->id,
            'dibuat' => $n->created_at->diffForHumans(),
        ]))->values();

        return response()->json([
            'total' => $notifs->count(),
            'items' => $items,
        ]);
    }

    public function baca(string $id): JsonResponse
    {
        Auth::user()->notifications()->findOrFail($id)->markAsRead();
        return response()->json(['ok' => true]);
    }

    public function bacaSemua(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }
}
