<?php $__env->startSection('title', 'Registro - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <div style="max-width: 720px; margin: 0 auto;">
    <?php if($errors->any()): ?>
      <div style="background: #fee; color: #c00; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <strong>Errores:</strong>
        <ul style="margin: 10px 0 0 20px;">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div style="background: #fee; color: #c00; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <?php echo e(session('error')); ?>

      </div>
    <?php endif; ?>

    <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <div style="display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 2px solid #eee;">
        <button class="btn" id="btnLoginTab" onclick="switchTab('login')" style="flex: 1; background: #667eea; color: white; border-radius: 0; border: none; cursor: pointer; padding: 10px;">Iniciar Sesión</button>
        <button class="btn" id="btnRegisterTab" onclick="switchTab('register')" style="flex: 1; background: transparent; color: #333; border-radius: 0; border: none; cursor: pointer; padding: 10px; border-bottom: 3px solid transparent;">Registrarse como Comprador</button>
        <button class="btn" id="btnStoreTab" onclick="switchTab('store')" style="flex: 1; background: transparent; color: #333; border-radius: 0; border: none; cursor: pointer; padding: 10px; border-bottom: 3px solid transparent;">Registrar Comercio</button>
      </div>

      <!-- Login -->
      <div id="loginTab" style="display: block;">
        <h2>Iniciar Sesión</h2>
        <form action="/auth/login" method="POST">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" name="correo" required value="<?php echo e(old('correo')); ?>">
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="contraseña" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
      </div>

      <!-- Registro Comprador -->
      <div id="registerTab" style="display: none;">
        <h2>Crear Cuenta de Comprador</h2>
        <form action="/auth/register-buyer" method="POST">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label>Nombre Completo</label>
            <input type="text" name="nombre" required value="<?php echo e(old('nombre')); ?>">
          </div>
          <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" name="correo" required value="<?php echo e(old('correo')); ?>">
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="tel" name="telefono" value="<?php echo e(old('telefono')); ?>">
          </div>
          <div class="form-group">
            <label>Dirección</label>
            <input type="text" name="direccion" value="<?php echo e(old('direccion')); ?>">
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="contraseña" required>
          </div>
          <div class="form-group">
            <label>Confirmar Contraseña</label>
            <input type="password" name="contraseña_confirmation" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Crear Cuenta</button>
        </form>
      </div>

      <!-- Registro Comercio -->
      <div id="storeTab" style="display: none;">
        <h2>Registrar tu Comercio</h2>
        <form action="/auth/register-store" method="POST">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label>Nombre del Comercio</label>
            <input type="text" name="nombre_negocio" required value="<?php echo e(old('nombre_negocio')); ?>">
          </div>
          <div class="form-group">
            <label>Nombre del Propietario</label>
            <input type="text" name="nombre_propietario" required value="<?php echo e(old('nombre_propietario')); ?>">
          </div>
          <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" name="correo" required value="<?php echo e(old('correo')); ?>">
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="tel" name="telefono" value="<?php echo e(old('telefono')); ?>">
          </div>
          <div class="form-group">
            <label>Ubicación del Comercio</label>
            <input type="text" name="ubicacion" value="<?php echo e(old('ubicacion')); ?>">
          </div>
          <div class="form-group">
            <label>Descripción del Comercio</label>
            <textarea name="descripcion"><?php echo e(old('descripcion')); ?></textarea>
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="contraseña" required>
          </div>
          <div class="form-group">
            <label>Confirmar Contraseña</label>
            <input type="password" name="contraseña_confirmation" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Registrar Comercio</button>
        </form>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  function switchTab(tab) {
    ['login', 'register', 'store'].forEach(name => {
      const el = document.getElementById(name + 'Tab');
      const btn = document.getElementById('btn' + name.charAt(0).toUpperCase() + name.slice(1) + 'Tab');
      if (el && btn) {
        el.style.display = name === tab ? 'block' : 'none';
        btn.style.background = name === tab ? '#667eea' : 'transparent';
        btn.style.color = name === tab ? 'white' : '#333';
        btn.style.borderBottom = name === tab ? '3px solid #667eea' : 'transparent';
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    const tab = new URLSearchParams(window.location.search).get('tab') || 'login';
    switchTab(tab);
  });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\castr\OneDrive\Desktop\laravel\hola\ProjectMarket\market\resources\views/registro.blade.php ENDPATH**/ ?>