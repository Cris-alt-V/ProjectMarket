@extends('layouts.app')

@section('title', 'Carrito de Compras - Marketplace Local')

@section('content')
<div class="cart-page-wrapper">
  <!-- Header del Carrito -->
  <div class="cart-header">
    <div class="cart-header-content">
      <h1>🛒 Mi Carrito de Compras</h1>
      <p id="itemCountDisplay">Cargando...</p>
    </div>
  </div>

  <!-- Contenedor Principal -->
  <div class="cart-main-container">
    <!-- Items del Carrito -->
    <div class="cart-items-section" id="cartItemsContainer"></div>

    <!-- Resumen del Carrito -->
    <aside class="cart-summary-sidebar" id="summaryContainer"></aside>
  </div>
</div>

<style>
/* ======================== CART PAGE WRAPPER ======================== */
.cart-page-wrapper {
  min-height: calc(100vh - 100px);
  background: #f8f9fb;
  padding: 40px 20px;
}

.cart-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 40px 20px;
  border-radius: 12px;
  margin-bottom: 40px;
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

.cart-header-content {
  max-width: 1200px;
  margin: 0 auto;
}

.cart-header h1 {
  font-size: 32px;
  margin: 0 0 10px 0;
  font-weight: 700;
}

.cart-header p {
  margin: 0;
  opacity: 0.9;
  font-size: 16px;
}

/* ======================== MAIN CONTAINER ======================== */
.cart-main-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 40px;
}

.cart-items-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* ======================== EMPTY CART ======================== */
.empty-cart-container {
  background: white;
  border-radius: 12px;
  padding: 80px 40px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  animation: slideUp 0.5s ease-out;
}

.empty-cart-icon {
  font-size: 80px;
  margin-bottom: 20px;
  opacity: 0.7;
}

.empty-cart-title {
  font-size: 24px;
  color: #1a1a2e;
  font-weight: 700;
  margin-bottom: 10px;
}

.empty-cart-text {
  color: #666;
  font-size: 16px;
  margin-bottom: 30px;
  line-height: 1.6;
}

.empty-cart-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 14px 40px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.empty-cart-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* ======================== CART ITEM ======================== */
.cart-item-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  display: grid;
  grid-template-columns: 120px 1fr auto;
  gap: 20px;
  align-items: center;
  transition: all 0.3s ease;
  animation: slideUp 0.5s ease-out;
  border: 2px solid transparent;
}

.cart-item-card:hover {
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  border-color: #667eea;
}

.cart-item-image-wrapper {
  position: relative;
  width: 120px;
  height: 120px;
  border-radius: 8px;
  overflow: hidden;
  background: #f5f5f5;
}

.cart-item-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.cart-item-card:hover .cart-item-image {
  transform: scale(1.05);
}

.cart-item-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.cart-item-name {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a2e;
  margin: 0;
}

.cart-item-description {
  font-size: 13px;
  color: #999;
  margin: 0;
  line-height: 1.5;
}

.cart-item-meta {
  display: flex;
  gap: 15px;
  font-size: 13px;
  margin-top: 8px;
}

.cart-item-meta span {
  color: #666;
}

.cart-item-meta strong {
  color: #1a1a2e;
}

.cart-item-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* ======================== QUANTITY SPINNER ======================== */
.quantity-spinner {
  display: flex;
  align-items: center;
  border: 2px solid #e8e8f0;
  border-radius: 8px;
  background: #f9f9fb;
  overflow: hidden;
}

.qty-btn {
  background: transparent;
  border: none;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 16px;
  color: #667eea;
  transition: all 0.2s ease;
  font-weight: bold;
}

.qty-btn:hover {
  background: #667eea;
  color: white;
}

.qty-input {
  border: none;
  background: transparent;
  width: 50px;
  text-align: center;
  font-weight: 600;
  font-size: 14px;
  color: #1a1a2e;
  outline: none;
}

.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* ======================== REMOVE BUTTON ======================== */
.remove-btn {
  background: transparent;
  border: 2px solid #ff6b6b;
  color: #ff6b6b;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.remove-btn:hover {
  background: #ff6b6b;
  color: white;
  transform: translateY(-2px);
}

/* ======================== CART SUMMARY SIDEBAR ======================== */
.cart-summary-sidebar {
  background: white;
  border-radius: 12px;
  padding: 30px;
  height: fit-content;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  position: sticky;
  top: 110px;
  animation: slideUp 0.5s ease-out 0.1s both;
}

.summary-title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a2e;
  margin: 0 0 20px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #e8e8f0;
  font-size: 14px;
  color: #666;
}

.summary-row strong {
  color: #1a1a2e;
}

.summary-row.total {
  border-bottom: 2px solid #667eea;
  border-top: 2px solid #667eea;
  padding: 16px 0;
  margin-top: 12px;
  font-size: 18px;
  font-weight: 700;
  color: #1a1a2e;
}

.summary-row.total span:last-child {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.summary-info {
  font-size: 12px;
  color: #999;
  padding: 12px 0;
  text-align: center;
  border-bottom: 1px solid #e8e8f0;
}

.checkout-btn {
  width: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 16px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  margin-top: 20px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.checkout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.checkout-btn:active {
  transform: translateY(0);
}

.continue-shopping-btn {
  width: 100%;
  background: #f0f0f5;
  color: #667eea;
  border: 2px solid #667eea;
  padding: 12px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 10px;
  transition: all 0.3s ease;
}

.continue-shopping-btn:hover {
  background: #667eea;
  color: white;
}

/* ======================== ANIMATIONS ======================== */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideOut {
  from {
    opacity: 1;
    transform: translateX(0);
  }
  to {
    opacity: 0;
    transform: translateX(100%);
  }
}

.item-removing {
  animation: slideOut 0.3s ease-out forwards;
}

/* ======================== RESPONSIVE ======================== */
@media (max-width: 900px) {
  .cart-main-container {
    grid-template-columns: 1fr;
  }

  .cart-summary-sidebar {
    position: static;
    top: auto;
  }

  .cart-item-card {
    grid-template-columns: 100px 1fr auto;
  }

  .cart-item-image-wrapper {
    width: 100px;
    height: 100px;
  }
}

@media (max-width: 600px) {
  .cart-page-wrapper {
    padding: 20px 15px;
  }

  .cart-header {
    padding: 25px 15px;
    margin-bottom: 25px;
  }

  .cart-header h1 {
    font-size: 24px;
  }

  .cart-item-card {
    grid-template-columns: 80px 1fr;
    gap: 15px;
    padding: 15px;
  }

  .cart-item-image-wrapper {
    width: 80px;
    height: 80px;
  }

  .cart-item-actions {
    grid-column: 1 / -1;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e8e8f0;
  }

  .cart-summary-sidebar {
    padding: 20px;
  }

  .remove-btn {
    padding: 6px 12px;
    font-size: 12px;
  }
}
</style>
@endsection

@push('scripts')
<script>
  function renderCart() {
    const cart = MarketplaceApp.getCart();
    const container = document.getElementById('cartItemsContainer');
    const itemCountDisplay = document.getElementById('itemCountDisplay');
    const summary = document.getElementById('summaryContainer');

    // Mostrar cantidad de items
    const itemCount = cart.reduce((sum, item) => sum + item.cantidad, 0);
    itemCountDisplay.textContent = itemCount > 0 ? `${itemCount} artículo${itemCount !== 1 ? 's' : ''} en tu carrito` : 'Carrito vacío';

    if (!cart.length) {
      container.innerHTML = `
        <div class="empty-cart-container">
          <div class="empty-cart-icon">🛒</div>
          <h2 class="empty-cart-title">Tu carrito está vacío</h2>
          <p class="empty-cart-text">¡Haz que sea especial! Explora nuestros productos y agrega algo que te guste.</p>
          <button class="empty-cart-btn" onclick="window.location.href='/productos'">Explorar Productos</button>
        </div>
      `;
      summary.innerHTML = '';
      return;
    }

    container.innerHTML = cart.map(item => `
      <div class="cart-item-card" id="cart-item-${item.id}">
        <div class="cart-item-image-wrapper">
          <img src="${item.foto}" alt="${item.nombre}" class="cart-item-image" onerror="this.onerror=null;this.src='/imagenes/blusa.png';">
        </div>
        <div class="cart-item-details">
          <h3 class="cart-item-name">${item.nombre}</h3>
          <p class="cart-item-description">${item.descripcion}</p>
          <div class="cart-item-meta">
            <span>Precio unitario: <strong>$${item.precio.toFixed(2)}</strong></span>
            <span>Subtotal: <strong>$${(item.precio * item.cantidad).toFixed(2)}</strong></span>
          </div>
        </div>
        <div class="cart-item-actions">
          <div class="quantity-spinner">
            <button class="qty-btn" onclick="decreaseQuantity(${item.id})">−</button>
            <input type="number" class="qty-input" value="${item.cantidad}" onchange="updateCartQuantity(${item.id}, this.value)" min="1">
            <button class="qty-btn" onclick="increaseQuantity(${item.id})">+</button>
          </div>
          <button class="remove-btn" onclick="removeCartItem(${item.id})">✕ Eliminar</button>
        </div>
      </div>
    `).join('');

    const subtotal = cart.reduce((sum, item) => sum + item.precio * item.cantidad, 0);
    const taxes = subtotal * 0.12;
    const total = subtotal + taxes;

    summary.innerHTML = `
      <div class="summary-title">📋 Resumen</div>
      <div class="summary-row">
        <span>Subtotal</span>
        <strong>$${subtotal.toFixed(2)}</strong>
      </div>
      <div class="summary-row">
        <span>Impuestos (12%)</span>
        <strong>$${taxes.toFixed(2)}</strong>
      </div>
      <div class="summary-row total">
        <span>Total</span>
        <span>$${total.toFixed(2)}</span>
      </div>
      <div class="summary-info">
        ✓ Envío gratis en ordenes mayores a $50
      </div>
      <button class="checkout-btn" onclick="checkout()">Proceder al Pago</button>
      <button class="continue-shopping-btn" onclick="window.location.href='/productos'">Seguir Comprando</button>
    `;
  }

  function increaseQuantity(productId) {
    const cart = MarketplaceApp.getCart();
    const item = cart.find(product => product.id === productId);
    if (item) {
      item.cantidad++;
      MarketplaceApp.setCart(cart);
      renderCart();
      updateCartBadge();
    }
  }

  function decreaseQuantity(productId) {
    const cart = MarketplaceApp.getCart();
    const item = cart.find(product => product.id === productId);
    if (item && item.cantidad > 1) {
      item.cantidad--;
      MarketplaceApp.setCart(cart);
      renderCart();
      updateCartBadge();
    }
  }

  function updateCartQuantity(productId, quantity) {
    const cart = MarketplaceApp.getCart();
    const item = cart.find(product => product.id === parseInt(productId, 10));
    if (item) {
      const newQuantity = Math.max(1, parseInt(quantity, 10) || 1);
      item.cantidad = newQuantity;
      MarketplaceApp.setCart(cart);
      renderCart();
      updateCartBadge();
    }
  }

  function removeCartItem(productId) {
    const itemElement = document.getElementById(`cart-item-${productId}`);
    if (itemElement) {
      itemElement.classList.add('item-removing');
      setTimeout(() => {
        const cart = MarketplaceApp.getCart().filter(item => item.id !== parseInt(productId, 10));
        MarketplaceApp.setCart(cart);
        renderCart();
        updateCartBadge();
        MarketplaceApp.showNotification('Producto eliminado del carrito');
      }, 300);
    }
  }

  function checkout() {
    const user = MarketplaceApp.getCurrentUser();
    if (!user) {
      MarketplaceApp.showNotification('Debes iniciar sesión para continuar', 'error');
      setTimeout(() => {
        window.location.href = '/registro';
      }, 1500);
      return;
    }
    MarketplaceApp.showNotification('¡Pedido procesado! Gracias por tu compra 🎉');
    setTimeout(() => {
      MarketplaceApp.setCart([]);
      renderCart();
      updateCartBadge();
      window.location.href = '/';
    }, 2000);
  }

  document.addEventListener('DOMContentLoaded', function() {
    renderCart();
    updateUserDisplay();
    updateCartBadge();
  });
</script>
@endpush
