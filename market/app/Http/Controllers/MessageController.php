<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Message;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return Redirect::to('/registro');
        }

        $data = $request->validate([
            'receiver_id' => 'required|integer',
            'subject' => 'nullable|string|max:200',
            'body' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id' => $user['id_usuario'] ?? null,
            'receiver_id' => $data['receiver_id'],
            'subject' => $data['subject'] ?? null,
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
                'sender_id' => $user['id_usuario'] ?? null,
                'message' => 'Has recibido un nuevo mensaje',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Redirect::back()->with('success', 'Mensaje enviado.');
    }
}
