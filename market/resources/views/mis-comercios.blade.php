@extends('layouts.app')

@section('title', 'Mis Comercios - Marketplace Local')

@section('content')
  <h1 style="margin-bottom: 30px; color: #333;">Gestionar mis Comercios</h1>

  <div id="loginPrompt" style="background: white; border-radius: 8px; padding: 40px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <h2>Por favor inicia sesión</h2>
    <p style="color: #666; margin-bottom: 20px;">Solo los vendedores registrados pueden gestionar sus comercios</p>
    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
      <a href="/registro" class="btn btn-primary">Iniciar Sesión</a>
      <a href="/registro?tab=store" class="btn btn-secondary">Registrar Comercio</a>
    </div>
  </div>

  <div id="storesContent" style="display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 10px;">
      <h2 style="margin: 0;">Mis Tiendas</h2>
      <a href="/registro?tab=store" class="btn btn-primary">Crear Nueva Tienda</a>
    </div>

    <div id="storesGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;"></div>
  </div>
@endsection

@push('scripts')
<script>
  function initializeStoresPage() {
    const user = MarketplaceApp.getCurrentUser();
    const loginPrompt = document.getElementById('loginPrompt');
    const storesContent = document.getElementById('storesContent');
    const storesGrid = document.getElementById('storesGrid');

    if (!user || user.tipo_usuario !== 'vendedor') {
      loginPrompt.style.display = 'block';
      storesContent.style.display = 'none';
    } else {
      loginPrompt.style.display = 'none';
      storesContent.style.display = 'block';
      storesGrid.innerHTML = `
        <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;">
            <h3 style="margin: 0 0 10px 0; font-size: 1.5em;">🏪 ${user.nombre_negocio}</h3>
            <p style="margin: 0; opacity: 0.9;">${user.descripcion}</p>
          </div>
          <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 20px;">
              <div>
                <label style="color: #666; font-size: 0.9em;">Ubicación</label>
                <p style="margin: 5px 0; font-weight: bold;">${user.ubicacion}</p>
              </div>
              <div>
                <label style="color: #666; font-size: 0.9em;">Teléfono</label>
                <p style="margin: 5px 0; font-weight: bold;">${user.telefono || '(Sin teléfono)'}</p>
              </div>
              <div>
                <label style="color: #666; font-size: 0.9em;">Correo</label>
                <p style="margin: 5px 0; font-weight: bold;">${user.correo || user.email}</p>
              </div>
              <div>
                <label style="color: #666; font-size: 0.9em;">Propietario</label>
                <p style="margin: 5px 0; font-weight: bold;">${user.nombre}</p>
              </div>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
              <button class="btn btn-primary" onclick="MarketplaceApp.showNotification('Funcionalidad de edición en desarrollo')">Editar Información</button>
              <button class="btn btn-secondary" onclick="MarketplaceApp.showNotification('Mostrando productos de tu tienda')">Ver Productos</button>
              <button class="btn btn-primary" onclick="MarketplaceApp.showNotification('Funcionalidad de agregar producto en desarrollo')">Agregar Producto</button>
              <button class="btn btn-danger" onclick="deleteStore()">Eliminar Tienda</button>
            </div>
          </div>
        </div>
      `;
    }
  }

  function deleteStore() {
    if (confirm('¿Estás seguro de que deseas eliminar esta tienda?')) {
      MarketplaceApp.setCurrentUser(null);
      MarketplaceApp.showNotification('Tienda eliminada');
      setTimeout(() => window.location.href = '/', 1500);
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    initializeStoresPage();
    updateUserDisplay();
    updateCartBadge();
  });
</script>
@endpush