<?php $__env->startSection('title', 'Carrito de Compras - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <h1 style="margin-bottom: 30px; color: #333;">🛒 Carrito de Compras</h1>

  <div class="cart-container" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <div class="cart-items" id="cartItemsContainer"></div>
    <div class="summary" id="summaryContainer"></div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  function renderCart() {
    const cart = MarketplaceApp.getCart();
    const container = document.getElementById('cartItemsContainer');
    const summary = document.getElementById('summaryContainer');

    if (!cart.length) {
      container.innerHTML = `
        <div class="empty-cart">
          <p>Tu carrito está vacío.</p>
          <button class="btn btn-primary" onclick="window.location.href='/'">Explorar Productos</button>
        </div>
      `;
      summary.innerHTML = '';
      return;
    }

    container.innerHTML = cart.map(item => `
      <div class="cart-item">
        <img src="${item.foto}" alt="${item.nombre}" class="cart-item-image" onerror="this.onerror=null;this.src='/imagenes/blusa.png';">
        <div class="cart-item-info">
          <h3>${item.nombre}</h3>
          <p>${item.descripcion}</p>
          <p>Precio: $${item.precio.toFixed(2)}</p>
        </div>
        <div class="cart-item-actions">
          <input type="number" min="1" value="${item.cantidad}" class="quantity-input" onchange="updateCartQuantity(${item.id}, this.value)">
          <button class="btn btn-secondary" onclick="removeCartItem(${item.id})">Eliminar</button>
        </div>
      </div>
    `).join('');

    const subtotal = cart.reduce((sum, item) => sum + item.precio * item.cantidad, 0);
    const taxes = subtotal * 0.12;
    const total = subtotal + taxes;

    summary.innerHTML = `
      <div class="summary-row"><span>Subtotal</span><span>$${subtotal.toFixed(2)}</span></div>
      <div class="summary-row"><span>Impuestos</span><span>$${taxes.toFixed(2)}</span></div>
      <div class="summary-row total"><span>Total</span><span>$${total.toFixed(2)}</span></div>
      <button class="btn btn-primary btn-block" onclick="checkout()">Continuar al Pago</button>
    `;
  }

  function updateCartQuantity(productId, quantity) {
    const cart = MarketplaceApp.getCart();
    const item = cart.find(product => product.id === parseInt(productId, 10));
    if (item) {
      item.cantidad = Math.max(1, parseInt(quantity, 10) || 1);
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

  function checkout() {
    MarketplaceApp.showNotification('Funcionalidad de pago en desarrollo');
  }

  document.addEventListener('DOMContentLoaded', function() {
    renderCart();
    updateUserDisplay();
    updateCartBadge();
  });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Yerovi\Desktop\programarket\ProjectMarket\market\resources\views/carrito.blade.php ENDPATH**/ ?>