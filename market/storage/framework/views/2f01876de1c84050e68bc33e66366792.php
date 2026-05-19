<?php $__env->startSection('title', 'Mi Cuenta - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <h1 style="margin-bottom: 30px; color: #333;">Mi Cuenta</h1>

  <?php if(!session('user')): ?>
    <div style="background: white; border-radius: 8px; padding: 40px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <h2>Por favor inicia sesión</h2>
      <p style="color: #666; margin-bottom: 20px;">Necesitas iniciar sesión para ver tu cuenta</p>
      <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
        <a href="/registro" class="btn btn-primary">Iniciar Sesión</a>
        <a href="/registro?tab=register" class="btn btn-secondary">Registrarse</a>
      </div>
    </div>
  <?php else: ?>
    <div class="account-container" style="display: grid; grid-template-columns: 250px 1fr; gap: 30px; max-width: 1200px; margin: 0 auto;">
      <div class="sidebar" style="display: flex; flex-direction: column; gap: 10px;">
        <button class="tab-btn active" onclick="switchTab('profile')" style="padding: 10px 15px; text-align: left; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer;">👤 Perfil</button>
        <button class="tab-btn" onclick="switchTab('orders')" style="padding: 10px 15px; text-align: left; background: transparent; color: #333; border: none; border-radius: 4px; cursor: pointer;">📦 Mis Compras</button>
        <button class="tab-btn" onclick="switchTab('addresses')" style="padding: 10px 15px; text-align: left; background: transparent; color: #333; border: none; border-radius: 4px; cursor: pointer;">📍 Direcciones</button>
        <button class="tab-btn" onclick="switchTab('preferences')" style="padding: 10px 15px; text-align: left; background: transparent; color: #333; border: none; border-radius: 4px; cursor: pointer;">⚙️ Preferencias</button>
      </div>

      <div class="content">
        <!-- Perfil -->
        <div id="profileTab" class="tab-content">
          <h2>Mi Perfil</h2>
          <div class="info-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
            <div>
              <label style="color: #666; font-size: 0.9em;">Nombre Completo</label>
              <strong><?php echo e(session('user')['nombre']); ?></strong>
            </div>
            <div>
              <label style="color: #666; font-size: 0.9em;">Correo Electrónico</label>
              <strong><?php echo e(session('user')['correo']); ?></strong>
            </div>
            <div>
              <label style="color: #666; font-size: 0.9em;">Teléfono</label>
              <strong><?php echo e(session('user')['telefono'] ?? 'No especificado'); ?></strong>
            </div>
            <div>
              <label style="color: #666; font-size: 0.9em;">Dirección</label>
              <strong><?php echo e(session('user')['direccion'] ?? 'No especificada'); ?></strong>
            </div>
          </div>
          <button class="btn btn-primary" onclick="switchTab('edit-profile')">Editar Perfil</button>
        </div>

        <!-- Editar Perfil -->
        <div id="editProfileTab" class="tab-content" style="display: none;">
          <h2>Editar Perfil</h2>
          <form action="/auth/profile" method="POST" style="display: grid; gap: 15px; max-width: 500px;">
            <?php echo csrf_field(); ?>
            <div>
              <label>Nombre Completo</label>
              <input type="text" name="nombre" value="<?php echo e(session('user')['nombre']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
              <label>Teléfono</label>
              <input type="tel" name="telefono" value="<?php echo e(session('user')['telefono'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
              <label>Dirección</label>
              <input type="text" name="direccion" value="<?php echo e(session('user')['direccion'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div style="display: flex; gap: 10px;">
              <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar Cambios</button>
              <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="switchTab('profile')">Cancelar</button>
            </div>
          </form>
        </div>

        <!-- Mis Compras -->
        <div id="ordersTab" class="tab-content" style="display: none;">
          <h2>Mis Compras</h2>
          <div style="background: #f0f0f0; border-radius: 8px; padding: 30px; text-align: center;">
            <p style="color: #666; margin: 0;">No tienes compras registradas.</p>
          </div>
        </div>

        <!-- Direcciones -->
        <div id="addressesTab" class="tab-content" style="display: none;">
          <h2>Mis Direcciones</h2>
          <div style="background: #f0f0f0; border-radius: 8px; padding: 30px; text-align: center;">
            <p style="color: #666; margin: 0;">Gestión de direcciones en desarrollo.</p>
          </div>
        </div>

        <!-- Preferencias -->
        <div id="preferencesTab" class="tab-content" style="display: none;">
          <h2>Preferencias</h2>
          <div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="margin-bottom: 20px;">
              <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" checked>
                <span>Recibir notificaciones por correo</span>
              </label>
            </div>
            <div style="margin-bottom: 20px;">
              <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" checked>
                <span>Recibir ofertas y promociones</span>
              </label>
            </div>
            <button type="button" class="btn btn-primary" onclick="MarketplaceApp.showNotification('Preferencias guardadas')">Guardar Preferencias</button>

            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
              <h3>Zona de Peligro</h3>
              <button class="btn btn-danger" onclick="if(confirm('¿Estás seguro?')) { alert('Cuenta eliminada'); }">Eliminar Cuenta</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => {
      el.style.background = 'transparent';
      el.style.color = '#333';
    });

    const tabEl = document.getElementById(tab + 'Tab');
    if (tabEl) tabEl.style.display = 'block';

    const btn = document.querySelector(`button[onclick="switchTab('${tab}')"]`);
    if (btn) {
      btn.style.background = '#667eea';
      btn.style.color = 'white';
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    updateUserDisplay();
    updateCartBadge();
  });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Yerovi\Desktop\programarket\ProjectMarket\market\resources\views/micuenta.blade.php ENDPATH**/ ?>