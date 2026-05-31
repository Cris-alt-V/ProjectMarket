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

    protected function saveProductImage(Request $request): ?string
    {
        if ($request->hasFile('imagen_file') && $request->file('imagen_file')->isValid()) {
            $file = $request->file('imagen_file');
            $destination = public_path('imagenes');
            if (! is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $filename = uniqid('producto_', true) . '.' . $extension;
            $file->move($destination, $filename);

            return '/imagenes/' . $filename;
        }

        $dataUrl = $request->input('imagen_data');
        if ($dataUrl) {
            if (preg_match('/^data:image\/(png|jpeg|jpg|gif|webp|svg)\;base64,(.+)$/i', $dataUrl, $matches)) {
                $extension = strtolower($matches[1]);
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }

                $decoded = base64_decode($matches[2]);
                if ($decoded === false) {
                    return null;
                }

                $destination = public_path('imagenes');
                if (! is_dir($destination)) {
                    mkdir($destination, 0755, true);
                }

                $filename = uniqid('producto_', true) . '.' . $extension;
                file_put_contents($destination . DIRECTORY_SEPARATOR . $filename, $decoded);

                return '/imagenes/' . $filename;
            }

            return null;
        }

        return null;
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
            'imagen_file' => 'nullable|image|max:5120',
            'imagen_data' => 'nullable|string',
            'imagen_url' => 'nullable|string',
        ]);

        $imagen = $this->saveProductImage($request) ?? $this->normalizeImageUrl($request->imagen_url);

        DB::table('productos')->insert([
            'id_vendedor' => $user['id_usuario'],
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock,
            'imagen_url' => $imagen,
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
            'imagen_file' => 'nullable|image|max:5120',
            'imagen_data' => 'nullable|string',
            'imagen_url' => 'nullable|string',
        ]);

        $imagen = $this->saveProductImage($request) ?? $this->normalizeImageUrl($request->imagen_url);

        DB::table('productos')->where('id_producto', $id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock,
            'imagen_url' => $imagen,
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
