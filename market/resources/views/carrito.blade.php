@extends('layouts.app')

@section('title', 'Carrito de Compras - Marketplace Local')

@section('content')
  <section class="cart-page">
    <h1 class="cart-title">Carrito de Compras</h1>

    <div class="cart-container">
      <div class="cart-main">
        <div class="cart-items" id="cartItemsContainer"></div>
        <div id="couponContainer"></div>
      </div>
      <aside class="summary" id="summaryContainer"></aside>
    </div>
    <div id="paymentModal" class="payment-modal hidden"></div>
  </section>
@endsection

@push('scripts')
<script>
  const PROMO_CODES = [
    { code: 'Rata 40400', discount: 0.13 },
    { code: 'Aristo Social', discount: 0.10 },
    { code: 'Local 2026', discount: 0.08 }
  ];

  let paymentStepActive = false;
  let selectedPaymentMethod = 'efectivo';

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
          <div style="margin-top:10px; font-size:0.95em; color:#444;">
            Existencias: ${item.stock > 0 ? item.stock : 'sin existencias'}
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
    if (!promo) {
      const promoInput = document.getElementById('promoCodeInput');
      if (promoInput) {
        promoInput.value = '';
      }
    }

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
      const availableStock = MarketplaceApp.getAvailableStock(item.id);
      const nextQuantity = Math.min(Math.max(1, currentQuantity + delta), availableStock);
      if (nextQuantity !== currentQuantity + delta) {
        MarketplaceApp.showNotification(`Solo hay ${availableStock} unidades disponibles.`, 'error');
      }
      item.quantity = nextQuantity;
      MarketplaceApp.setCart(cart);
      renderCart();
      updateCartBadge();
    }
  }

  function updateCartQuantity(productId, quantity) {
    const cart = MarketplaceApp.getCart();
    const item = cart.find(product => product.id === parseInt(productId, 10));

    if (item) {
      const requestedQuantity = Math.max(1, parseInt(quantity, 10) || 1);
      const availableStock = MarketplaceApp.getAvailableStock(item.id);
      item.quantity = Math.min(requestedQuantity, availableStock);
      if (requestedQuantity > availableStock) {
        MarketplaceApp.showNotification(`Solo quedan ${availableStock} unidades disponibles.`, 'error');
      }
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

  function clearCouponInput() {
    const promoInput = document.getElementById('promoCodeInput');
    if (promoInput) {
      promoInput.value = '';
    }
  }

  function updatePaymentMethod(method) {
    selectedPaymentMethod = method;
    const modal = document.getElementById('paymentModal');
    if (!modal || modal.classList.contains('hidden')) {
      return;
    }
    modal.innerHTML = renderPaymentModal();
  }

  function renderPaymentModal() {
    const user = MarketplaceApp.getCurrentUser() || {};
    const paymentName = user.nombre || '';
    const paymentEmail = user.email || '';
    const paymentPhone = user.telefono || '';
    const paymentAddress = user.direccion || '';
    return `
      <div class="payment-modal-overlay" onclick="closePaymentModal()">
        <div class="payment-modal-content" role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle" onclick="event.stopPropagation()">
          <button class="payment-modal-close" type="button" onclick="closePaymentModal()" aria-label="Cerrar formulario de pago">×</button>
          <div class="payment-modal-header">
            <h2 id="paymentModalTitle">Confirmar Pago</h2>
            <p>Completá tus datos antes de finalizar la compra.</p>
          </div>
          <div class="payment-field">
            <label for="paymentName">Nombre completo</label>
            <input type="text" id="paymentName" value="${paymentName}" placeholder="Nombre y apellido">
          </div>
          <div class="payment-field">
            <label for="paymentEmail">Correo electrónico</label>
            <input type="email" id="paymentEmail" value="${paymentEmail}" placeholder="correo@ejemplo.com">
          </div>
          <div class="payment-field">
            <label for="paymentPhone">Teléfono</label>
            <input type="text" id="paymentPhone" value="${paymentPhone}" placeholder="Ej. 0412-1234567">
          </div>
          <div class="payment-field">
            <label for="paymentAddress">Dirección de entrega</label>
            <input type="text" id="paymentAddress" value="${paymentAddress}" placeholder="Calle, número, ciudad">
          </div>
          <div class="payment-field">
            <label>Método de pago</label>
            <div class="payment-options">
              <label><input type="radio" name="paymentMethod" value="tarjeta" ${selectedPaymentMethod === 'tarjeta' ? 'checked' : ''} onchange="updatePaymentMethod('tarjeta')"><span>Tarjeta</span></label>
              <label><input type="radio" name="paymentMethod" value="efectivo" ${selectedPaymentMethod === 'efectivo' ? 'checked' : ''} onchange="updatePaymentMethod('efectivo')"><span>Efectivo al recibir el paquete</span></label>
            </div>
          </div>
          ${selectedPaymentMethod === 'tarjeta' ? `
            <div class="payment-field">
              <label for="cardNumber">Número de tarjeta</label>
              <input type="text" id="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19">
            </div>
            <div class="payment-field two-columns">
              <div>
                <label for="cardExpiry">Fecha de vencimiento</label>
                <input type="text" id="cardExpiry" placeholder="MM/AA" maxlength="5">
              </div>
              <div>
                <label for="cardCvv">CVV</label>
                <input type="text" id="cardCvv" placeholder="123" maxlength="4">
              </div>
            </div>
          ` : `
            <div class="payment-note">
              <p>Pagarás en efectivo al recibir el paquete. No se requiere tarjeta.</p>
            </div>
          `}
          <p class="payment-feedback" id="paymentFeedback" aria-live="polite"></p>
          <div class="payment-actions">
            <button class="btn btn-primary btn-block checkout-btn" onclick="confirmPurchase()">Confirmar Compra</button>
            <button class="btn btn-secondary btn-block continue-btn" onclick="closePaymentModal()">Volver al carrito</button>
          </div>
        </div>
      </div>
    `;
  }

  function validatePaymentForm() {
    const name = document.getElementById('paymentName')?.value.trim();
    const email = document.getElementById('paymentEmail')?.value.trim();
    const phone = document.getElementById('paymentPhone')?.value.trim();
    const address = document.getElementById('paymentAddress')?.value.trim();
    const feedback = document.getElementById('paymentFeedback');

    if (!name || !email || !phone || !address) {
      if (feedback) {
        feedback.textContent = 'Por favor completá todos los datos de envío y contacto.';
      }
      return null;
    }

    if (selectedPaymentMethod === 'tarjeta') {
      const cardNumber = document.getElementById('cardNumber')?.value.trim();
      const cardExpiry = document.getElementById('cardExpiry')?.value.trim();
      const cardCvv = document.getElementById('cardCvv')?.value.trim();
      if (!cardNumber || !cardExpiry || !cardCvv) {
        if (feedback) {
          feedback.textContent = 'Completá los datos de la tarjeta para continuar.';
        }
        return null;
      }
      return {
        name,
        email,
        phone,
        address,
        paymentMethod: selectedPaymentMethod,
        cardNumber,
        cardExpiry,
        cardCvv,
      };
    }

    return {
      name,
      email,
      phone,
      address,
      paymentMethod: selectedPaymentMethod,
    };
  }

  function checkout() {
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

    selectedPaymentMethod = 'efectivo';
    openPaymentModal();
  }

  function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (!modal) {
      return;
    }
    modal.innerHTML = renderPaymentModal();
    modal.classList.remove('hidden');
    const firstInput = modal.querySelector('#paymentName');
    if (firstInput) {
      firstInput.focus();
    }
  }

  function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (!modal) {
      return;
    }
    modal.classList.add('hidden');
    modal.innerHTML = '';
    selectedPaymentMethod = 'efectivo';
  }

  function clearActivePromo() {
    const storageKey = getPromoStorageKey();
    localStorage.removeItem(storageKey);
  }

  function confirmPurchase() {
    const cart = MarketplaceApp.getCart();
    const user = MarketplaceApp.getCurrentUser();
    const paymentData = validatePaymentForm();

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

    if (!paymentData) {
      return;
    }

    const promo = getActivePromo();
    if (promo) {
      markPromoAsUsed(promo.code);
      clearActivePromo();
    }

    MarketplaceApp.decrementStockForCart(cart);
    MarketplaceApp.setCart([]);
    clearCouponInput();
    closePaymentModal();
    paymentStepActive = false;
    selectedPaymentMethod = 'efectivo';

    MarketplaceApp.showNotification('¡Compra realizada exitosamente! 🎉', 'success');
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
@endpush
