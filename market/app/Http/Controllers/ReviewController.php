<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, $id)
    {
        $user = session('user');
        if (!$user) {
            return Redirect::to('/registro');
        }

        // Verificar que el usuario NO sea el vendedor del producto
        $producto = DB::table('productos')->where('id_producto', $id)->first();
        if (!$producto) {
            return Redirect::back()->with('error', 'Producto no encontrado');
        }

        if ($producto->id_vendedor === $user['id_usuario']) {
            return Redirect::back()->with('error', 'No puedes reseñar tu propio producto');
        }

        // Verificar si el usuario ya tiene una reseña en este producto
        $existingReview = DB::table('reviews')
            ->where('producto_id', $id)
            ->where('user_id', $user['id_usuario'])
            ->first();

        if ($existingReview) {
            return Redirect::back()->with('error', 'Ya has reseñado este producto. Solo puedes hacer una reseña por producto.');
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $review = Review::create([
            'producto_id' => $id,
            'user_id' => $user['id_usuario'] ?? null,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        // notify vendedor via a simple notifications table entry
        if ($producto) {
            $vendedorId = $producto->id_vendedor;
            $commentSnippet = $data['comment'] ? Str::limit($data['comment'], 80) : null;
            $message = "Tu producto '{$producto->nombre}' recibió una reseña de {$user['nombre']} con {$data['rating']} estrella";
            if ($commentSnippet) {
                $message .= ": " . $commentSnippet;
            }

            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'review.created',
                'notifiable_type' => 'vendedor',
                'notifiable_id' => $vendedorId,
                'data' => json_encode([
                    'producto_id' => $id,
                    'producto_nombre' => $producto->nombre,
                    'review_id' => $review->id,
                    'rating' => $data['rating'],
                    'comment' => $data['comment'] ?? null,
                    'reviewer_name' => $user['nombre'] ?? 'Cliente',
                    'message' => $message,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // If the request expects JSON (AJAX), return the created review and updated stats
        if ($request->wantsJson() || $request->ajax()) {
            $avg = DB::table('reviews')->where('producto_id', $id)->avg('rating');
            $count = DB::table('reviews')->where('producto_id', $id)->count();
            $latest = DB::table('reviews')
                ->where('producto_id', $id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            return response()->json([
                'review' => $review,
                'average' => round($avg ?: 0, 2),
                'count' => $count,
                'latest' => $latest,
            ], 201);
        }

        return Redirect::back()->with('success', 'Gracias por tu reseña.');
    }
}
