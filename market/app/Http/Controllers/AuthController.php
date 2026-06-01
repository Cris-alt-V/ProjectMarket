<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function normalizeUser($user)
    {
        if (!$user) {
            return null;
        }

        return [
            'id_usuario' => $user->id_usuario,
            'nombre' => $user->nombre,
            'correo' => $user->correo,
            'tipo_usuario' => $user->tipo_usuario,
            'telefono' => $user->telefono ?? null,
            'direccion' => $user->direccion ?? null,
            'nombre_negocio' => $user->nombre_negocio ?? null,
            'descripcion' => $user->descripcion ?? null,
            'ubicacion' => $user->ubicacion ?? null,
        ];
    }

    private function validationMessages()
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'unique' => 'Ese :attribute ya está registrado.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'confirmed' => 'La confirmación de :attribute no coincide.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'numeric' => 'El campo :attribute debe ser un número.',
            'exists' => 'El :attribute seleccionado no existe.',
        ];
    }

    private function validationAttributes()
    {
        return [
            'nombre' => 'nombre',
            'correo' => 'correo electrónico',
            'telefono' => 'teléfono',
            'direccion' => 'dirección',
            'contraseña' => 'contraseña',
            'contraseña_confirmation' => 'confirmación de contraseña',
            'nombre_propietario' => 'nombre del propietario',
            'nombre_negocio' => 'nombre del comercio',
            'ubicacion' => 'ubicación',
            'descripcion' => 'descripción',
            'id_vendedor' => 'vendedor',
            'precio' => 'precio',
            'stock' => 'stock',
            'imagen_url' => 'imagen',
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contraseña' => 'required|string',
        ], $this->validationMessages(), $this->validationAttributes());

        $usuario = DB::table('usuarios')->where('correo', $request->correo)->first();

        if (!$usuario) {
            return redirect('/registro')->with('error', 'Credenciales incorrectas')->with('tab', 'login');
        }

        $password = $request->contraseña;
        $stored = $usuario->contraseña;

        $valid = false;
        if (Hash::check($password, $stored)) {
            $valid = true;
        } elseif ($password === $stored) {
            $valid = true;
            DB::table('usuarios')->where('id_usuario', $usuario->id_usuario)->update([
                'contraseña' => Hash::make($password),
            ]);
        }

        if (!$valid) {
            return redirect('/registro')->with('error', 'Credenciales incorrectas')->with('tab', 'login');
        }

        if ($usuario->tipo_usuario === 'cliente') {
            $extra = DB::table('clientes')->where('id_cliente', $usuario->id_usuario)->first();
        } else {
            $extra = DB::table('vendedores')->where('id_vendedor', $usuario->id_usuario)->first();
        }

        $sessionUser = (object) array_merge((array) $usuario, (array) $extra);
        $request->session()->put('user', $this->normalizeUser($sessionUser));

        return redirect('/')->with('success', '¡Bienvenido! Has iniciado sesión correctamente');
    }

    public function registerBuyer(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:150',
            'contraseña' => 'required|string|min:6|confirmed',
        ], $this->validationMessages(), $this->validationAttributes());

        $id = DB::table('usuarios')->insertGetId([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'contraseña' => Hash::make($request->contraseña),
            'tipo_usuario' => 'cliente',
        ], 'id_usuario');

        DB::table('clientes')->insert([
            'id_cliente' => $id,
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
        ]);

        $usuario = DB::table('usuarios')->where('id_usuario', $id)->first();
        $cliente = DB::table('clientes')->where('id_cliente', $id)->first();
        $sessionUser = (object) array_merge((array) $usuario, (array) $cliente);
        $request->session()->put('user', $this->normalizeUser($sessionUser));

        return redirect('/')->with('success', 'Cuenta de comprador creada exitosamente');
    }

    public function registerStore(Request $request)
    {
        $request->validate([
            'nombre_propietario' => 'required|string|max:100',
            'nombre_negocio' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo',
            'telefono' => 'nullable|string|max:20',
            'ubicacion' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
            'contraseña' => 'required|string|min:6|confirmed',
        ], $this->validationMessages(), $this->validationAttributes());

        $id = DB::table('usuarios')->insertGetId([
            'nombre' => $request->nombre_propietario,
            'correo' => $request->correo,
            'contraseña' => Hash::make($request->contraseña),
            'tipo_usuario' => 'vendedor',
        ], 'id_usuario');

        DB::table('vendedores')->insert([
            'id_vendedor' => $id,
            'nombre_negocio' => $request->nombre_negocio,
            'descripcion' => $request->descripcion,
            'ubicacion' => $request->ubicacion,
        ]);

        $usuario = DB::table('usuarios')->where('id_usuario', $id)->first();
        $vendedor = DB::table('vendedores')->where('id_vendedor', $id)->first();
        $sessionUser = (object) array_merge((array) $usuario, (array) $vendedor);
        $request->session()->put('user', $this->normalizeUser($sessionUser));

        return redirect('/mis-comercios')->with('success', 'Comercio registrado exitosamente');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user');
        return redirect('/')->with('success', 'Sesión cerrada');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect('/registro');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:150',
        ], $this->validationMessages(), $this->validationAttributes());

        DB::table('usuarios')->where('id_usuario', $user['id_usuario'])->update([
            'nombre' => $request->nombre,
        ]);

        if ($user['tipo_usuario'] === 'cliente') {
            DB::table('clientes')->where('id_cliente', $user['id_usuario'])->update([
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
            ]);
        }

        $usuario = DB::table('usuarios')->where('id_usuario', $user['id_usuario'])->first();
        $extra = DB::table($user['tipo_usuario'] === 'cliente' ? 'clientes' : 'vendedores')
            ->where($user['tipo_usuario'] === 'cliente' ? 'id_cliente' : 'id_vendedor', $user['id_usuario'])
            ->first();

        $sessionUser = (object) array_merge((array) $usuario, (array) $extra);
        $request->session()->put('user', $this->normalizeUser($sessionUser));

        return redirect('/micuenta')->with('success', 'Perfil actualizado');
    }

    public function addproduct(Request $request)
    {
        $request->validate([
            'id_vendedor' => 'required|integer|exists:vendedores,id_vendedor',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'imagen_url' => 'nullable|string',
        ], $this->validationMessages(), $this->validationAttributes());

        $id = DB::table('productos')->insertGetId([
            'id_vendedor' => $request->id_vendedor,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock ?? 0,
            'imagen_url' => $this->normalizeImageUrl($request->imagen_url),
        ], 'id_producto');

        return response()->json([
            'success' => true,
            'id_producto' => $id,
        ]);
    }
}
