<?php $__env->startSection('title', 'Carrito de Compras - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <section class="cart-page">
    <h1 class="cart-title">Carrito de Compras</h1>

    <div class="cart-container">
      <div class="cart-main">
        <div class="cart-items" id="cartItemsContainer"></div>
        <div id="couponContainer"></div>
      </div>
      <aside class="summary" id="summaryContainer"></aside>
    </div>
  </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  const PROMO_CODES = [
    { code: 'Rata 40400', discount: 0.13 },
    { code: 'Aristo Social', discount: 0.10 },
    { code: 'Local 2026', discount: 0.08 }
  ];

  function getPromoStorageKey() {
    const user = MarketplaceApp.getCurrentUser();
    return user ? `promo_active_${user.id_usuario}` : 'promo_active_guest';
  }

  function getUsedPromosKey() {
    const user = MarketplaceApp.getCurrentUser();
    return user ? `promo_used_${user.id_usuario}` : 'promo_used_guest';
  }

  function getUsedPromos() {
    try {
      const used = localStorage.getItem(getUsedPromosKey());
      return used ? JSON.parse(used) : [];
    } catch (error) {
      return [];
    }
  }

  function isPromoAlreadyUsed(promoCode) {
    const usedPromos = getUsedPromos();
    return usedPromos.includes(promoCode.toLowerCase());
  }

  function markPromoAsUsed(promoCode) {
    const usedPromos = getUsedPromos();
    const cleanCode = promoCode.toLowerCase();
    if (!usedPromos.includes(cleanCode)) {
      usedPromos.push(cleanCode);
      localStorage.setItem(getUsedPromosKey(), JSON.stringify(usedPromos));
    }
  }

  function getActivePromo() {
    localStorage.removeItem('marketplacePromoCode');
    const storageKey = getPromoStorageKey();
    const savedPromo = localStorage.getItem(storageKey);

    if (savedPromo) {
      try {
        return JSON.parse(savedPromo);
      } catch (error) {
        localStorage.removeItem(storageKey);
      }
    }

    return null;
  }

  function findPromoByCode(code) {
    const cleanCode = code.trim().toLowerCase();
    return PROMO_CODES.find(promo => promo.code.toLowerCase() === cleanCode) || null;
  }

  function formatCurrency(value) {
    return `$${value.toFixed(2)}`;
  }

  function getCartItemQuantity(item) {
    return Number(item.quantity ?? item.cantidad ?? 1);
  }

  function renderCart() {
    const cart = MarketplaceApp.getCart();
    const container = document.getElementById('cartItemsContainer');
    const couponContainer = document.getElementById('couponContainer');
    const summary = document.getElementById('summaryContainer');

    if (!cart.length) {
      container.innerHTML = `
        <div class="empty-cart">
          <h2>Tu carrito est&aacute; vac&iacute;o.</h2>
          <p>Explora productos locales y vuelve cuando algo te guste.</p>
          <button class="btn btn-primary" onclick="window.location.href='/productos'">Explorar Productos</button>
        </div>
      `;
      couponContainer.innerHTML = '';
      summary.innerHTML = '';
      return;
    }

    container.innerHTML = cart.map(item => {
      const quantity = getCartItemQuantity(item);
      return `
      <div class="cart-item">
        <img src="${item.foto}" alt="${item.nombre}" class="cart-item-image" onerror="this.style.display='none';">
        <div class="cart-item-info">
          <h3>${item.nombre}</h3>
          <p>${item.descripcion}</p>
          <div class="cart-item-prices">
            <span>Precio unitario: <strong>${formatCurrency(item.precio)}</strong></span>
            <span>Subtotal: <strong>${formatCurrency(item.precio * quantity)}</strong></span>
          </div>
        </div>
        <div class="cart-item-actions">
          <div class="quantity-stepper" aria-label="Cantidad de ${item.nombre}">
            <button type="button" onclick="changeCartQuantity(${item.id}, -1)" aria-label="Reducir cantidad">-</button>
            <input type="number" min="1" value="${quantity}" class="quantity-input" onchange="updateCartQuantity(${item.id}, this.value)" aria-label="Cantidad">
            <button type="button" onclick="changeCartQuantity(${item.id}, 1)" aria-label="Aumentar cantidad">+</button>
          </div>
          <button class="btn cart-remove-btn" onclick="removeCartItem(${item.id})">&times; Eliminar</button>
        </div>
      </div>
    `;
    }).join('');

    const subtotal = cart.reduce((sum, item) => {
      const quantity = getCartItemQuantity(item);
      return sum + item.precio * quantity;
    }, 0);
    const promo = getActivePromo();
    const discount = promo ? subtotal * promo.discount : 0;
    const taxableSubtotal = Math.max(subtotal - discount, 0);
    const taxes = taxableSubtotal * 0.12;
    const total = taxableSubtotal + taxes;

    couponContainer.innerHTML = `
      <section class="coupon-card">
        <h2>¿Tenés un cupón?</h2>
        ${promo ? `
          <div class="coupon-applied">
            <p>Cupón <strong>${promo.code}</strong> aplicado: ${Math.round(promo.discount * 100)}% de descuento.</p>
            <button type="button" onclick="removePromoCode()">Quitar</button>
          </div>
        ` : `
          <form class="coupon-form" onsubmit="applyPromoCode(event)">
            <input type="text" id="promoCodeInput" placeholder="Ingresar código..." autocomplete="off">
            <button type="submit">Aplicar</button>
          </form>
          <p class="coupon-feedback" id="couponFeedback" aria-live="polite"></p>
        `}
      </section>
    `;

    summary.innerHTML = `
      <h2 class="summary-title">Resumen</h2>
      <div class="summary-row"><span>Subtotal</span><strong>${formatCurrency(subtotal)}</strong></div>
      ${promo ? `<div class="summary-row summary-discount"><span>Descuento (${promo.code})</span><strong>-${formatCurrency(discount)}</strong></div>` : ''}
      <div class="summary-row"><span>Impuestos (12%)</span><strong>${formatCurrency(taxes)}</strong></div>
      <div class="summary-row summary-total"><span>Total</span><strong>${formatCurrency(total)}</strong></div>
      <p class="shipping-note">Envío gratis en órdenes mayores a $50</p>
      <button class="btn btn-primary btn-block checkout-btn" onclick="checkout()">Proceder al Pago</button>
      <button class="btn btn-secondary btn-block continue-btn" onclick="window.location.href='/productos'">Seguir Comprando</button>
    `;
  }

  function changeCartQuantity(productId, delta) {
    const cart = MarketplaceApp.getCart();
    const item = cart.find(product => product.id === parseInt(productId, 10));

    if (item) {
      const currentQuantity = getCartItemQuantity(item);
      item.quantity = Math.max(1, currentQuantity + delta);
      MarketplaceApp.setCart(cart);
      renderCart();
      updateCartBadge();
    }
  }

  function updateCartQuantity(productId, quantity) {
    const cart = MarketplaceApp.getCart();
    const item = cart.find(product => product.id === parseInt(productId, 10));

    if (item) {
      item.quantity = Math.max(1, parseInt(quantity, 10) || 1);
      MarketplaceApp.setCart(cart);
      renderCart();
      updateCartBadge();
    }
  }

  function removeCartItem(productId) {
    const cart = MarketplaceApp.getCart().filter(item => item.id !== parseInt(productId, 10));
    MarketplaceApp.setCart(cart);
    renderCart();
    updateCartBadge();
  }

  function applyPromoCode(event) {
    event.preventDefault();

    const input = document.getElementById('promoCodeInput');
    const feedback = document.getElementById('couponFeedback');
    const promo = findPromoByCode(input.value);

    if (!promo) {
      feedback.textContent = 'Código no válido. Revisá el cupón anunciado e intentá de nuevo.';
      feedback.classList.add('coupon-feedback-error');
      return;
    }

    // Verificar si el cupón ya fue usado por este usuario
    if (isPromoAlreadyUsed(promo.code)) {
      feedback.textContent = 'Este cupón ya fue utilizado y no puede volver a usarse.';
      feedback.classList.add('coupon-feedback-error');
      return;
    }

    // Guardar el cupón activo para este usuario
    const storageKey = getPromoStorageKey();
    localStorage.setItem(storageKey, JSON.stringify(promo));
    MarketplaceApp.showNotification('Cupón aplicado correctamente');
    renderCart();
  }

  function removePromoCode() {
    const promo = getActivePromo();
    if (promo) {
      // Marcar como usado permanentemente
      markPromoAsUsed(promo.code);
    }
    // Remover el cupón activo
    const storageKey = getPromoStorageKey();
    localStorage.removeItem(storageKey);
    MarketplaceApp.showNotification('Cupón eliminado');
    renderCart();
  }

  function checkout() {
    // Obtener carrito y usuario para validación
    const cart = MarketplaceApp.getCart();
    const user = MarketplaceApp.getCurrentUser();
    
    if (!user) {
      MarketplaceApp.showNotification('Por favor inicia sesión para continuar', 'error');
      setTimeout(() => {
        window.location.href = '/registro';
      }, 2000);
      return;
    }
    
    if (!cart.length) {
      MarketplaceApp.showNotification('Tu carrito está vacío', 'error');
      return;
    }
    
    // Marcar cupón como usado si existe
    const promo = getActivePromo();
    if (promo) {
      markPromoAsUsed(promo.code);
    }
    
    // Limpiar el carrito
    MarketplaceApp.setCart([]);
    
    // Mostrar mensaje de confirmación
    MarketplaceApp.showNotification('¡Compra realizada exitosamente! 🎉', 'success');
    
    // Redirigir al inicio después de 2.5 segundos
    setTimeout(() => {
      window.location.href = '/';
    }, 2500);
  }

  document.addEventListener('DOMContentLoaded', function() {
    renderCart();
    updateUserDisplay();
    updateCartBadge();
  });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Desktop\asdf\ProjectMarket\market\resources\views/carrito.blade.php ENDPATH**/ ?>