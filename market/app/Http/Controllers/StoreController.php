<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function products(Request $request)
    {
        $user = $request->session()->get('user');
        if (!$user || $user['tipo_usuario'] !== 'vendedor') {
            return redirect('/registro')->with('error', 'No autorizado');
        }

        $productos = DB::table('productos')
            ->where('id_vendedor', $user['id_usuario'])
            ->get();

        return view('mis-comercios', compact('productos'));
    }

    public function store(Request $request)
    {
        $user = $request->session()->get('user');
        if (!$user || $user['tipo_usuario'] !== 'vendedor') {
            return redirect('/registro')->with('error', 'No autorizado');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen_url' => 'nullable|string|max:255',
        ]);

        DB::table('productos')->insert([
            'id_vendedor' => $user['id_usuario'],
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock,
            'imagen_url' => $request->imagen_url,
        ]);

        return redirect('/mis-comercios')->with('success', 'Producto agregado correctamente');
    }

    public function update(Request $request, $id)
    {
        $user = $request->session()->get('user');
        if (!$user || $user['tipo_usuario'] !== 'vendedor') {
            return redirect('/registro')->with('error', 'No autorizado');
        }

        $producto = DB::table('productos')->where('id_producto', $id)->first();
        if (!$producto || $producto->id_vendedor !== $user['id_usuario']) {
            return redirect('/mis-comercios')->with('error', 'Producto no encontrado');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen_url' => 'nullable|string|max:255',
        ]);

        DB::table('productos')->where('id_producto', $id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock,
            'imagen_url' => $request->imagen_url,
        ]);

        return redirect('/mis-comercios')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->session()->get('user');
        if (!$user || $user['tipo_usuario'] !== 'vendedor') {
            return redirect('/registro')->with('error', 'No autorizado');
        }

        $producto = DB::table('productos')->where('id_producto', $id)->first();
        if (!$producto || $producto->id_vendedor !== $user['id_usuario']) {
            return redirect('/mis-comercios')->with('error', 'Producto no encontrado');
        }

        DB::table('productos')->where('id_producto', $id)->delete();

        return redirect('/mis-comercios')->with('success', 'Producto eliminado correctamente');
    }
}
