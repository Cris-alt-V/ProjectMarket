<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = DB::table('productos as p')
            ->join('vendedores as v', 'p.id_vendedor', '=', 'v.id_vendedor')
            ->select(
                'p.id_producto',
                'p.nombre',
                'p.descripcion',
                'p.precio',
                'p.stock',
                'p.imagen_url',
                'p.id_vendedor',
                'v.nombre_negocio',
                'v.ubicacion as tienda_ubicacion',
                'v.descripcion as tienda_descripcion'
            )
            ->get()
            ->map(function ($product) {
                $foto = $this->normalizeImageUrl($product->imagen_url) ?: '/imagenes/blusa.png';

                return [
                    'id' => $product->id_producto,
                    'nombre' => $product->nombre,
                    'descripcion' => $product->descripcion,
                    'precio' => (float) $product->precio,
                    'stock' => $product->stock,
                    'foto' => $foto,
                    'comercioId' => $product->id_vendedor,
                    'categoria' => 'General',
                    'ubicacion' => $product->tienda_ubicacion,
                    'rating' => 4.5,
                    'vendidos' => 0,
                    'tienda_nombre' => $product->nombre_negocio,
                    'tienda_descripcion' => $product->tienda_descripcion,
                ];
            });

        return response()->json($products);
    }

    public function show($id)
    {
        $product = DB::table('productos as p')
            ->join('vendedores as v', 'p.id_vendedor', '=', 'v.id_vendedor')
            ->select(
                'p.id_producto',
                'p.nombre',
                'p.descripcion',
                'p.precio',
                'p.stock',
                'p.imagen_url',
                'p.id_vendedor',
                'v.nombre_negocio',
                'v.ubicacion as tienda_ubicacion',
                'v.descripcion as tienda_descripcion'
            )
            ->where('p.id_producto', $id)
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json([
            'id' => $product->id_producto,
            'nombre' => $product->nombre,
            'descripcion' => $product->descripcion,
            'precio' => (float) $product->precio,
            'stock' => $product->stock,
            'foto' => $product->imagen_url ?: '/imagenes/blusa.png',
            'comercioId' => $product->id_vendedor,
            'categoria' => 'General',
            'ubicacion' => $product->tienda_ubicacion,
            'rating' => 4.5,
            'vendidos' => 0,
            'tienda_nombre' => $product->nombre_negocio,
            'tienda_descripcion' => $product->tienda_descripcion,
        ]);
    }
}
