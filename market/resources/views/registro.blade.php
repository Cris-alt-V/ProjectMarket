@extends('layouts.app')

@section('title', 'Registro - Marketplace Local')

@section('content')
<div class="auth-container">
  <div class="auth-wrapper">
    <!-- Header del Auth -->
    <div class="auth-header">
      <div class="auth-header-brand">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M16 2L2 8V16C2 26 16 30 16 30C16 30 30 26 30 16V8L16 2Z" fill="#667eea" stroke="#667eea" stroke-width="1.5"/>
          <path d="M16 16L20 12L22 14L16 20L10 14L12 12L16 16Z" fill="white"/>
        </svg>
        <span>AristoMarket</span>
      </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="auth-tabs">
      <button class="tab-btn active" id="btnLoginTab" onclick="switchTab('login')">
        <span class="tab-icon">🔐</span>
        Iniciar Sesión
      </button>
      <button class="tab-btn" id="btnRegisterTab" onclick="switchTab('register')">
        <span class="tab-icon">👤</span>
        Comprador
      </button>
      <button class="tab-btn" id="btnStoreTab" onclick="switchTab('store')">
        <span class="tab-icon">🏪</span>
        Comercio
      </button>
    </div>

    <!-- Login Tab -->
    <div id="loginTab" class="auth-tab-content active">
      <h1>Bienvenido</h1>
      <p class="auth-subtitle">Inicia sesión para continuar</p>
      
      <form onsubmit="handleLogin(event)" class="auth-form">
        <div class="form-group-auth">
          <label for="loginEmail">Correo Electrónico</label>
          <div class="input-wrapper">
            <span class="input-icon">✉️</span>
            <input type="email" id="loginEmail" placeholder="tu@email.com" required>
          </div>
        </div>

        <div class="form-group-auth">
          <label for="loginPassword">Contraseña</label>
          <div class="input-wrapper">
            <span class="input-icon">🔑</span>
            <input type="password" id="loginPassword" placeholder="••••••••" required>
          </div>
        </div>

        <div class="form-remember">
          <input type="checkbox" id="rememberMe">
          <label for="rememberMe">Recuérdame</label>
        </div>

        <button type="submit" class="auth-btn btn-primary">Iniciar Sesión</button>
      </form>

      <div class="auth-footer">
        <p>¿No tienes cuenta? <a href="#" onclick="switchTab('register'); return false;">Regístrate aquí</a></p>
      </div>
    </div>

    <!-- Register Buyer Tab -->
    <div id="registerTab" class="auth-tab-content">
      <h1>Crear Cuenta de Comprador</h1>
      <p class="auth-subtitle">Únete a nuestro marketplace</p>
      
      <form onsubmit="handleRegisterBuyer(event)" class="auth-form">
        <div class="form-row">
          <div class="form-group-auth">
            <label for="buyerName">Nombre Completo</label>
            <div class="input-wrapper">
              <span class="input-icon">👤</span>
              <input type="text" id="buyerName" placeholder="Tu nombre" required>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group-auth">
            <label for="buyerEmail">Correo Electrónico</label>
            <div class="input-wrapper">
              <span class="input-icon">✉️</span>
              <input type="email" id="buyerEmail" placeholder="tu@email.com" required>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group-auth">
            <label for="buyerPhone">Teléfono</label>
            <div class="input-wrapper">
              <span class="input-icon">📱</span>
              <input type="tel" id="buyerPhone" placeholder="(123) 456-7890" required>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group-auth">
            <label for="buyerAddress">Dirección</label>
            <div class="input-wrapper">
              <span class="input-icon">📍</span>
              <input type="text" id="buyerAddress" placeholder="Tu dirección" required>
            </div>
          </div>
        </div>

        <div class="form-row-two">
          <div class="form-group-auth">
            <label for="buyerPassword">Contraseña</label>
            <div class="input-wrapper">
              <span class="input-icon">🔐</span>
              <input type="password" id="buyerPassword" placeholder="••••••••" required>
            </div>
          </div>

          <div class="form-group-auth">
            <label for="buyerPasswordConfirm">Confirmar Contraseña</label>
            <div class="input-wrapper">
              <span class="input-icon">🔐</span>
              <input type="password" id="buyerPasswordConfirm" placeholder="••••••••" required>
            </div>
          </div>
        </div>

        <div class="form-terms">
          <input type="checkbox" id="termsAccept">
          <label for="termsAccept">Acepto los términos y condiciones</label>
        </div>

        <button type="submit" class="auth-btn btn-primary">Crear Cuenta</button>
      </form>

      <div class="auth-footer">
        <p>¿Ya tienes cuenta? <a href="#" onclick="switchTab('login'); return false;">Inicia sesión aquí</a></p>
      </div>
    </div>

    <!-- Register Store Tab -->
    <div id="storeTab" class="auth-tab-content">
      <h1>Registrar tu Comercio</h1>
      <p class="auth-subtitle">Comienza a vender en nuestro marketplace</p>
      
      <form onsubmit="handleRegisterStore(event)" class="auth-form">
        <div class="form-row-two">
          <div class="form-group-auth">
            <label for="storeName">Nombre del Comercio</label>
            <div class="input-wrapper">
              <span class="input-icon">🏪</span>
              <input type="text" id="storeName" placeholder="Mi Tienda" required>
            </div>
          </div>

          <div class="form-group-auth">
            <label for="storeOwner">Nombre del Propietario</label>
            <div class="input-wrapper">
              <span class="input-icon">👤</span>
              <input type="text" id="storeOwner" placeholder="Tu nombre" required>
            </div>
          </div>
        </div>

        <div class="form-row-two">
          <div class="form-group-auth">
            <label for="storeEmail">Correo Electrónico</label>
            <div class="input-wrapper">
              <span class="input-icon">✉️</span>
              <input type="email" id="storeEmail" placeholder="contacto@tienda.com" required>
            </div>
          </div>

          <div class="form-group-auth">
            <label for="storePhone">Teléfono</label>
            <div class="input-wrapper">
              <span class="input-icon">📱</span>
              <input type="tel" id="storePhone" placeholder="(123) 456-7890" required>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group-auth">
            <label for="storeLocation">Ubicación del Comercio</label>
            <div class="input-wrapper">
              <span class="input-icon">📍</span>
              <input type="text" id="storeLocation" placeholder="Dirección de tu tienda" required>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group-auth">
            <label for="storeDescription">Descripción del Comercio</label>
            <div class="textarea-wrapper">
              <span class="input-icon">📝</span>
              <textarea id="storeDescription" placeholder="Describe tu comercio..." required></textarea>
            </div>
          </div>
        </div>

        <div class="form-row-two">
          <div class="form-group-auth">
            <label for="storePassword">Contraseña</label>
            <div class="input-wrapper">
              <span class="input-icon">🔐</span>
              <input type="password" id="storePassword" placeholder="••••••••" required>
            </div>
          </div>

          <div class="form-group-auth">
            <label for="storePasswordConfirm">Confirmar Contraseña</label>
            <div class="input-wrapper">
              <span class="input-icon">🔐</span>
              <input type="password" id="storePasswordConfirm" placeholder="••••••••" required>
            </div>
          </div>
        </div>

        <div class="form-terms">
          <input type="checkbox" id="storeTermsAccept">
          <label for="storeTermsAccept">Acepto los términos y condiciones para comerciantes</label>
        </div>

        <button type="submit" class="auth-btn btn-primary">Registrar Comercio</button>
      </form>

      <div class="auth-footer">
        <p>¿Ya tienes cuenta? <a href="#" onclick="switchTab('login'); return false;">Inicia sesión aquí</a></p>
      </div>
    </div>
  </div>

  <!-- Decorative Elements -->
  <div class="auth-decoration"></div>
</div>

<style>
/* ======================== AUTH CONTAINER ======================== */
.auth-container {
  min-height: calc(100vh - 100px);
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  position: relative;
  overflow: hidden;
}

.auth-decoration {
  position: absolute;
  width: 400px;
  height: 400px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 50%;
  top: -200px;
  right: -200px;
  pointer-events: none;
}

.auth-decoration::after {
  content: '';
  position: absolute;
  width: 400px;
  height: 400px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 50%;
  bottom: -250px;
  left: -250px;
}

.auth-wrapper {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  max-width: 500px;
  width: 100%;
  overflow: hidden;
  animation: slideUp 0.5s ease-out;
  position: relative;
  z-index: 10;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ======================== AUTH HEADER ======================== */
.auth-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 30px 20px;
  text-align: center;
}

.auth-header-brand {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  font-size: 24px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

/* ======================== AUTH TABS ======================== */
.auth-tabs {
  display: flex;
  border-bottom: 2px solid #e8e8f0;
}

.tab-btn {
  flex: 1;
  padding: 15px 10px;
  background: transparent;
  border: none;
  cursor: pointer;
  font-weight: 600;
  color: #999;
  border-bottom: 3px solid transparent;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 14px;
}

.tab-btn:hover {
  color: #667eea;
}

.tab-btn.active {
  color: #667eea;
  border-bottom-color: #667eea;
}

.tab-icon {
  font-size: 18px;
}

/* ======================== TAB CONTENT ======================== */
.auth-tab-content {
  display: none;
  padding: 40px;
  animation: fadeIn 0.3s ease-in;
}

.auth-tab-content.active {
  display: block;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.auth-tab-content h1 {
  font-size: 28px;
  color: #1a1a2e;
  margin-bottom: 8px;
  font-weight: 700;
}

.auth-subtitle {
  color: #999;
  font-size: 14px;
  margin-bottom: 25px;
}

/* ======================== FORM GROUPS ======================== */
.form-group-auth {
  margin-bottom: 18px;
}

.form-row {
  display: flex;
  gap: 15px;
  margin-bottom: 0;
}

.form-row .form-group-auth {
  flex: 1;
}

.form-row-two {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
  margin-bottom: 0;
}

.form-row-two .form-group-auth {
  margin-bottom: 18px;
}

.form-group-auth label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #333;
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* ======================== INPUT WRAPPER ======================== */
.input-wrapper,
.textarea-wrapper {
  display: flex;
  align-items: center;
  border: 2px solid #e8e8f0;
  border-radius: 8px;
  overflow: hidden;
  transition: all 0.3s ease;
  background: #f9f9fb;
}

.input-wrapper:focus-within,
.textarea-wrapper:focus-within {
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.input-icon {
  padding: 0 12px;
  font-size: 18px;
  color: #999;
  flex-shrink: 0;
  display: flex;
  align-items: center;
}

.input-wrapper input,
.textarea-wrapper textarea {
  flex: 1;
  border: none;
  background: transparent;
  padding: 12px 0 12px 0;
  font-size: 14px;
  color: #1a1a2e;
  outline: none;
  font-family: inherit;
}

.input-wrapper input::placeholder,
.textarea-wrapper textarea::placeholder {
  color: #ccc;
}

.textarea-wrapper {
  align-items: flex-start;
  padding: 0;
}

.textarea-wrapper textarea {
  padding: 12px 0;
  resize: vertical;
  min-height: 100px;
  max-height: 150px;
}

/* ======================== FORM ELEMENTS ======================== */
.form-remember,
.form-terms {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 18px 0;
  font-size: 13px;
}

.form-remember input,
.form-terms input {
  width: 16px;
  height: 16px;
  cursor: pointer;
  accent-color: #667eea;
}

.form-remember label,
.form-terms label {
  cursor: pointer;
  color: #666;
}

/* ======================== AUTH BUTTON ======================== */
.auth-btn {
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 10px;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.auth-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.auth-btn:active {
  transform: translateY(0);
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* ======================== AUTH FOOTER ======================== */
.auth-footer {
  text-align: center;
  margin-top: 20px;
  font-size: 13px;
  color: #666;
}

.auth-footer a {
  color: #667eea;
  font-weight: 600;
  transition: color 0.3s ease;
}

.auth-footer a:hover {
  color: #764ba2;
}

/* ======================== RESPONSIVE ======================== */
@media (max-width: 600px) {
  .auth-wrapper {
    border-radius: 8px;
  }

  .auth-tab-content {
    padding: 25px;
  }

  .auth-tab-content h1 {
    font-size: 22px;
  }

  .form-row-two {
    grid-template-columns: 1fr;
  }

  .tab-btn {
    font-size: 12px;
    padding: 12px 5px;
  }

  .tab-icon {
    display: none;
  }
}
</style>
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