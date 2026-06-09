<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $types = $user['tipo_usuario'] === 'vendedor' ? ['vendedor', 'user'] : ['user'];
        $id = $user['id_usuario'];

        $notifications = DB::table('notifications')
            ->whereIn('notifiable_type', $types)
            ->where('notifiable_id', $id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $unread = DB::table('notifications')
            ->whereIn('notifiable_type', $types)
            ->where('notifiable_id', $id)
            ->whereNull('read_at')
            ->count();

        $list = $notifications->map(function ($notification) {
            $data = json_decode($notification->data, true);
            if ($data === null && !empty($notification->data)) {
                $data = ['message' => trim($notification->data)];
            }
            $data = $data ?: [];
            $formatted = $this->formatNotification($notification, $data);

            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $data,
                'text' => $formatted['body'],
                'title' => $formatted['title'],
                'body' => $formatted['body'],
                'meta' => $formatted['meta'],
                'icon' => $formatted['icon'],
                'url' => $formatted['url'],
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
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

        $types = $user['tipo_usuario'] === 'vendedor' ? ['vendedor', 'user'] : ['user'];

        $updated = DB::table('notifications')
            ->where('id', $id)
            ->whereIn('notifiable_type', $types)
            ->where('notifiable_id', $user['id_usuario'])
            ->update(['read_at' => now(), 'updated_at' => now()]);

        if ($updated) {
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => false], 404);
    }

    private function formatNotification($notification, array $data): array
    {
        if ($notification->type === 'message.received') {
            $productName = $data['product_name'] ?? $data['producto_nombre'] ?? null;

            return [
                'title' => 'Nuevo mensaje',
                'body' => $data['message'] ?? 'Has recibido un nuevo mensaje.',
                'meta' => $productName ? 'Producto: ' . $productName : 'Mensajeria',
                'icon' => 'MSG',
                'url' => '/mensajes',
            ];
        }

        if ($notification->type === 'review.created') {
            $rating = isset($data['rating']) ? (int) $data['rating'] : 0;
            $productName = $data['producto_nombre'] ?? 'tu producto';
            $reviewer = $data['reviewer_name'] ?? 'Un cliente';
            $comment = trim((string) ($data['comment'] ?? ''));

            return [
                'title' => 'Nueva resena',
                'body' => $comment
                    ? "{$reviewer} dejo {$rating}/5 en {$productName}: {$comment}"
                    : "{$reviewer} dejo {$rating}/5 en {$productName}.",
                'meta' => $rating ? 'Calificacion ' . $rating . '/5' : 'Resena de producto',
                'icon' => 'REV',
                'url' => !empty($data['producto_id']) ? '/producto/' . $data['producto_id'] : '/micuenta',
            ];
        }

        $body = $data['message'] ?? null;
        if (!$body && !empty($data['producto_nombre'])) {
            $body = "Hay una actualizacion en tu producto {$data['producto_nombre']}";
        }

        return [
            'title' => 'Notificacion',
            'body' => $body ?: ($notification->type ?? 'Notificacion'),
            'meta' => 'Marketplace Local',
            'icon' => 'NOT',
            'url' => null,
        ];
    }
}
