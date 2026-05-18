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

  showNotification: function (message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: ${type === 'success' ? '#dff0d8' : '#f2dede'};
      color: ${type === 'success' ? '#3c763d' : '#a94442'};
      padding: 15px 20px;
      border-radius: 4px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      z-index: 9999;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
  },

  toggleUserMenu: function () {
    const menu = document.getElementById('userMenu');
    if (menu) {
      menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
  },

  goToCart: function () {
    window.location.href = '/carrito';
  },

  search: function () {
    const searchInput = document.getElementById('searchInput');
    if (searchInput && searchInput.value) {
      window.location.href = '/productos?search=' + encodeURIComponent(searchInput.value);
    }
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

  getProductById: function (id) {
    return this.productos.find(p => p.id === parseInt(id, 10));
  },

  getComercioById: function (id) {
    return this.comercios.find(c => c.id === parseInt(id, 10));
  },

  getProductosByComercio: function (comercioId) {
    return this.productos.filter(p => p.comercioId === parseInt(comercioId, 10));
  },
};

// Funciones globales para navegación
function goToProductDetail(id) {
  window.location.href = '/producto/' + id;
}

function goToStore(id) {
  window.location.href = '/tienda?id=' + id;
}

function toggleUserMenu() {
  MarketplaceApp.toggleUserMenu();
}

function goToCart() {
  MarketplaceApp.goToCart();
}

function search() {
  MarketplaceApp.search();
}

// Funciones para carrito
function addToCart(id) {
  const product = MarketplaceApp.getProductById(id);
  if (product) {
    MarketplaceApp.addToCart(product);
    updateCartBadge();
  }
}

function removeCartItem(id) {
  const cart = MarketplaceApp.getCart();
  const filtered = cart.filter(item => item.id !== id);
  MarketplaceApp.setCart(filtered);
  updateCartBadge();
  location.reload();
}

function checkout() {
  const cart = MarketplaceApp.getCart();
  if (cart.length === 0) {
    MarketplaceApp.showNotification('El carrito está vacío', 'error');
    return;
  }
  MarketplaceApp.showNotification('Procesando pago...');
  setTimeout(() => {
    MarketplaceApp.setCart([]);
    updateCartBadge();
    window.location.href = '/';
    MarketplaceApp.showNotification('¡Pedido realizado exitosamente!', 'success');
  }, 1500);
}

// Funciones para actualizar UI
function updateCartBadge() {
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
}

function updateUserDisplay() {
  // Usuario se maneja por sesión en el servidor
  // Esta función solo es para actualizar en cliente si es necesario
  updateCartBadge();
}

// Funciones para filtros
function applyFilters() {
  const location = document.getElementById('filterLocation')?.value || '';
  const category = document.getElementById('filterCategory')?.value || '';
  const priceMin = document.getElementById('filterPriceMin')?.value || '0';
  const priceMax = document.getElementById('filterPriceMax')?.value || '999999';

  let url = '/productos?';
  if (location) url += '&location=' + encodeURIComponent(location);
  if (category) url += '&category=' + encodeURIComponent(category);
  if (priceMin) url += '&priceMin=' + priceMin;
  if (priceMax) url += '&priceMax=' + priceMax;

  window.location.href = url;
}

function resetFilters() {
  document.getElementById('filterLocation').value = '';
  document.getElementById('filterCategory').value = '';
  document.getElementById('filterPriceMin').value = '';
  document.getElementById('filterPriceMax').value = '';
  window.location.href = '/productos';
}

// Funciones para tabs
function switchTab(tab) {
  const tabs = document.querySelectorAll('[id$="Tab"]');
  const buttons = document.querySelectorAll('.tab-btn');

  tabs.forEach(t => {
    t.style.display = t.id === tab + 'Tab' ? 'block' : 'none';
  });

  buttons.forEach(btn => {
    btn.classList.remove('active');
    if (btn.getAttribute('onclick').includes(tab)) {
      btn.classList.add('active');
    }
  });
}

// Funciones para cuenta
function deleteAccount() {
  if (confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.')) {
    MarketplaceApp.showNotification('Cuenta eliminada');
    setTimeout(() => {
      window.location.href = '/';
    }, 1500);
  }
}

function deleteStore() {
  if (confirm('¿Estás seguro de que deseas eliminar tu tienda? Esta acción no se puede deshacer.')) {
    MarketplaceApp.showNotification('Tienda eliminada');
    setTimeout(() => {
      window.location.href = '/';
    }, 1500);
  }
}

// Inicializar en carga
document.addEventListener('DOMContentLoaded', function() {
  MarketplaceApp.loadFromStorage();
  updateCartBadge();
  updateUserDisplay();
});

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
