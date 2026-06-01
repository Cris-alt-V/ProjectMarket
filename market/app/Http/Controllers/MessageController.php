<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Message;

class MessageController extends Controller
{
    private function productForChat(int $productId)
    {
        return DB::table('productos as p')
            ->join('vendedores as v', 'p.id_vendedor', '=', 'v.id_vendedor')
            ->select('p.id_producto', 'p.nombre', 'p.id_vendedor', 'v.nombre_negocio')
            ->where('p.id_producto', $productId)
            ->first();
    }

    private function canReadMessage($message, int $userId): bool
    {
        return (int) $message->sender_id === $userId || (int) $message->receiver_id === $userId;
    }

    public function send(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return Redirect::to('/registro');
        }

        $data = $request->validate([
            'receiver_id' => 'required|integer',
            'product_id' => 'nullable|integer',
            'subject' => 'nullable|string|max:200',
            'body' => 'required|string|max:5000',
        ]);

        $product = null;
        if (!empty($data['product_id'])) {
            $product = $this->productForChat((int) $data['product_id']);
            if (!$product) {
                return response()->json(['message' => 'Producto no encontrado'], 404);
            }

            $senderId = (int) $user['id_usuario'];
            $receiverId = (int) $data['receiver_id'];
            $sellerId = (int) $product->id_vendedor;
            $isBuyerMessage = $senderId !== $sellerId && $receiverId === $sellerId;
            $isSellerReply = $senderId === $sellerId && $receiverId !== $sellerId;

            if (!$isBuyerMessage && !$isSellerReply) {
                return response()->json(['message' => 'No autorizado para esta conversacion'], 403);
            }
        }

        $message = Message::create([
            'sender_id' => $user['id_usuario'] ?? null,
            'receiver_id' => $data['receiver_id'],
            'product_id' => $data['product_id'] ?? null,
            'subject' => $data['subject'] ?? ($product ? 'Producto: ' . $product->nombre : null),
            'body' => $data['body'],
        ]);

        // create notification entry for receiver
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'message.received',
            'notifiable_type' => 'user',
            'notifiable_id' => $data['receiver_id'],
            'data' => json_encode([
                'message_id' => $message->id,
                'product_id' => $data['product_id'] ?? null,
                'sender_id' => $user['id_usuario'] ?? null,
                'message' => $product ? 'Nuevo mensaje sobre ' . $product->nombre : 'Has recibido un nuevo mensaje',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Mensaje enviado.',
                'chat' => $this->formatMessage($message, (int) $user['id_usuario']),
            ]);
        }

        return Redirect::back()->with('success', 'Mensaje enviado.');
    }

    public function productThread(Request $request, $id)
    {
        $user = session('user');
        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $product = $this->productForChat((int) $id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $userId = (int) $user['id_usuario'];
        $partnerId = (int) $request->query('partner_id', $product->id_vendedor);

        if ($userId !== (int) $product->id_vendedor) {
            $partnerId = (int) $product->id_vendedor;
        }

        $messages = Message::where('product_id', $product->id_producto)
            ->where(function ($query) use ($userId, $partnerId) {
                $query->where(function ($subQuery) use ($userId, $partnerId) {
                    $subQuery->where('sender_id', $userId)->where('receiver_id', $partnerId);
                })->orWhere(function ($subQuery) use ($userId, $partnerId) {
                    $subQuery->where('sender_id', $partnerId)->where('receiver_id', $userId);
                });
            })
            ->orderBy('created_at')
            ->get();

        Message::whereIn('id', $messages->pluck('id'))
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'product' => $product,
            'partner_id' => $partnerId,
            'messages' => $messages->map(fn ($message) => $this->formatMessage($message, $userId))->values(),
        ]);
    }

    public function conversations()
    {
        $user = session('user');
        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $userId = (int) $user['id_usuario'];
        $messages = DB::table('messages as m')
            ->leftJoin('productos as p', 'm.product_id', '=', 'p.id_producto')
            ->leftJoin('usuarios as sender', 'm.sender_id', '=', 'sender.id_usuario')
            ->leftJoin('usuarios as receiver', 'm.receiver_id', '=', 'receiver.id_usuario')
            ->select(
                'm.*',
                'p.nombre as product_name',
                'p.id_vendedor as product_seller_id',
                'sender.nombre as sender_name',
                'receiver.nombre as receiver_name'
            )
            ->where('m.sender_id', $userId)
            ->orWhere('m.receiver_id', $userId)
            ->orderByDesc('m.created_at')
            ->get();

        $conversations = [];
        foreach ($messages as $message) {
            $partnerId = (int) $message->sender_id === $userId ? (int) $message->receiver_id : (int) $message->sender_id;
            $key = ($message->product_id ?: 'general') . ':' . $partnerId;

            if (isset($conversations[$key])) {
                continue;
            }

            $conversations[$key] = [
                'product_id' => $message->product_id,
                'product_name' => $message->product_name ?: 'Conversacion',
                'partner_id' => $partnerId,
                'partner_name' => (int) $message->sender_id === $userId ? $message->receiver_name : $message->sender_name,
                'last_message' => $message->body,
                'last_at' => $message->created_at,
                'unread' => $messages
                    ->where('product_id', $message->product_id)
                    ->where('sender_id', $partnerId)
                    ->where('receiver_id', $userId)
                    ->whereNull('read_at')
                    ->count(),
            ];
        }

        return response()->json(['conversations' => array_values($conversations)]);
    }

    private function formatMessage($message, int $currentUserId): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'product_id' => $message->product_id,
            'body' => $message->body,
            'is_mine' => (int) $message->sender_id === $currentUserId,
            'created_at' => optional($message->created_at)->toDateTimeString(),
        ];
    }
}
