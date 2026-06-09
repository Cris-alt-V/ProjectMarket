<?php $__env->startSection('title', 'Acceso - AristoMarket'); ?>

<?php $__env->startSection('content'); ?>
  <section class="auth-page">
    <div class="auth-shell">
      <div class="auth-brand-panel">
        <div class="auth-brand-mark">A</div>
        <p class="auth-eyebrow">Marketplace local</p>
        <h1>AristoMarket</h1>
        <p>Compra productos cercanos, administra tu cuenta o registra tu comercio desde un solo lugar.</p>

        <div class="auth-benefits">
          <div>
            <strong>Compradores</strong>
            <span>Acceso rápido a productos y pedidos.</span>
          </div>
          <div>
            <strong>Comercios</strong>
            <span>Herramientas para vender y gestionar tu catálogo.</span>
          </div>
        </div>
      </div>

      <div class="auth-card">
        <div class="auth-card-header">
          <div>
            <p class="auth-eyebrow">Bienvenido</p>
            <h2 id="authTitle">Iniciar sesión</h2>
          </div>
          <span class="auth-status">Seguro</span>
        </div>

        <div class="auth-tabs" role="tablist" aria-label="Opciones de acceso">
          <button type="button" class="auth-tab active" id="btnLoginTab" onclick="switchTab('login')" role="tab" aria-controls="loginTab">
            <span class="auth-tab-icon">L</span>
            <span>Iniciar sesión</span>
          </button>
          <button type="button" class="auth-tab" id="btnRegisterTab" onclick="switchTab('register')" role="tab" aria-controls="registerTab">
            <span class="auth-tab-icon">C</span>
            <span>Comprador</span>
          </button>
          <button type="button" class="auth-tab" id="btnStoreTab" onclick="switchTab('store')" role="tab" aria-controls="storeTab">
            <span class="auth-tab-icon">T</span>
            <span>Comercio</span>
          </button>
        </div>

        <?php if($errors->any()): ?>
          <div class="auth-alert auth-alert-error">
            <strong>Revisá estos campos:</strong>
            <ul>
              <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
          <div class="auth-alert auth-alert-error">
            <?php echo e(session('error')); ?>

          </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
          <div class="auth-alert auth-alert-success">
            <?php echo e(session('success')); ?>

          </div>
        <?php endif; ?>

        <div class="auth-panel active" id="loginTab" role="tabpanel" aria-labelledby="btnLoginTab">
          <p class="auth-panel-copy">Ingresá con tu correo y contraseña para continuar.</p>
          <form action="/auth/login" method="POST" class="auth-form">
            <?php echo csrf_field(); ?>
            <div class="auth-field">
              <label for="loginCorreo">Correo electrónico</label>
              <div class="auth-input-wrap">
                <span>@</span>
                <input id="loginCorreo" type="email" name="correo" autocomplete="email" required value="<?php echo e(old('correo')); ?>" placeholder="tu@email.com">
              </div>
            </div>

            <div class="auth-field">
              <label for="loginPassword">Contraseña</label>
              <div class="auth-input-wrap">
                <span>*</span>
                <input id="loginPassword" type="password" name="contraseña" autocomplete="current-password" required placeholder="********">
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block auth-submit">Iniciar sesión</button>
          </form>
        </div>

        <div class="auth-panel" id="registerTab" role="tabpanel" aria-labelledby="btnRegisterTab">
          <p class="auth-panel-copy">Crea una cuenta para guardar tus datos y comprar con mayor rapidez.</p>
          <form action="/auth/register-buyer" method="POST" class="auth-form">
            <?php echo csrf_field(); ?>
            <div class="auth-form-grid">
              <div class="auth-field">
                <label for="buyerNombre">Nombre completo</label>
                <div class="auth-input-wrap">
                  <span>N</span>
                  <input id="buyerNombre" type="text" name="nombre" autocomplete="name" required value="<?php echo e(old('nombre')); ?>" placeholder="Tu nombre">
                </div>
              </div>

              <div class="auth-field">
                <label for="buyerCorreo">Correo electrónico</label>
                <div class="auth-input-wrap">
                  <span>@</span>
                  <input id="buyerCorreo" type="email" name="correo" autocomplete="email" required value="<?php echo e(old('correo')); ?>" placeholder="tu@email.com">
                </div>
              </div>

              <div class="auth-field">
                <label for="buyerTelefono">Teléfono</label>
                <div class="auth-input-wrap">
                  <span>#</span>
                  <input id="buyerTelefono" type="tel" name="telefono" autocomplete="tel" value="<?php echo e(old('telefono')); ?>" placeholder="5555-5555">
                </div>
              </div>

              <div class="auth-field">
                <label for="buyerDireccion">Dirección</label>
                <div class="auth-input-wrap">
                  <span>D</span>
                  <input id="buyerDireccion" type="text" name="direccion" autocomplete="street-address" value="<?php echo e(old('direccion')); ?>" placeholder="Ciudad, zona o colonia">
                </div>
              </div>

              <div class="auth-field">
                <label for="buyerPassword">Contraseña</label>
                <div class="auth-input-wrap">
                  <span>*</span>
                  <input id="buyerPassword" type="password" name="contraseña" autocomplete="new-password" required placeholder="Mínimo 6 caracteres">
                </div>
              </div>

              <div class="auth-field">
                <label for="buyerPasswordConfirm">Confirmar contraseña</label>
                <div class="auth-input-wrap">
                  <span>*</span>
                  <input id="buyerPasswordConfirm" type="password" name="contraseña_confirmation" autocomplete="new-password" required placeholder="Repite tu contraseña">
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block auth-submit">Crear cuenta de comprador</button>
          </form>
        </div>

        <div class="auth-panel" id="storeTab" role="tabpanel" aria-labelledby="btnStoreTab">
          <p class="auth-panel-copy">Registra tu comercio y empieza a publicar productos para tus clientes.</p>
          <form action="/auth/register-store" method="POST" class="auth-form">
            <?php echo csrf_field(); ?>
            <div class="auth-form-grid">
              <div class="auth-field">
                <label for="storeName">Nombre del comercio</label>
                <div class="auth-input-wrap">
                  <span>T</span>
                  <input id="storeName" type="text" name="nombre_negocio" required value="<?php echo e(old('nombre_negocio')); ?>" placeholder="Nombre comercial">
                </div>
              </div>

              <div class="auth-field">
                <label for="ownerName">Nombre del propietario</label>
                <div class="auth-input-wrap">
                  <span>N</span>
                  <input id="ownerName" type="text" name="nombre_propietario" autocomplete="name" required value="<?php echo e(old('nombre_propietario')); ?>" placeholder="Responsable del comercio">
                </div>
              </div>

              <div class="auth-field">
                <label for="storeCorreo">Correo electrónico</label>
                <div class="auth-input-wrap">
                  <span>@</span>
                  <input id="storeCorreo" type="email" name="correo" autocomplete="email" required value="<?php echo e(old('correo')); ?>" placeholder="negocio@email.com">
                </div>
              </div>

              <div class="auth-field">
                <label for="storeTelefono">Teléfono</label>
                <div class="auth-input-wrap">
                  <span>#</span>
                  <input id="storeTelefono" type="tel" name="telefono" autocomplete="tel" value="<?php echo e(old('telefono')); ?>" placeholder="5555-5555">
                </div>
              </div>

              <div class="auth-field auth-field-wide">
                <label for="storeUbicacion">Ubicación del comercio</label>
                <div class="auth-input-wrap">
                  <span>U</span>
                  <input id="storeUbicacion" type="text" name="ubicacion" value="<?php echo e(old('ubicacion')); ?>" placeholder="Dirección o punto de referencia">
                </div>
              </div>

              <div class="auth-field auth-field-wide">
                <label for="storeDescripcion">Descripción del comercio</label>
                <textarea id="storeDescripcion" name="descripcion" placeholder="Contá qué vendés, horarios o detalles importantes"><?php echo e(old('descripcion')); ?></textarea>
              </div>

              <div class="auth-field">
                <label for="storePassword">Contraseña</label>
                <div class="auth-input-wrap">
                  <span>*</span>
                  <input id="storePassword" type="password" name="contraseña" autocomplete="new-password" required placeholder="Mínimo 6 caracteres">
                </div>
              </div>

              <div class="auth-field">
                <label for="storePasswordConfirm">Confirmar contraseña</label>
                <div class="auth-input-wrap">
                  <span>*</span>
                  <input id="storePasswordConfirm" type="password" name="contraseña_confirmation" autocomplete="new-password" required placeholder="Repite tu contraseña">
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block auth-submit">Registrar comercio</button>
          </form>
        </div>
      </div>
    </div>
  </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  const authTabTitles = {
    login: 'Iniciar sesión',
    register: 'Registro de comprador',
    store: 'Registro de comercio'
  };

  function switchTab(tab) {
    const selectedTab = ['login', 'register', 'store'].includes(tab) ? tab : 'login';

    ['login', 'register', 'store'].forEach(name => {
      const panel = document.getElementById(name + 'Tab');
      const button = document.getElementById('btn' + name.charAt(0).toUpperCase() + name.slice(1) + 'Tab');
      const isActive = name === selectedTab;

      if (panel && button) {
        panel.classList.toggle('active', isActive);
        button.classList.toggle('active', isActive);
        button.setAttribute('aria-selected', isActive ? 'true' : 'false');
      }
    });

    const title = document.getElementById('authTitle');
    if (title) {
      title.textContent = authTabTitles[selectedTab];
    }

    const url = new URL(window.location.href);
    if (selectedTab === 'login') {
      url.searchParams.delete('tab');
    } else {
      url.searchParams.set('tab', selectedTab);
    }
    window.history.replaceState({}, '', url);
  }

  document.addEventListener('DOMContentLoaded', function() {
    const queryTab = new URLSearchParams(window.location.search).get('tab');
    const sessionTab = <?php echo json_encode(session('tab'), 15, 512) ?>;
    switchTab(queryTab || sessionTab || 'login');
    updateUserDisplay();
    updateCartBadge();
  });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Desktop\casdq\ProjectMarket\market\resources\views/registro.blade.php ENDPATH**/ ?>