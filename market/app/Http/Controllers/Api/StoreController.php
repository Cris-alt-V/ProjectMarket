<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    protected function currentUser(Request $request)
    {
        return $request->session()->get('user');
    }

    protected function vendorForUser($user)
    {
        return DB::table('vendedores')->where('id_vendedor', $user['id_usuario'])->first();
    }

    public function products(Request $request)
    {
        $user = $this->currentUser($request);
        if (! $user || $user['tipo_usuario'] !== 'vendedor') {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $vendor = $this->vendorForUser($user);
        if (! $vendor) {
            return response()->json(['message' => 'Vendedor no encontrado'], 404);
        }

        $products = DB::table('productos')->where('id_vendedor', $vendor->id_vendedor)->get()->map(function ($product) {
            return [
                'id' => $product->id_producto,
                'nombre' => $product->nombre,
                'descripcion' => $product->descripcion,
                'precio' => (float) $product->precio,
                'stock' => $product->stock,
                'imagen_url' => $product->imagen_url,
                'id_vendedor' => $product->id_vendedor,
            ];
        });

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $user = $this->currentUser($request);
        if (! $user || $user['tipo_usuario'] !== 'vendedor') {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen_url' => 'nullable|string',
        ]);

        $vendor = $this->vendorForUser($user);
        if (! $vendor) {
            return response()->json(['message' => 'Vendedor no encontrado'], 404);
        }

        $imagen = $this->normalizeImageUrl($request->imagen_url);

        $id = DB::table('productos')->insertGetId([
            'id_vendedor' => $vendor->id_vendedor,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock,
            'imagen_url' => $imagen,
        ], 'id_producto');

        return response()->json(['message' => 'Producto creado', 'id' => $id]);
    }

    public function update(Request $request, $id)
    {
        $user = $this->currentUser($request);
        if (! $user || $user['tipo_usuario'] !== 'vendedor') {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $vendor = $this->vendorForUser($user);
        if (! $vendor) {
            return response()->json(['message' => 'Vendedor no encontrado'], 404);
        }

        $product = DB::table('productos')->where('id_producto', $id)->where('id_vendedor', $vendor->id_vendedor)->first();
        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen_url' => 'nullable|string',
        ]);

        $imagen = $this->normalizeImageUrl($request->imagen_url);

        DB::table('productos')->where('id_producto', $id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock,
            'imagen_url' => $imagen,
        ]);

        return response()->json(['message' => 'Producto actualizado']);
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->currentUser($request);
        if (! $user || $user['tipo_usuario'] !== 'vendedor') {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $vendor = $this->vendorForUser($user);
        if (! $vendor) {
            return response()->json(['message' => 'Vendedor no encontrado'], 404);
        }

        DB::table('productos')->where('id_producto', $id)->where('id_vendedor', $vendor->id_vendedor)->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }
}
