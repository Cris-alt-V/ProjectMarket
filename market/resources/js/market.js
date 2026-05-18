window.MarketplaceApp = {
  defaultProductImages: {
    1: "/imagenes/plata%20collar.png",
    2: "/imagenes/bolso.png",
    3: "/imagenes/blusa.png",
    4: "/imagenes/vakero.png",
    5: "/imagenes/cafe.jpg",
    6: "/imagenes/pan.png",
    7: "/imagenes/blusa.png",
    8: "/imagenes/bolso.png",
    9: "/imagenes/plata%20collar.png",
  },

  comercios: [],
  productos: [],
  categorias: [
    { id: 1, nombre: "Accesorios", icono: "👜" },
    { id: 2, nombre: "Ropa", icono: "👕" },
    { id: 3, nombre: "Alimentos", icono: "🍽️" },
    { id: 4, nombre: "Electrónica", icono: "📱" },
    { id: 5, nombre: "Hogar", icono: "🏠" },
    { id: 6, nombre: "Belleza", icono: "💄" },
  ],

  apiBase: '/api',

  getCsrfToken: function () {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  },

  async api(path, options = {}) {
    const headers = options.headers || {};
    headers['X-CSRF-TOKEN'] = this.getCsrfToken() || '';
    headers['Accept'] = 'application/json';
    if (options.body && !(options.body instanceof FormData)) {
      headers['Content-Type'] = 'application/json';
      options.body = JSON.stringify(options.body);
    }
    const response = await fetch(`${this.apiBase}${path}`, {
      credentials: 'same-origin',
      ...options,
      headers,
    });
    return response;
  },

  loadFromStorage: function () {
    const stored = localStorage.getItem('marketplace');
    if (stored) {
      const data = JSON.parse(stored);
      this.comercios = data.comercios || this.comercios;
      this.productos = data.productos || this.productos;
    }
  },

  saveToStorage: function () {
    localStorage.setItem('marketplace', JSON.stringify({
      comercios: this.comercios,
      productos: this.productos,
    }));
  },

  getCurrentUser: function () {
    if (this.currentUser) {
      return this.currentUser;
    }
    const stored = localStorage.getItem('currentUser');
    if (!stored) {
      return null;
    }
    try {
      this.currentUser = JSON.parse(stored);
      return this.currentUser;
    } catch {
      return null;
    }
  },

  setCurrentUser: function (user) {
    if (user) {
      this.currentUser = user;
      localStorage.setItem('currentUser', JSON.stringify(user));
    } else {
      this.currentUser = null;
      localStorage.removeItem('currentUser');
    }
  },

  clearCurrentUser: function () {
    this.currentUser = null;
    localStorage.removeItem('currentUser');
  },

  async syncCurrentUser() {
    const response = await this.api('/auth/user', { method: 'GET' });
    if (response.ok) {
      const user = await response.json();
      if (user) {
        this.setCurrentUser(user);
        return user;
      }
    }
    this.clearCurrentUser();
    return null;
  },

  getCart: function () {
    return JSON.parse(localStorage.getItem('cart')) || [];
  },

  setCart: function (cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
  },

  addToCart: function (producto) {
    const cart = this.getCart();
    const existing = cart.find(item => item.id === producto.id);
    if (existing) {
      existing.cantidad++;
    } else {
      cart.push({
        ...producto,
        cantidad: 1,
      });
    }
    this.setCart(cart);
    this.showNotification('Producto agregado al carrito');
  },

  showNotification: function (message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
      notification.classList.add('show');
    }, 10);
    setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  },

  getProductById: function (id) {
    return this.productos.find(p => p.id === parseInt(id, 10));
  },

  getComercioById: function (id) {
    return this.comercios.find(c => c.id === parseInt(id, 10));
  },

  getProductosByComercio: function (comercioId) {
    return this.productos.filter(p => p.comercioId === parseInt(comercioId, 10));
  },

  setupNavigation: function () {
    const user = this.getCurrentUser();
    const accountLink = document.querySelector('[href="/micuenta"]');
    const storesLink = document.querySelector('[href="/mis-comercios"]');
    const loginLink = document.querySelector('[href="/registro"]');
    const registerLink = document.querySelector('[href="/registro?tab=register"]');
    if (user) {
      if (accountLink) {
        accountLink.textContent = `Mi Cuenta (${user.nombre})`;
      }
      if (loginLink) {
        loginLink.style.display = 'none';
      }
      if (registerLink) {
        registerLink.style.display = 'none';
      }
      if (storesLink) {
        storesLink.style.display = user.tipo_usuario === 'vendedor' ? 'block' : 'none';
      }
    } else {
      if (accountLink) {
        accountLink.textContent = 'Mi Cuenta';
      }
      if (loginLink) {
        loginLink.style.display = 'block';
      }
      if (registerLink) {
        registerLink.style.display = 'block';
      }
      if (storesLink) {
        storesLink.style.display = 'none';
      }
    }
  },

  async init() {
    await this.syncCurrentUser();
    this.loadFromStorage();
    this.saveToStorage();
    this.setupNavigation();
  },
};

window.getQueryParam = function (name) {
  const params = new URLSearchParams(window.location.search);
  return params.get(name);
};

window.goToCart = function () {
  window.location.href = '/carrito';
};

window.goToProductDetail = function (productId) {
  window.location.href = `/detalle-producto?id=${productId}`;
};

window.goToStore = function (storeId) {
  window.location.href = `/tienda?id=${storeId}`;
};

window.goToRegister = function (tab) {
  window.location.href = `/registro${tab ? '?tab=' + tab : ''}`;
};

window.search = function () {
  const query = document.getElementById('searchInput')?.value || '';
  const path = window.location.pathname;
  if (path.includes('/comercios')) {
    window.location.href = `/comercios${query ? '?search=' + encodeURIComponent(query) : ''}`;
  } else {
    window.location.href = `/productos${query ? '?search=' + encodeURIComponent(query) : ''}`;
  }
};

window.searchProducts = window.search;
window.searchStores = window.search;

window.toggleUserMenu = function () {
  const menu = document.getElementById('userMenu');
  if (menu) {
    menu.classList.toggle('active');
  }
};

window.logout = async function () {
  await MarketplaceApp.api('/auth/logout', { method: 'POST' });
  MarketplaceApp.clearCurrentUser();
  updateUserDisplay();
  MarketplaceApp.showNotification('Sesión cerrada');
};

window.updateUserDisplay = function () {
  const user = MarketplaceApp.getCurrentUser();
  const userDisplay = document.getElementById('userDisplay');
  const userName = document.getElementById('userName');
  const loginLink = document.getElementById('loginLink');
  const registerLink = document.getElementById('registerLink');
  const accountLink = document.getElementById('accountLink');
  const storesLink = document.getElementById('storesLink');
  const logoutBtn = document.getElementById('logoutBtn');

  if (user) {
    if (userDisplay) userDisplay.textContent = '👤';
    if (userName) userName.textContent = user.nombre;
    if (loginLink) loginLink.style.display = 'none';
    if (registerLink) registerLink.style.display = 'none';
    if (accountLink) accountLink.style.display = 'block';
    if (logoutBtn) logoutBtn.style.display = 'block';
    if (storesLink) storesLink.style.display = user.tipo_usuario === 'vendedor' ? 'block' : 'none';
  } else {
    if (userDisplay) userDisplay.textContent = '👤';
    if (userName) userName.textContent = 'Iniciar';
    if (loginLink) loginLink.style.display = 'block';
    if (registerLink) registerLink.style.display = 'block';
    if (accountLink) accountLink.style.display = 'none';
    if (logoutBtn) logoutBtn.style.display = 'none';
    if (storesLink) storesLink.style.display = 'none';
  }
};

window.updateCartBadge = function () {
  const cart = MarketplaceApp.getCart();
  const badge = document.getElementById('cartBadge');
  if (badge) {
    if (cart.length > 0) {
      badge.textContent = cart.length;
      badge.style.display = 'flex';
    } else {
      badge.style.display = 'none';
    }
  }
};

window.addEventListener('DOMContentLoaded', function () {
  MarketplaceApp.init();
  updateUserDisplay();
  updateCartBadge();
  document.addEventListener('click', function (e) {
    const menu = document.getElementById('userMenu');
    const userInfo = document.querySelector('.user-info');
    if (menu && userInfo && !menu.contains(e.target) && !userInfo.contains(e.target)) {
      menu.classList.remove('active');
    }
  });
});
