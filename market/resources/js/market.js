const MarketplaceApp = {
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

  getCart() {
    try {
      const raw = localStorage.getItem('marketplaceCart');
      return raw ? JSON.parse(raw) : [];
    } catch (error) {
      console.error('Unable to read cart from localStorage', error);
      return [];
    }
  },

  setCart(cartItems) {
    try {
      localStorage.setItem('marketplaceCart', JSON.stringify(cartItems));
      this.updateCartBadge();
    } catch (error) {
      console.error('Unable to save cart to localStorage', error);
    }
  },

  addToCart(product) {
    const cart = this.getCart();
    const existing = cart.find((item) => item.id === product.id);
    if (existing) {
      existing.quantity += 1;
    } else {
      cart.push({
        ...product,
        quantity: 1,
      });
    }
    this.setCart(cart);
    this.showNotification('Producto agregado al carrito');
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

  showNotification(message) {
    if (!message) {
      return;
    }
    alert(message);
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
  window.location.href = `/tienda?comercio_id=${storeId}`;
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

window.addToCart = function (product) {
  if (!product || !product.id) {
    return;
  }
  MarketplaceApp.addToCart(product);
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
  document.addEventListener('click', (event) => {
    const menu = document.getElementById('userMenu');
    const userInfo = document.querySelector('.user-info');
    if (menu && userInfo && !menu.contains(event.target) && !userInfo.contains(event.target)) {
      menu.classList.remove('active');
    }
  });
});
