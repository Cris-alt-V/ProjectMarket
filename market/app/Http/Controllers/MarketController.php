<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketController extends Controller
{
    public function welcome()
    {
        $comercios = DB::table('vendedores')->limit(4)->get();

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
                'v.ubicacion'
            )
            ->get();

        $ratings = collect();
        try {
            $ratings = DB::table('reviews')
                ->select('producto_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as reviews_count'))
                ->groupBy('producto_id')
                ->get()
                ->keyBy('producto_id');
        } catch (\Exception $e) {
            try {
                $ratings = DB::table('reseñas')
                    ->select('id_producto', DB::raw('AVG(puntuacion) as avg_rating'), DB::raw('COUNT(*) as reviews_count'))
                    ->groupBy('id_producto')
                    ->get()
                    ->keyBy('id_producto');
            } catch (\Exception $inner) {
                $ratings = collect();
            }
        }

        $productos = $productos->map(function ($product) use ($ratings) {
            $meta = $ratings->get($product->id_producto);
            $product->avg_rating = $meta ? round($meta->avg_rating, 2) : 0;
            $product->reviews_count = $meta ? (int)$meta->reviews_count : 0;
            return $product;
        });

        $storeRatings = DB::table('reviews')
            ->join('productos', 'reviews.producto_id', '=', 'productos.id_producto')
            ->select('productos.id_vendedor', DB::raw('AVG(reviews.rating) as avg_rating'), DB::raw('COUNT(*) as reviews_count'))
            ->groupBy('productos.id_vendedor')
            ->get()
            ->keyBy('id_vendedor');

        $comercios = $comercios->map(function ($store) use ($storeRatings) {
            $meta = $storeRatings->get($store->id_vendedor);
            $store->avg_rating = $meta ? round($meta->avg_rating, 2) : 0;
            $store->reviews_count = $meta ? (int)$meta->reviews_count : 0;
            return $store;
        });

        $popularProducts = $productos->sortByDesc('avg_rating')->take(3)->values();
        $recentProducts = $productos->sortByDesc('id_producto')->take(3)->values();

        return view('welcome', [
            'comercios' => $comercios,
            'popularProducts' => $popularProducts,
            'recentProducts' => $recentProducts,
        ]);
    }

    public function productos(Request $request)
    {
        $query = DB::table('productos as p')
            ->join('vendedores as v', 'p.id_vendedor', '=', 'v.id_vendedor')
            ->select('p.*', 'v.nombre_negocio', 'v.ubicacion');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('p.nombre', 'like', "%$search%")
                    ->orWhere('p.descripcion', 'like', "%$search%")
                    ->orWhere('p.categoria', 'like', "%$search%");
            });
        }

        $productos = $query->get();
        $comercios = DB::table('vendedores')->get();

        return view('productos', [
            'productos' => $productos,
            'comercios' => $comercios,
        ]);
    }

    public function detalleProducto(Request $request)
    {
        $id = $request->query('id');
        $producto = DB::table('productos')->where('id_producto', $id)->first();

        if (!$producto) {
            abort(404);
        }

        $comercio = DB::table('vendedores')->where('id_vendedor', $producto->id_vendedor)->first();

        $reviews = DB::table('reviews')->where('producto_id', $id)->orderBy('created_at', 'desc')->get();

        return view('detalle-producto', [
            'producto' => $producto,
            'comercio' => $comercio,
            'reviews' => $reviews,
        ]);
    }

    public function tienda(Request $request)
    {
        $id = $request->query('id', $request->query('comercio_id'));
        $tienda = DB::table('vendedores')->where('id_vendedor', $id)->first();
        if (!$tienda) {
            abort(404);
        }

        $productos = DB::table('productos')->where('id_vendedor', $tienda->id_vendedor)->get();

        $productRatings = DB::table('reviews')
            ->select('producto_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as reviews_count'))
            ->groupBy('producto_id')
            ->get()
            ->keyBy('producto_id');

        $productos = $productos->map(function ($product) use ($productRatings) {
            $meta = $productRatings->get($product->id_producto);
            $product->avg_rating = $meta ? round($meta->avg_rating, 2) : 0;
            $product->reviews_count = $meta ? (int)$meta->reviews_count : 0;
            return $product;
        });

        $storeMeta = DB::table('reviews')
            ->join('productos', 'reviews.producto_id', '=', 'productos.id_producto')
            ->where('productos.id_vendedor', $tienda->id_vendedor)
            ->select(DB::raw('AVG(reviews.rating) as avg_rating'), DB::raw('COUNT(*) as reviews_count'))
            ->first();

        $tienda->avg_rating = $storeMeta && $storeMeta->avg_rating ? round($storeMeta->avg_rating, 2) : 0;
        $tienda->reviews_count = $storeMeta ? (int)$storeMeta->reviews_count : 0;

        return view('tienda', [
            'comercio' => $tienda,
            'productos' => $productos,
        ]);
    }

    public function carrito()
    {
        return view('carrito');
    }

    public function registro()
    {
        return view('registro');
    }

    public function searchSuggestions(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $searchQuery = "%$query%";
        
        // Obtener sugerencias de productos con imágenes
        $productos = DB::table('productos')
            ->select('nombre', 'imagen_url', 'id_producto')
            ->where('nombre', 'like', $searchQuery)
            ->distinct()
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'nombre' => $item->nombre,
                    'imagen_url' => $item->imagen_url,
                    'id_producto' => $item->id_producto
                ];
            })
            ->values();

        // Obtener sugerencias de categorías
        $categorias = DB::table('productos')
            ->select('categoria')
            ->where('categoria', 'like', $searchQuery)
            ->distinct()
            ->limit(3)
            ->pluck('categoria');

        // Obtener sugerencias de comercios
        $comercios = DB::table('vendedores')
            ->select('nombre_negocio')
            ->where('nombre_negocio', 'like', $searchQuery)
            ->distinct()
            ->limit(3)
            ->pluck('nombre_negocio');

        // Combinar y retornar sugerencias
        $sugerencias = [
            'productos' => $productos->toArray(),
            'categorias' => $categorias->toArray(),
            'comercios' => $comercios->toArray(),
        ];

        return response()->json($sugerencias);
    }

    public function micuenta()
    {
        $user = session('user');
        if (!$user) {
            return redirect('/registro');
        }

        $types = $user['tipo_usuario'] === 'vendedor' ? ['vendedor', 'user'] : ['user'];
        $notifications = DB::table('notifications')
            ->whereIn('notifiable_type', $types)
            ->where('notifiable_id', $user['id_usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $unreadCount = DB::table('notifications')
            ->whereIn('notifiable_type', $types)
            ->where('notifiable_id', $user['id_usuario'])
            ->whereNull('read_at')
            ->count();

        return view('micuenta', [
            'user' => $user,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function misComercios()
    {
        $user = session('user');
        if (!$user || $user['tipo_usuario'] !== 'vendedor') {
            return redirect('/registro');
        }

        $productos = DB::table('productos')->where('id_vendedor', $user['id_usuario'])->get();

        return view('mis-comercios', [
            'user' => $user,
            'productos' => $productos,
        ]);
    }

    public function comercios()
    {
        $comercios = DB::table('vendedores')->get();

        $storeRatings = DB::table('reviews')
            ->join('productos', 'reviews.producto_id', '=', 'productos.id_producto')
            ->select('productos.id_vendedor', DB::raw('AVG(reviews.rating) as avg_rating'), DB::raw('COUNT(*) as reviews_count'))
            ->groupBy('productos.id_vendedor')
            ->get()
            ->keyBy('id_vendedor');

        $comercios = $comercios->map(function ($store) use ($storeRatings) {
            $meta = $storeRatings->get($store->id_vendedor);
            $store->avg_rating = $meta ? round($meta->avg_rating, 2) : 0;
            $store->reviews_count = $meta ? (int)$meta->reviews_count : 0;
            return $store;
        });

        return view('comercios', [
            'comercios' => $comercios,
        ]);
    }
}
