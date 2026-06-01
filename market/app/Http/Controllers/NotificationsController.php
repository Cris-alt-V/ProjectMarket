<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $type = $user['tipo_usuario'] === 'vendedor' ? 'vendedor' : 'user';
        $id = $user['id_usuario'];

        $notifications = DB::table('notifications')
            ->where('notifiable_type', $type)
            ->where('notifiable_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $unread = DB::table('notifications')
            ->where('notifiable_type', $type)
            ->where('notifiable_id', $id)
            ->whereNull('read_at')
            ->count();

        $list = $notifications->map(function ($n) {
            $data = json_decode($n->data, true);
            if ($data === null && !empty($n->data)) {
                $data = ['message' => trim($n->data)];
            }
            $data = $data ?: [];
            $text = $data['message'] ?? null;
            if (!$text && !empty($data['producto_nombre'])) {
                $text = "Hay una actualización en tu producto {$data['producto_nombre']}";
            }
            if (!$text) {
                $text = $n->type ?? 'Notificación';
            }

            return [
                'id' => $n->id,
                'type' => $n->type,
                'data' => $data,
                'text' => $text,
                'created_at' => $n->created_at,
                'read_at' => $n->read_at,
            ];
        });

        return response()->json(['unread' => $unread, 'notifications' => $list]);
    }

    public function markRead(Request $request, $id)
    {
        $user = session('user');
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $type = $user['tipo_usuario'] === 'vendedor' ? 'vendedor' : 'user';
        $uid = $user['id_usuario'];

        $updated = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', $type)
            ->where('notifiable_id', $uid)
            ->update(['read_at' => now(), 'updated_at' => now()]);

        if ($updated) {
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => false], 404);
    }
}
