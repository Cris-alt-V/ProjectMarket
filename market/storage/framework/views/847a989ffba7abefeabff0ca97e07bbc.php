<?php $__env->startSection('title', 'Mis Comercios - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <h1 style="margin-bottom: 30px; color: #333;">Gestionar mis Comercios</h1>

  <?php if(!session('user') || session('user')['tipo_usuario'] !== 'vendedor'): ?>
    <div id="loginPrompt" style="background: white; border-radius: 8px; padding: 40px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <h2>Por favor inicia sesión</h2>
      <p style="color: #666; margin-bottom: 20px;">Solo los vendedores registrados pueden gestionar sus comercios</p>
      <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
        <a href="/registro" class="btn btn-primary">Iniciar Sesión</a>
        <a href="/registro?tab=store" class="btn btn-secondary">Registrar Comercio</a>
      </div>
    </div>
  <?php else: ?>
    <div id="storesContent">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 10px;">
        <h2 style="margin: 0;">Mis Tiendas</h2>
        <button class="btn btn-primary" onclick="document.getElementById('formAgregarProducto').style.display='block'">Agregar Producto</button>
      </div>

      <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;">
          <h3 style="margin: 0 0 10px 0; font-size: 1.5em;">🏪 <?php echo e(session('user')['nombre_negocio'] ?? 'Mi Tienda'); ?></h3>
          <p style="margin: 0; opacity: 0.9;"><?php echo e(session('user')['descripcion'] ?? 'Descripción no disponible'); ?></p>
        </div>
        <div style="padding: 20px;">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 20px;">
            <div>
              <label style="color: #666; font-size: 0.9em;">Ubicación</label>
              <p style="margin: 5px 0; font-weight: bold;"><?php echo e(session('user')['ubicacion'] ?? 'No especificada'); ?></p>
            </div>
            <div>
              <label style="color: #666; font-size: 0.9em;">Correo</label>
              <p style="margin: 5px 0; font-weight: bold;"><?php echo e(session('user')['correo'] ?? 'No disponible'); ?></p>
            </div>
            <div>
              <label style="color: #666; font-size: 0.9em;">Propietario</label>
              <p style="margin: 5px 0; font-weight: bold;"><?php echo e(session('user')['nombre'] ?? 'No disponible'); ?></p>
            </div>
          </div>
        </div>
      </div>

      <h3 style="margin-bottom: 20px;">Mis Productos</h3>
      <?php if($productos->count() > 0): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
          <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
              <img src="<?php echo e($producto->imagen_url ?? '/imagenes/blusa.png'); ?>" alt="<?php echo e($producto->nombre); ?>" style="width: 100%; height: 200px; object-fit: cover;">
              <div style="padding: 15px;">
                <h4 style="margin: 0 0 10px 0;"><?php echo e($producto->nombre); ?></h4>
                <p style="color: #666; font-size: 0.9em; margin: 5px 0;"><?php echo e(Str::limit($producto->descripcion, 100)); ?></p>
                <div style="margin: 10px 0; font-weight: bold; color: #667eea;">
                  $<?php echo e(number_format($producto->precio, 2)); ?>

                </div>
                <div style="margin: 10px 0; font-size: 0.9em; color: #666;">
                  Stock: <?php echo e($producto->stock); ?>

                </div>
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                  <button class="btn btn-primary" style="flex: 1;" onclick="editProduct(<?php echo e($producto->id_producto); ?>)">Editar</button>
                  <form action="/productos/<?php echo e($producto->id_producto); ?>" method="POST" style="flex: 1;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger" style="width: 100%;" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php else: ?>
        <div style="background: #f0f0f0; border-radius: 8px; padding: 30px; text-align: center;">
          <p style="color: #666; margin: 0;">No tienes productos publicados. ¡Comienza ahora!</p>
        </div>
      <?php endif; ?>

      <!-- Formulario agregar producto -->
      <div id="formAgregarProducto" style="display: none; background: white; border-radius: 8px; padding: 30px; margin-top: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0;">Agregar Nuevo Producto</h3>
        <form action="/productos/crear" method="POST" style="display: grid; gap: 15px;">
          <?php echo csrf_field(); ?>
          <div>
            <label>Nombre del Producto</label>
            <input type="text" name="nombre" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
          </div>
          <div>
            <label>Descripción</label>
            <textarea name="descripcion" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; height: 100px;"></textarea>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
              <label>Precio</label>
              <input type="number" name="precio" required step="0.01" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
              <label>Stock</label>
              <input type="number" name="stock" required min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
          </div>
          <div>
            <label>URL de Imagen</label>
            <input type="text" name="imagen_url" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
          </div>
          <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar Producto</button>
            <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="document.getElementById('formAgregarProducto').style.display='none'">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  function editProduct(id) {
    MarketplaceApp.showNotification('Edición de productos próximamente');
  }

  document.addEventListener('DOMContentLoaded', function() {
    updateUserDisplay();
    updateCartBadge();
  });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Yerovi\Desktop\programarket\ProjectMarket\market\resources\views/mis-comercios.blade.php ENDPATH**/ ?>