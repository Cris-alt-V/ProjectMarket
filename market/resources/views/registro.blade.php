@extends('layouts.app')

@section('title', 'Registro - Marketplace Local')

@section('content')
  <div style="max-width: 720px; margin: 0 auto;">
    <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <div style="display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 2px solid #eee;">
        <button class="btn" id="btnLoginTab" onclick="switchTab('login')" style="flex: 1; background: #667eea; color: white; border-radius: 0;">Iniciar Sesión</button>
        <button class="btn" id="btnRegisterTab" onclick="switchTab('register')" style="flex: 1; background: transparent; color: #333; border-radius: 0; border-bottom: 3px solid transparent;">Registrarse como Comprador</button>
        <button class="btn" id="btnStoreTab" onclick="switchTab('store')" style="flex: 1; background: transparent; color: #333; border-radius: 0; border-bottom: 3px solid transparent;">Registrar Comercio</button>
      </div>

      <div id="loginTab" style="display: none;">
        <h2>Iniciar Sesión</h2>
        <form onsubmit="handleLogin(event)">
          <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" id="loginEmail" required>
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" id="loginPassword" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
      </div>

      <div id="registerTab" style="display: none;">
        <h2>Crear Cuenta de Comprador</h2>
        <form onsubmit="handleRegisterBuyer(event)">
          <div class="form-group">
            <label>Nombre Completo</label>
            <input type="text" id="buyerName" required>
          </div>
          <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" id="buyerEmail" required>
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="tel" id="buyerPhone" required>
          </div>
          <div class="form-group">
            <label>Dirección</label>
            <input type="text" id="buyerAddress" required>
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" id="buyerPassword" required>
          </div>
          <div class="form-group">
            <label>Confirmar Contraseña</label>
            <input type="password" id="buyerPasswordConfirm" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Crear Cuenta</button>
        </form>
      </div>

      <div id="storeTab" style="display: none;">
        <h2>Registrar tu Comercio</h2>
        <form onsubmit="handleRegisterStore(event)">
          <div class="form-group">
            <label>Nombre del Comercio</label>
            <input type="text" id="storeName" required>
          </div>
          <div class="form-group">
            <label>Nombre del Propietario</label>
            <input type="text" id="storeOwner" required>
          </div>
          <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" id="storeEmail" required>
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="tel" id="storePhone" required>
          </div>
          <div class="form-group">
            <label>Ubicación del Comercio</label>
            <input type="text" id="storeLocation" required>
          </div>
          <div class="form-group">
            <label>Descripción del Comercio</label>
            <textarea id="storeDescription" required></textarea>
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" id="storePassword" required>
          </div>
          <div class="form-group">
            <label>Confirmar Contraseña</label>
            <input type="password" id="storePasswordConfirm" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Registrar Comercio</button>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  function initializeRegisterPage() {
    const searchParams = new URLSearchParams(window.location.search);
    const tab = searchParams.get('tab') || 'login';
    switchTab(tab);
  }

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

  function handleLogin(e) {
    e.preventDefault();
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const user = {
      id: Math.random(),
      nombre: email.split('@')[0],
      email: email,
      tipo: 'comprador'
    };
    MarketplaceApp.setCurrentUser(user);
    MarketplaceApp.showNotification('¡Bienvenido! Has iniciado sesión correctamente');
    setTimeout(() => window.location.href = '/', 1500);
  }

  function handleRegisterBuyer(e) {
    e.preventDefault();
    const password = document.getElementById('buyerPassword').value;
    const passwordConfirm = document.getElementById('buyerPasswordConfirm').value;
    if (password !== passwordConfirm) {
      MarketplaceApp.showNotification('Las contraseñas no coinciden');
      return;
    }
    const user = {
      id: Math.random(),
      nombre: document.getElementById('buyerName').value,
      email: document.getElementById('buyerEmail').value,
      telefono: document.getElementById('buyerPhone').value,
      direccion: document.getElementById('buyerAddress').value,
      tipo: 'comprador'
    };
    MarketplaceApp.setCurrentUser(user);
    MarketplaceApp.showNotification('¡Registro completado! Bienvenido a Marketplace Local');
    setTimeout(() => window.location.href = '/', 1500);
  }

  function handleRegisterStore(e) {
    e.preventDefault();
    const password = document.getElementById('storePassword').value;
    const passwordConfirm = document.getElementById('storePasswordConfirm').value;
    if (password !== passwordConfirm) {
      MarketplaceApp.showNotification('Las contraseñas no coinciden');
      return;
    }
    const user = {
      id: Math.random(),
      nombre: document.getElementById('storeOwner').value,
      nombreComercio: document.getElementById('storeName').value,
      email: document.getElementById('storeEmail').value,
      telefono: document.getElementById('storePhone').value,
      ubicacion: document.getElementById('storeLocation').value,
      descripcion: document.getElementById('storeDescription').value,
      tipo: 'comercio'
    };
    MarketplaceApp.setCurrentUser(user);
    MarketplaceApp.showNotification('¡Tu comercio ha sido registrado exitosamente!');
    setTimeout(() => window.location.href = '/mis-comercios', 1500);
  }

  document.addEventListener('DOMContentLoaded', function() {
    initializeRegisterPage();
    updateUserDisplay();
    updateCartBadge();
  });
</script>
@endpush