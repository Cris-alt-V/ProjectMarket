<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Tabla Usuarios
        Schema::create('Usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombre', 100);
            $table->string('correo', 100)->unique();
            $table->string('contraseña', 255);
            $table->string('tipo_usuario', 20);
        });
        DB::statement("ALTER TABLE \"Usuarios\" ADD CONSTRAINT tipo_usuario_check CHECK (tipo_usuario IN ('cliente','vendedor'))");

        // Tabla Clientes
        Schema::create('Clientes', function (Blueprint $table) {
            $table->integer('id_cliente')->primary();
            $table->string('nombre', 100);
            $table->string('direccion', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->foreign('id_cliente')->references('id_usuario')->on('Usuarios')->onDelete('cascade');
        });

        // Tabla Vendedores
        Schema::create('Vendedores', function (Blueprint $table) {
            $table->integer('id_vendedor')->primary();
            $table->string('nombre_negocio', 100);
            $table->text('descripcion')->nullable();
            $table->string('ubicacion', 150)->nullable();
            $table->foreign('id_vendedor')->references('id_usuario')->on('Usuarios')->onDelete('cascade');
        });

        // Tabla Productos
        Schema::create('Productos', function (Blueprint $table) {
            $table->id('id_producto');
            $table->integer('id_vendedor');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('imagen_url', 255)->nullable();
            $table->foreign('id_vendedor')->references('id_vendedor')->on('Vendedores')->onDelete('cascade');
        });

        // Tabla Pedidos
        Schema::create('Pedidos', function (Blueprint $table) {
            $table->id('id_pedido');
            $table->integer('id_cliente');
            $table->date('fecha');
            $table->string('estado', 20);
            $table->foreign('id_cliente')->references('id_cliente')->on('Clientes')->onDelete('cascade');
        });
        DB::statement("ALTER TABLE \"Pedidos\" ADD CONSTRAINT estado_check CHECK (estado IN ('pendiente','enviado','entregado'))");

        // Tabla DetallePedido
        Schema::create('DetallePedido', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->integer('id_pedido');
            $table->integer('id_producto');
            $table->integer('cantidad');
            $table->decimal('subtotal', 10, 2);
            $table->foreign('id_pedido')->references('id_pedido')->on('Pedidos')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('Productos')->onDelete('cascade');
        });

        // Tabla Ventas
        Schema::create('Ventas', function (Blueprint $table) {
            $table->id('id_venta');
            $table->integer('id_pedido');
            $table->integer('id_cliente');
            $table->date('fecha_venta');
            $table->decimal('total', 10, 2);
            $table->string('metodo_pago', 20);
            $table->foreign('id_pedido')->references('id_pedido')->on('Pedidos')->onDelete('cascade');
            $table->foreign('id_cliente')->references('id_cliente')->on('Clientes')->onDelete('cascade');
        });
        DB::statement("ALTER TABLE \"Ventas\" ADD CONSTRAINT metodo_pago_check CHECK (metodo_pago IN ('contra entrega','simulado'))");

        // Tabla Reseñas
        Schema::create('Reseñas', function (Blueprint $table) {
            $table->id('id_reseña');
            $table->integer('id_usuario');
            $table->integer('id_producto');
            $table->integer('puntuacion');
            $table->text('comentario')->nullable();
            $table->date('fecha');
            $table->foreign('id_usuario')->references('id_usuario')->on('Usuarios')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('Productos')->onDelete('cascade');
        });
        DB::statement("ALTER TABLE \"Reseñas\" ADD CONSTRAINT puntuacion_check CHECK (puntuacion BETWEEN 1 AND 5)");
    }

    public function down(): void
    {
        Schema::dropIfExists('Reseñas');
        Schema::dropIfExists('Ventas');
        Schema::dropIfExists('DetallePedido');
        Schema::dropIfExists('Pedidos');
        Schema::dropIfExists('Productos');
        Schema::dropIfExists('Vendedores');
        Schema::dropIfExists('Clientes');
        Schema::dropIfExists('Usuarios');
    }
};
