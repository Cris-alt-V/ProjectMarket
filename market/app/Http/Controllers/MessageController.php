<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        $user = session('user');
        if (!$user) {
            return redirect('/registro')->with('error', 'Debes iniciar sesion para ver tus mensajes.');
        }

        return view('mensajes', [
            'chatServerUrl' => env('CHAT_SERVER_URL', 'http://localhost:3001'),
        ]);
    }

    private function productForChat(int $productId)
    {
        return DB::table('productos as p')
            ->join('vendedores as v', 'p.id_vendedor', '=', 'v.id_vendedor')
            ->select('p.id_producto', 'p.nombre', 'p.id_vendedor', 'v.nombre_negocio')
            ->where('p.id_producto', $productId)
            ->first();
    }

    private function conversationId(int $senderId, int $receiverId, ?int $productId): int
    {
        $existing = DB::table('messages')
            ->where(function ($query) use ($senderId, $receiverId) {
                $query->where(function ($subQuery) use ($senderId, $receiverId) {
                    $subQuery->where('sender_id', $senderId)->where('receiver_id', $receiverId);
                })->orWhere(function ($subQuery) use ($senderId, $receiverId) {
                    $subQuery->where('sender_id', $receiverId)->where('receiver_id', $senderId);
                });
            })
            ->when($productId, fn ($query) => $query->where('product_id', $productId), fn ($query) => $query->whereNull('product_id'))
            ->orderBy('conversation_id')
            ->value('conversation_id');

        if ($existing) {
            return (int) $existing;
        }

        return (int) DB::table('messages')->max('conversation_id') + 1;
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

        $senderId = (int) $user['id_usuario'];
        $receiverId = (int) $data['receiver_id'];
        $productId = isset($data['product_id']) ? (int) $data['product_id'] : null;
        $conversationId = $this->conversationId($senderId, $receiverId, $productId);

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'product_id' => $productId,
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
                'product_name' => $product?->nombre,
                'sender_id' => $senderId,
                'message' => $product ? 'Nuevo mensaje sobre ' . $product->nombre : 'Has recibido un nuevo mensaje',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Mensaje enviado.',
                'chat' => $this->formatMessage($message, $senderId),
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
            'conversation_id' => $messages->first()?->conversation_id,
            'messages' => $messages->map(fn ($message) => $this->formatMessage($message, $userId))->values(),
        ]);
    }

    public function conversation($conversationId)
    {
        $user = session('user');
        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $userId = (int) $user['id_usuario'];
        $messages = Message::where('conversation_id', $conversationId)
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->orderBy('created_at')
            ->get();

        Message::whereIn('id', $messages->pluck('id'))
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'conversation_id' => (int) $conversationId,
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
            $key = (string) $message->conversation_id;

            if (isset($conversations[$key])) {
                continue;
            }

            $conversations[$key] = [
                'conversation_id' => (int) $message->conversation_id,
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
            'conversation_id' => $message->conversation_id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'product_id' => $message->product_id,
            'body' => $message->body,
            'is_mine' => (int) $message->sender_id === $currentUserId,
            'created_at' => optional($message->created_at)->toDateTimeString(),
        ];
    }
}
