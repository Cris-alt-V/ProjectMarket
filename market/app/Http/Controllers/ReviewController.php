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
        $producto = DB::table('productos')->where('id_producto', $id)->first();
        if ($producto) {
            $vendedorId = $producto->id_vendedor;
            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'review.created',
                'notifiable_type' => 'vendedor',
                'notifiable_id' => $vendedorId,
                'data' => json_encode([
                    'producto_id' => $id,
                    'review_id' => $review->id,
                    'message' => 'Nuevo comentario en tu producto',
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
