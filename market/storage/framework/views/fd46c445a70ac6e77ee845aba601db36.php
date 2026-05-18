<?php $__env->startSection('title', 'Mi Cuenta - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <h1 style="margin-bottom: 30px; color: #333;">Mi Cuenta</h1>

  <div id="loginPrompt" style="background: white; border-radius: 8px; padding: 40px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <h2>Por favor inicia sesión</h2>
    <p style="color: #666; margin-bottom: 20px;">Necesitas iniciar sesión para ver tu cuenta</p>
    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
      <a href="/registro" class="btn btn-primary">Iniciar Sesión</a>
      <a href="/registro?tab=register" class="btn btn-secondary">Registrarse</a>
    </div>
  </div>

  <div id="accountContent" style="display: none;">
    <div class="account-container" style="display: grid; grid-template-columns: 250px 1fr; gap: 30px;">
      <div class="sidebar">
        <button class="tab-btn active" onclick="switchTab('profile')">👤 Perfil</button>
        <button class="tab-btn" onclick="switchTab('orders')">📦 Mis Compras</button>
        <button class="tab-btn" onclick="switchTab('addresses')">📍 Direcciones</button>
        <button class="tab-btn" onclick="switchTab('preferences')">⚙️ Preferencias</button>
      </div>
      <div class="content">
        <div id="profileTab" class="tab-content">
          <h2>Mi Perfil</h2>
          <div class="info-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
            <div class="info-item">
              <label>Nombre Completo</label>
              <strong id="displayName">-</strong>
            </div>
            <div class="info-item">
              <label>Correo Electrónico</label>
              <strong id="displayEmail">-</strong>
            </div>
            <div class="info-item">
              <label>Teléfono</label>
              <strong id="displayPhone">-</strong>
            </div>
            <div class="info-item">
              <label>Dirección</label>
              <strong id="displayAddress">-</strong>
            </div>
          </div>
          <button class="btn btn-primary" onclick="switchTab('edit-profile')">Editar Perfil</button>
        </div>

        <div id="editProfileTab" class="tab-content" style="display: none;">
          <h2>Editar Perfil</h2>
          <form onsubmit="saveProfile(event)">
            <div class="form-group">
              <label>Nombre Completo</label>
              <input type="text" id="editName" required>
            </div>
            <div class="form-group">
              <label>Teléfono</label>
              <input type="tel" id="editPhone" required>
            </div>
            <div class="form-group">
              <label>Dirección</label>
              <input type="text" id="editAddress" required>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
              <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              <button type="button" class="btn btn-secondary" onclick="switchTab('profile')">Cancelar</button>
            </div>
          </form>
        </div>

        <div id="ordersTab" class="tab-content" style="display: none;">
          <h2>Mis Compras</h2>
          <div id="ordersContainer"><p style="text-align: center; color: #666;">No hay compras registradas aún</p></div>
        </div>

        <div id="addressesTab" class="tab-content" style="display: none;">
          <h2>Mis Direcciones</h2>
          <div id="addressesContainer"><p style="margin-bottom: 20px;">Aún no has registrado direcciones</p></div>
          <button class="btn btn-primary" onclick="switchTab('add-address')">Agregar Dirección</button>
        </div>

        <div id="addAddressTab" class="tab-content" style="display: none;">
          <h2>Agregar Nueva Dirección</h2>
          <form onsubmit="addAddress(event)">
            <div class="form-group">
              <label>Dirección</label>
              <input type="text" id="newAddressStreet" required>
            </div>
            <div class="form-group">
              <label>Ciudad</label>
              <input type="text" id="newAddressCity" required>
            </div>
            <div class="form-group">
              <label>Código Postal</label>
              <input type="text" id="newAddressZip" required>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
              <button type="submit" class="btn btn-primary">Guardar Dirección</button>
              <button type="button" class="btn btn-secondary" onclick="switchTab('addresses')">Cancelar</button>
            </div>
          </form>
        </div>

        <div id="preferencesTab" class="tab-content" style="display: none;">
          <h2>Preferencias</h2>
          <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px;"><input type="checkbox" id="emailNotifications" checked> Recibir notificaciones por correo electrónico</label>
          </div>
          <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px;"><input type="checkbox" id="smsNotifications" checked> Recibir notificaciones por SMS</label>
          </div>
          <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px;"><input type="checkbox" id="promotions"> Recibir promociones especiales</label>
          </div>
          <button class="btn btn-primary" style="margin-top: 20px;">Guardar Preferencias</button>
          <button class="btn btn-danger" style="margin-top: 10px;" onclick="deleteAccount()">Eliminar Cuenta</button>
        </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

    const tabEl = document.getElementById(tab + 'Tab');
    const btn = document.querySelector(`button[onclick="switchTab('${tab}')"]`);
    if (tabEl) tabEl.style.display = 'block';
    if (btn) btn.classList.add('active');
  }

  async function saveProfile(e) {
    e.preventDefault();
    const user = MarketplaceApp.getCurrentUser();
    if (!user) return;

    const response = await MarketplaceApp.api('/auth/profile', {
      method: 'POST',
      body: {
        nombre: document.getElementById('editName').value,
        telefono: document.getElementById('editPhone').value,
        direccion: document.getElementById('editAddress').value,
      },
    });

    if (!response.ok) {
      const error = await response.json();
      MarketplaceApp.showNotification(error.message || 'Error al actualizar perfil');
      return;
    }

    const data = await response.json();
    MarketplaceApp.setCurrentUser(data.user);
    updateProfile();
    MarketplaceApp.showNotification('Perfil actualizado exitosamente');
    switchTab('profile');
  }

  function addAddress(e) {
    e.preventDefault();
    const street = document.getElementById('newAddressStreet').value;
    const city = document.getElementById('newAddressCity').value;
    const zip = document.getElementById('newAddressZip').value;
    let addresses = JSON.parse(localStorage.getItem('userAddresses')) || [];
    addresses.push({ street, city, zip });
    localStorage.setItem('userAddresses', JSON.stringify(addresses));
    MarketplaceApp.showNotification('Dirección agregada exitosamente');
    switchTab('addresses');
  }

  function deleteAccount() {
    if (!confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.')) return;
    MarketplaceApp.setCurrentUser(null);
    localStorage.removeItem('userAddresses');
    MarketplaceApp.showNotification('Cuenta eliminada');
    setTimeout(() => window.location.href = '/', 1500);
  }

  function updateProfile() {
    const user = MarketplaceApp.getCurrentUser();
    if (!user) return;
    document.getElementById('displayName').textContent = user.nombre;
    document.getElementById('displayEmail').textContent = user.correo || user.email || '-';
    document.getElementById('displayPhone').textContent = user.telefono || '-';
    document.getElementById('displayAddress').textContent = user.direccion || '-';
    document.getElementById('editName').value = user.nombre;
    document.getElementById('editPhone').value = user.telefono || '';
    document.getElementById('editAddress').value = user.direccion || '';
  }

  document.addEventListener('DOMContentLoaded', async function() {
    await MarketplaceApp.syncCurrentUser();
    const user = MarketplaceApp.getCurrentUser();
    if (!user) {
      document.getElementById('loginPrompt').style.display = 'block';
      document.getElementById('accountContent').style.display = 'none';
    } else {
      document.getElementById('loginPrompt').style.display = 'none';
      document.getElementById('accountContent').style.display = 'block';
      updateProfile();
      switchTab('profile');
    }
    updateUserDisplay();
    updateCartBadge();
  });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\castr\OneDrive\Desktop\laravel\xddd\ProjectMarket\market\resources\views/micuenta.blade.php ENDPATH**/ ?>