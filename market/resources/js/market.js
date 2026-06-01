const MarketplaceApp = {
  categorias: [
    { nombre: 'General', icono: '📦' },
    { nombre: 'Accesorios', icono: '👜' },
    { nombre: 'Ropa', icono: '👗' },
    { nombre: 'Alimentos', icono: '🍎' },
    { nombre: 'Electrónica', icono: '💻' },
    { nombre: 'Hogar', icono: '🏠' },
    { nombre: 'Belleza', icono: '💄' },
  ],
  comercios: [],
  productos: [],
  api(path, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const defaultOptions = {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
      },
    };

    if (csrfToken) {
      defaultOptions.headers['X-CSRF-TOKEN'] = csrfToken;
    }

    const mergedOptions = {
      ...defaultOptions,
      ...options,
      headers: {
        ...defaultOptions.headers,
        ...(options.headers || {}),
      },
    };

    return fetch(path, mergedOptions).then(async (response) => {
      const contentType = response.headers.get('content-type');
      const data = contentType?.includes('application/json') ? await response.json() : null;
      if (!response.ok) {
        const error = new Error(data?.message || 'Request failed');
        error.response = response;
        error.data = data;
        throw error;
      }
      return data;
    });
  },

  getCurrentUser() {
    try {
      const raw = localStorage.getItem('marketplaceUser');
      return raw ? JSON.parse(raw) : null;
    } catch (error) {
      console.error('Unable to read current user from localStorage', error);
      return null;
    }
  },

  getStockAdjustmentsKey() {
    return 'marketplaceStockAdjustments';
  },

  getStockAdjustments() {
    try {
      const raw = localStorage.getItem(this.getStockAdjustmentsKey());
      return raw ? JSON.parse(raw) : {};
    } catch (error) {
      console.error('Unable to read stock adjustments', error);
      return {};
    }
  },

  setStockAdjustments(adjustments) {
    try {
      localStorage.setItem(this.getStockAdjustmentsKey(), JSON.stringify(adjustments));
    } catch (error) {
      console.error('Unable to save stock adjustments', error);
    }
  },

  getAvailableStock(productId) {
    const product = this.getProductById(productId);
    if (!product) {
      return 0;
    }
    return Number(product.stock || 0);
  },

  applyStockAdjustments(products) {
    const adjustments = this.getStockAdjustments();
    products.forEach(product => {
      const adjustment = Number(adjustments[product.id] || 0);
      product.stock = Math.max(0, Number(product.stock || 0) - adjustment);
    });
  },

  adjustStock(productId, quantity) {
    const adjustments = this.getStockAdjustments();
    adjustments[productId] = Math.max(0, Number(adjustments[productId] || 0) + Number(quantity));
    if (adjustments[productId] <= 0) {
      delete adjustments[productId];
    }
    this.setStockAdjustments(adjustments);
  },

  setCurrentUser(user) {
    try {
      if (user) {
        localStorage.setItem('marketplaceUser', JSON.stringify(user));
      } else {
        localStorage.removeItem('marketplaceUser');
      }
    } catch (error) {
      console.error('Unable to save current user to localStorage', error);
    }
  },

  clearCurrentUser() {
    try {
      localStorage.removeItem('marketplaceUser');
    } catch (error) {
      console.error('Unable to clear current user from localStorage', error);
    }
  },

  syncCurrentUser() {
    return Promise.resolve(this.getCurrentUser());
  },

  getCartKey() {
    const user = this.getCurrentUser();
    return user ? `cart_${user.id_usuario}` : 'cart_guest';
  },

  isAuthenticated() {
    return Boolean(this.getCurrentUser());
  },

  getCart() {
    try {
      const user = this.getCurrentUser();
      if (!user) {
        // Si no hay usuario, devolver carrito vacío (guest)
        const raw = localStorage.getItem('cart_guest');
        return raw ? JSON.parse(raw) : [];
      }
      // Obtener carrito específico del usuario
      const cartKey = this.getCartKey();
      const raw = localStorage.getItem(cartKey);
      return raw ? JSON.parse(raw) : [];
    } catch (error) {
      console.error('Unable to read cart from localStorage', error);
      return [];
    }
  },

  setCart(cartItems) {
    try {
      const cartKey = this.getCartKey();
      localStorage.setItem(cartKey, JSON.stringify(cartItems));
      this.updateCartBadge();
    } catch (error) {
      console.error('Unable to save cart to localStorage', error);
    }
  },

  clearCart() {
    try {
      const cartKey = this.getCartKey();
      localStorage.removeItem(cartKey);
      this.updateCartBadge();
    } catch (error) {
      console.error('Unable to clear cart from localStorage', error);
    }
  },

  addToCart(product, quantity = 1) {
    if (!this.isAuthenticated()) {
      this.showNotification('Debes iniciar sesión para agregar productos al carrito.', 'error');
      setTimeout(() => {
        window.location.href = '/registro';
      }, 1500);
      return;
    }

    const cart = this.getCart();
    const existing = cart.find((item) => item.id === product.id);
    const currentQuantity = existing ? Number(existing.quantity || 0) : 0;

    let availableStock;
    const productFromApp = this.getProductById(product.id);
    if (productFromApp) {
      availableStock = this.getAvailableStock(product.id);
    } else {
      const adjustments = this.getStockAdjustments();
      availableStock = Math.max(0, Number(product.stock || 0) - Number(adjustments[product.id] || 0));
    }

    if (availableStock <= 0) {
      this.showNotification('No hay existencias disponibles para este producto.', 'error');
      return;
    }

    if (currentQuantity + quantity > availableStock) {
      this.showNotification(`Solo quedan ${availableStock} unidades disponibles. Ajustá la cantidad.`, 'error');
      return;
    }

    if (existing) {
      existing.quantity = currentQuantity + quantity;
    } else {
      cart.push({
        ...product,
        quantity: quantity,
      });
    }

    this.setCart(cart);
    const mensaje = quantity > 1 ? `${quantity} unidades agregadas al carrito` : 'Producto agregado al carrito';
    this.showNotification(mensaje, 'success');
  },

  getComercioById(id) {
    return this.comercios?.find((store) => Number(store.id) === Number(id)) || null;
  },

  getProductById(id) {
    return this.productos?.find((product) => Number(product.id) === Number(id)) || null;
  },

  updateCartBadge() {
    const badge = document.getElementById('cartBadge');
    const cart = this.getCart();
    const totalItems = cart.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
    if (badge) {
      badge.textContent = totalItems;
      badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
    }
  },

  decrementStockForCart(cartItems) {
    cartItems.forEach((item) => {
      const product = this.getProductById(item.id);
      if (!product) {
        return;
      }
      const purchasedQuantity = Number(item.quantity || 0);
      const availableStock = Number(product.stock || 0);
      product.stock = Math.max(0, availableStock - purchasedQuantity);
      this.adjustStock(item.id, purchasedQuantity);
    });
  },

  updateUserDisplay() {
    const user = window.currentSessionUser || this.getCurrentUser();
    const userNameElement = document.getElementById('userName');
    const userDisplayElement = document.getElementById('userDisplay');

    if (!userNameElement || !userDisplayElement) {
      return;
    }

    if (user) {
      userNameElement.textContent = user.nombre || user.email || 'Usuario';
      userDisplayElement.textContent = '👤';
    } else {
      userNameElement.textContent = 'Iniciar';
      userDisplayElement.textContent = '👤';
    }
  },

  showNotification(message, type = 'info') {
    if (!message) {
      return;
    }
    
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    
    // Agregar clase de tipo si existe
    if (type === 'success') {
      notification.style.background = '#4caf50';
    } else if (type === 'error') {
      notification.style.background = '#f44336';
    } else if (type === 'info') {
      notification.style.background = '#2196f3';
    }
    
    document.body.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => {
      notification.classList.add('show');
    }, 10);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 3000);
  },
};

window.goToProductDetail = function (productId) {
  if (!productId) {
    return;
  }
  window.location.href = `/producto/${productId}`;
};

window.goToStore = function (storeId) {
  if (!storeId) {
    return;
  }
  window.location.href = `/tienda?id=${storeId}`;
};

window.goToCart = function () {
  window.location.href = '/carrito';
};

window.search = function () {
  const searchInput = document.querySelector('#searchInput');
  if (!searchInput) {
    return;
  }
  const query = searchInput.value.trim().toLowerCase();
  if (!query) {
    return;
  }

  const searchPath = `/productos?search=${encodeURIComponent(query)}`;
  window.location.href = searchPath;
};

window.toggleUserMenu = function () {
  const menu = document.getElementById('userMenu');
  if (!menu) {
    return;
  }
  menu.classList.toggle('active');
};

window.addToCart = function (product, quantity = 1) {
  if (!product || !product.id || !window.MarketplaceApp || typeof MarketplaceApp.addToCart !== 'function') {
    return;
  }
  MarketplaceApp.addToCart(product, Number(quantity) || 1);
};

window.addToCartById = function (productId, quantity = 1) {
  if (!window.MarketplaceApp || typeof MarketplaceApp.getProductById !== 'function') {
    return;
  }
  if (!MarketplaceApp.isAuthenticated()) {
    MarketplaceApp.showNotification('Debes iniciar sesión para agregar productos al carrito.', 'error');
    setTimeout(() => {
      window.location.href = '/registro';
    }, 1500);
    return;
  }
  const product = MarketplaceApp.getProductById(productId);
  if (!product) {
    console.warn('No se encontró el producto para añadir al carrito:', productId);
    return;
  }
  MarketplaceApp.addToCart(product, Number(quantity) || 1);
};

window.logout = async function () {
  try {
    await MarketplaceApp.api('/auth/logout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
    });
  } catch (error) {
    console.warn('Logout API request failed, falling back to form submit.', error);
  } finally {
    MarketplaceApp.clearCurrentUser();
    window.location.href = '/auth/logout';
  }
};

window.addEventListener('DOMContentLoaded', () => {
  MarketplaceApp.updateCartBadge();
  MarketplaceApp.updateUserDisplay();

  const logoutForm = document.getElementById('logoutForm');
  if (logoutForm) {
    logoutForm.addEventListener('submit', () => {
      MarketplaceApp.clearCurrentUser();
    });
  }

  document.addEventListener('click', (event) => {
    const menu = document.getElementById('userMenu');
    const userInfo = document.querySelector('.user-info');
    if (menu && userInfo && !menu.contains(event.target) && !userInfo.contains(event.target)) {
      menu.classList.remove('active');
    }
  });
});

// Expose small compatibility helpers used by blade views
window.updateCartBadge = function() { return MarketplaceApp.updateCartBadge(); };
window.updateUserDisplay = function() { return MarketplaceApp.updateUserDisplay(); };
window.showNotification = function(msg) { return MarketplaceApp.showNotification(msg); };

// Expose the app object for legacy inline scripts
window.MarketplaceApp = MarketplaceApp;
