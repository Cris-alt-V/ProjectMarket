<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $comercios = DB::table('vendedores')->get();

        $productos = DB::table('productos as p')
            ->join('vendedores as v', 'p.id_vendedor', '=', 'v.id_vendedor')
            ->select(
                'p.id_producto',
                'p.nombre',
                'p.descripcion',
                'p.categoria',
                'p.precio',
                'p.stock',
                'p.imagen_url',
                'p.id_vendedor',
                'v.nombre_negocio',
                'v.ubicacion as tienda_ubicacion',
                'v.descripcion as tienda_descripcion'
            )
            ->when($search, function ($query, $search) {
                $searchTerm = '%' . strtolower($search) . '%';
                return $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->whereRaw('LOWER(p.nombre) LIKE ?', [$searchTerm])
                        ->orWhereRaw('LOWER(p.descripcion) LIKE ?', [$searchTerm])
                        ->orWhereRaw('LOWER(p.categoria) LIKE ?', [$searchTerm])
                        ->orWhereRaw('LOWER(v.nombre_negocio) LIKE ?', [$searchTerm]);
                });
            })
            ->get();

        // Attach average rating and reviews count for each product
        $ratings = DB::table('reviews')
            ->select('producto_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as reviews_count'))
            ->groupBy('producto_id')
            ->get()
            ->keyBy('producto_id');

        $productos = $productos->map(function ($p) use ($ratings) {
            $meta = $ratings->get($p->id_producto);
            $p->avg_rating = $meta ? round($meta->avg_rating, 2) : 0;
            $p->reviews_count = $meta ? (int)$meta->reviews_count : 0;
            return $p;
        });

        return view('productos', compact('productos', 'comercios'));
    }

    public function show($id)
    {
        $producto = DB::table('productos as p')
            ->join('vendedores as v', 'p.id_vendedor', '=', 'v.id_vendedor')
            ->join('usuarios as u', 'v.id_vendedor', '=', 'u.id_usuario')
            ->select(
                'p.id_producto',
                'p.nombre',
                'p.descripcion',
                'p.categoria',
                'p.precio',
                'p.stock',
                'p.imagen_url',
                'p.id_vendedor',
                'v.nombre_negocio',
                'v.ubicacion as tienda_ubicacion',
                'v.descripcion as tienda_descripcion',
                'u.nombre as vendedor_nombre',
                'u.correo as vendedor_correo'
            )
            ->where('p.id_producto', $id)
            ->first();

        if (!$producto) {
            return redirect('/productos')->with('error', 'Producto no encontrado');
        }

        $comercio = (object) [
            'id_vendedor' => $producto->id_vendedor,
            'nombre_negocio' => $producto->nombre_negocio,
            'ubicacion' => $producto->tienda_ubicacion,
            'descripcion' => $producto->tienda_descripcion,
            'vendedor_nombre' => $producto->vendedor_nombre,
            'vendedor_correo' => $producto->vendedor_correo,
        ];

        // Load reviews and rating summary
        $reviews = DB::table('reviews')->where('producto_id', $id)->orderByDesc('created_at')->get();
        $avgRating = DB::table('reviews')->where('producto_id', $id)->avg('rating') ?: 0;
        $reviewsCount = DB::table('reviews')->where('producto_id', $id)->count();

        // Verificar si el usuario actual ya ha reseñado este producto
        $userAlreadyReviewed = false;
        $user = session('user');
        if ($user) {
            $userAlreadyReviewed = DB::table('reviews')
                ->where('producto_id', $id)
                ->where('user_id', $user['id_usuario'])
                ->exists();
        }

        return view('detalle-producto', compact('producto', 'comercio', 'reviews', 'avgRating', 'reviewsCount', 'userAlreadyReviewed'));
    }
}
