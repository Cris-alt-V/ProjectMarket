// Sistema de datos del Marketplace
window.MarketplaceApp = {
  // Fotos locales por defecto para los productos iniciales
  defaultProductImages: {
    1: "/imagenes/plata%20collar.png",
    2: "/imagenes/bolso.png",
    3: "/imagenes/blusa.png",
    4: "/imagenes/vakero.png",
    5: "/imagenes/cafe.jpg",
    6: "/imagenes/pan.png",
    7: "/imagenes/blusa.png",
    8: "/imagenes/bolso.png",
    9: "/imagenes/plata%20collar.png"
  },

  comercios: [
    {
      id: 1,
      nombre: "Tienda Artesanal Local",
      ubicacion: "Centro, Ciudad",
      telefono: "(555) 123-4567",
      email: "tienda@artesanal.com",
      descripcion: "Productos artesanales únicos",
      rating: 4.8,
      foto: "https://via.placeholder.com/100?text=Tienda1"
    },
    {
      id: 2,
      nombre: "Boutique de Moda",
      ubicacion: "Zona Norte, Ciudad",
      telefono: "(555) 234-5678",
      email: "boutique@moda.com",
      descripcion: "Ropa y accesorios de moda",
      rating: 4.5,
      foto: "https://via.placeholder.com/100?text=Tienda2"
    },
    {
      id: 3,
      nombre: "Café Gourmet",
      ubicacion: "Centro Histórico, Ciudad",
      telefono: "(555) 345-6789",
      email: "cafe@gourmet.com",
      descripcion: "Café y repostería artesanal",
      rating: 4.9,
      foto: "https://via.placeholder.com/100?text=Tienda3"
    }
  ],

  productos: [
    {
      id: 1,
      nombre: "Collar Artesanal de Plata",
      precio: 35.00,
      comercioId: 1,
      categoria: "Accesorios",
      ubicacion: "Centro, Ciudad",
      descripcion: "Collar único hecho a mano con plata pura",
      foto: "/imagenes/plata%20collar.png",
      rating: 4.7,
      vendidos: 12
    },
    {
      id: 2,
      nombre: "Bolso Artesanal de Cuero",
      precio: 65.00,
      comercioId: 1,
      categoria: "Bolsos",
      ubicacion: "Centro, Ciudad",
      descripcion: "Bolso hecho a mano con cuero genuino",
      foto: "/imagenes/bolso.png",
      rating: 4.8,
      vendidos: 28
    },
    {
      id: 3,
      nombre: "Blusa Casual Estampada",
      precio: 28.00,
      comercioId: 2,
      categoria: "Ropa",
      ubicacion: "Zona Norte, Ciudad",
      descripcion: "Blusa de algodón con estampado moderno",
      foto: "/imagenes/blusa.png",
      rating: 4.6,
      vendidos: 45
    },
    {
      id: 4,
      nombre: "Pantalón Vaquero Premium",
      precio: 55.00,
      comercioId: 2,
      categoria: "Ropa",
      ubicacion: "Zona Norte, Ciudad",
      descripcion: "Pantalón vaquero de alta calidad",
      foto: "/imagenes/vakero.png",
      rating: 4.9,
      vendidos: 62
    },
    {
      id: 5,
      nombre: "Café Espresso Premium 500g",
      precio: 15.00,
      comercioId: 3,
      categoria: "Alimentos",
      ubicacion: "Centro Histórico, Ciudad",
      descripcion: "Café tostado artesanalmente",
      foto: "/imagenes/cafe.jpg",
      rating: 4.8,
      vendidos: 120
    },
    {
      id: 6,
      nombre: "Croissantes de Chocolate",
      precio: 8.00,
      comercioId: 3,
      categoria: "Alimentos",
      ubicacion: "Centro Histórico, Ciudad",
      descripcion: "Croissantes frescos con chocolate belga",
      foto: "/imagenes/pan.png",
      rating: 4.9,
      vendidos: 95
    },
    {
      id: 7,
      nombre: "Elegante camiseta para hombre",
      precio: 42.00,
      comercioId: 2,
      categoria: "Ropa",
      ubicacion: "Zona Norte, Ciudad",
      descripcion: "Camiseta de corte limpio y confeccion premium, ideal para una presencia sobria y profesional.",
      foto: "/imagenes/blusa.png",
      rating: 4.7,
      vendidos: 18
    },
    {
      id: 8,
      nombre: "Traje para boda",
      precio: 249.00,
      comercioId: 2,
      categoria: "Ropa",
      ubicacion: "Zona Norte, Ciudad",
      descripcion: "Traje de sastreria clasica con acabados de alta calidad, disenado para ceremonias y eventos de maxima formalidad.",
      foto: "/imagenes/bolso.png",
      rating: 4.9,
      vendidos: 9
    },
    {
      id: 9,
      nombre: "Solar Bloom Privé",
      precio: 74.00,
      comercioId: 3,
      categoria: "Alimentos",
      ubicacion: "Soyapango",
      descripcion: "Cultivado con rayos de sol seleccionados, exclusivo para clientes con paladar refinado.",
      foto: "/imagenes/plata%20collar.png",
      rating: 4.8,
      vendidos: 6
    }
  ],

  // Productos semilla para mantener novedades aunque haya datos guardados en localStorage
  seedProducts: [
    {
      id: 7,
      nombre: "Elegante camiseta para hombre",
      precio: 42.00,
      comercioId: 2,
      categoria: "Ropa",
      ubicacion: "Zona Norte, Ciudad",
      descripcion: "Camiseta de corte limpio y confeccion premium, ideal para una presencia sobria y profesional.",
      foto: "/imagenes/blusa.png",
      rating: 4.7,
      vendidos: 18
    },
    {
      id: 8,
      nombre: "Traje para boda",
      precio: 249.00,
      comercioId: 2,
      categoria: "Ropa",
      ubicacion: "Zona Norte, Ciudad",
      descripcion: "Traje de sastreria clasica con acabados de alta calidad, disenado para ceremonias y eventos de maxima formalidad.",
      foto: "/imagenes/bolso.png",
      rating: 4.9,
      vendidos: 9
    },
    {
      id: 9,
      nombre: "Solar Bloom Privé",
      precio: 74.00,
      comercioId: 3,
      categoria: "Alimentos",
      ubicacion: "Soyapango",
      descripcion: "Cultivado con rayos de sol seleccionados, exclusivo para clientes con paladar refinado.",
      foto: "/imagenes/plata%20collar.png",
      rating: 4.8,
      vendidos: 6
    }
  ],

  categorias: [
    { id: 1, nombre: "Accesorios", icono: "👜" },
    { id: 2, nombre: "Ropa", icono: "👕" },
    { id: 3, nombre: "Alimentos", icono: "🍽️" },
    { id: 4, nombre: "Electrónica", icono: "📱" },
    { id: 5, nombre: "Hogar", icono: "🏠" },
    { id: 6, nombre: "Belleza", icono: "💄" }
  ],

  init: function() {
    this.loadFromStorage();
    this.syncCartImages();
    this.setupNavigation();
  },

  loadFromStorage: function() {
    const stored = localStorage.getItem('marketplace');
    if (stored) {
      const data = JSON.parse(stored);
      this.comercios = data.comercios || this.comercios;
      this.productos = data.productos || this.productos;
    }
    this.ensureSeedProducts();
    this.productos = this.productos.map(producto =>
      producto.id === 9 ? { ...producto, ubicacion: "Soyapango" } : producto
    );
    this.syncProductImages();
    this.saveToStorage();
  },

  // Agrega productos semilla faltantes sin duplicar registros existentes
  ensureSeedProducts: function() {
    this.seedProducts.forEach(seed => {
      const exists = this.productos.some(producto => producto.id === seed.id);
      if (!exists) {
        this.productos.push(seed);
      }
    });
  },

  // Reemplaza placeholders previos por rutas locales sin tocar fotos nuevas del usuario
  syncProductImages: function() {
    const legacyImageMap = {
      3: ["../imagenes/vakero.png", "/imagenes/vakero.png"],
      4: ["../imagenes/blusa.png", "/imagenes/blusa.png", "https://via.placeholder.com/300x300?text=Pantalon"],
      5: ["../imagenes/bolso.png", "/imagenes/bolso.png", "https://via.placeholder.com/300x300?text=Cafe"],
      6: ["../imagenes/plata%20collar.png", "/imagenes/plata%20collar.png", "https://via.placeholder.com/300x300?text=Croissant"],
      7: ["../imagenes/vakero.png", "/imagenes/vakero.png"],
      8: ["../imagenes/cafe.jpg", "/imagenes/cafe.jpg"]
    };

    this.productos = this.productos.map(producto => {
      const defaultImage = this.defaultProductImages[producto.id];
      const currentImage = producto.foto || '';
      const isPlaceholder = currentImage.includes('via.placeholder.com');
      const isLegacyImage = (legacyImageMap[producto.id] || []).includes(currentImage);

      if (defaultImage && (!currentImage || isPlaceholder || isLegacyImage)) {
        return {
          ...producto,
          foto: defaultImage
        };
      }

      return producto;
    });
  },

  // Actualiza fotos antiguas en carrito para evitar placeholders rotos
  syncCartImages: function() {
    const legacyImageMap = {
      3: ["../imagenes/vakero.png", "/imagenes/vakero.png"],
      4: ["../imagenes/blusa.png", "/imagenes/blusa.png", "https://via.placeholder.com/300x300?text=Pantalon"],
      5: ["../imagenes/bolso.png", "/imagenes/bolso.png", "https://via.placeholder.com/300x300?text=Cafe"],
      6: ["../imagenes/plata%20collar.png", "/imagenes/plata%20collar.png", "https://via.placeholder.com/300x300?text=Croissant"],
      7: ["../imagenes/vakero.png", "/imagenes/vakero.png"],
      8: ["../imagenes/cafe.jpg", "/imagenes/cafe.jpg"]
    };

    const cart = this.getCart().map(item => {
      const defaultImage = this.defaultProductImages[item.id];
      const currentImage = item.foto || '';
      const isPlaceholder = currentImage.includes('via.placeholder.com');
      const isLegacyImage = (legacyImageMap[item.id] || []).includes(currentImage);

      if (defaultImage && (!currentImage || isPlaceholder || isLegacyImage)) {
        return {
          ...item,
          foto: defaultImage
        };
      }

      return item;
    });

    this.setCart(cart);
  },

  saveToStorage: function() {
    localStorage.setItem('marketplace', JSON.stringify({
      comercios: this.comercios,
      productos: this.productos
    }));
  },

  getCurrentUser: function() {
    return JSON.parse(localStorage.getItem('currentUser')) || null;
  },

  setCurrentUser: function(user) {
    if (user) {
      localStorage.setItem('currentUser', JSON.stringify(user));
    } else {
      localStorage.removeItem('currentUser');
    }
  },

  getCart: function() {
    return JSON.parse(localStorage.getItem('cart')) || [];
  },

  setCart: function(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
  },

  addToCart: function(producto) {
    const cart = this.getCart();
    const existing = cart.find(item => item.id === producto.id);
    if (existing) {
      existing.cantidad++;
    } else {
      cart.push({
        ...producto,
        cantidad: 1
      });
    }
    this.setCart(cart);
    this.showNotification('Producto agregado al carrito');
  },

  showNotification: function(message) {
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

  searchProducts: function(query, filtros = {}) {
    let results = this.productos;
    if (query) {
      const q = query.toLowerCase();
      results = results.filter(p =>
        p.nombre.toLowerCase().includes(q) ||
        p.descripcion.toLowerCase().includes(q) ||
        p.categoria.toLowerCase().includes(q)
      );
    }
    if (filtros.categoria) {
      results = results.filter(p => p.categoria === filtros.categoria);
    }
    if (filtros.ubicacion) {
      results = results.filter(p => p.ubicacion.toLowerCase().includes(filtros.ubicacion.toLowerCase()));
    }
    if (filtros.precioMax) {
      results = results.filter(p => p.precio <= filtros.precioMax);
    }
    if (filtros.precioMin) {
      results = results.filter(p => p.precio >= filtros.precioMin);
    }
    return results;
  },

  getProductById: function(id) {
    return this.productos.find(p => p.id === parseInt(id));
  },

  getComercioById: function(id) {
    return this.comercios.find(c => c.id === parseInt(id));
  },

  getProductosByComercio: function(comercioId) {
    return this.productos.filter(p => p.comercioId === parseInt(comercioId));
  },

  setupNavigation: function() {
    const accountLink = document.querySelector('[href="/micuenta"]');
    const storesLink = document.querySelector('[href="/mis-comercios"]');
    const loginLink = document.querySelector('[href="/registro"]');
    const registerLink = document.querySelector('[href="/registro?tab=register"]');
    const user = this.getCurrentUser();
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
      if (storesLink && user.tipo === 'comercio') {
        storesLink.style.display = 'block';
      }
    }
  }
};

window.getQueryParam = function(name) {
  const params = new URLSearchParams(window.location.search);
  return params.get(name);
};

window.goToCart = function() {
  window.location.href = '/carrito';
};

window.goToProductDetail = function(productId) {
  window.location.href = `/detalle-producto?id=${productId}`;
};

window.goToStore = function(storeId) {
  window.location.href = `/tienda?id=${storeId}`;
};

window.goToRegister = function(tab) {
  window.location.href = `/registro${tab ? '?tab=' + tab : ''}`;
};

window.search = function() {
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

window.toggleUserMenu = function() {
  const menu = document.getElementById('userMenu');
  if (menu) {
    menu.classList.toggle('active');
  }
};

window.logout = function() {
  MarketplaceApp.setCurrentUser(null);
  updateUserDisplay();
  MarketplaceApp.showNotification('Sesión cerrada');
};

window.updateUserDisplay = function() {
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
    if (storesLink) {
      storesLink.style.display = user.tipo === 'comercio' ? 'block' : 'none';
    }
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

window.updateCartBadge = function() {
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

// ======================== FUNCIONES DE REGISTRO Y AUTENTICACIÓN ========================
window.switchTab = function(tabName) {
  // Ocultar todos los tabs
  const tabs = document.querySelectorAll('.auth-tab-content');
  const buttons = document.querySelectorAll('.tab-btn');

  tabs.forEach(tab => tab.classList.remove('active'));
  buttons.forEach(btn => btn.classList.remove('active'));

  // Mostrar el tab seleccionado
  const activeTab = document.getElementById(tabName + 'Tab');
  const activeButton = document.getElementById('btn' + tabName.charAt(0).toUpperCase() + tabName.slice(1) + 'Tab');

  if (activeTab) activeTab.classList.add('active');
  if (activeButton) activeButton.classList.add('active');
};

window.handleLogin = function(event) {
  event.preventDefault();

  const email = document.getElementById('loginEmail').value;
  const password = document.getElementById('loginPassword').value;

  if (!email || !password) {
    MarketplaceApp.showNotification('Por favor completa todos los campos', 'error');
    return;
  }

  // Simular login (en producción, hacer llamada al servidor)
  const user = {
    id: Math.random(),
    nombre: email.split('@')[0],
    email: email,
    tipo: 'comprador',
    fecha_registro: new Date().toISOString()
  };

  MarketplaceApp.setCurrentUser(user);
  updateUserDisplay();
  MarketplaceApp.showNotification('¡Bienvenido! Sesión iniciada correctamente');

  setTimeout(() => {
    window.location.href = '/';
  }, 1500);
};

window.handleRegisterBuyer = function(event) {
  event.preventDefault();

  const name = document.getElementById('buyerName').value;
  const email = document.getElementById('buyerEmail').value;
  const phone = document.getElementById('buyerPhone').value;
  const address = document.getElementById('buyerAddress').value;
  const password = document.getElementById('buyerPassword').value;
  const passwordConfirm = document.getElementById('buyerPasswordConfirm').value;
  const termsAccepted = document.getElementById('termsAccept').checked;

  // Validaciones
  if (!name || !email || !phone || !address || !password || !passwordConfirm) {
    MarketplaceApp.showNotification('Por favor completa todos los campos', 'error');
    return;
  }

  if (password !== passwordConfirm) {
    MarketplaceApp.showNotification('Las contraseñas no coinciden', 'error');
    return;
  }

  if (!termsAccepted) {
    MarketplaceApp.showNotification('Debes aceptar los términos y condiciones', 'error');
    return;
  }

  if (password.length < 6) {
    MarketplaceApp.showNotification('La contraseña debe tener al menos 6 caracteres', 'error');
    return;
  }

  // Crear usuario
  const newUser = {
    id: Math.random(),
    nombre: name,
    email: email,
    telefono: phone,
    direccion: address,
    tipo: 'comprador',
    fecha_registro: new Date().toISOString()
  };

  MarketplaceApp.setCurrentUser(newUser);
  updateUserDisplay();
  MarketplaceApp.showNotification('¡Cuenta creada exitosamente! Bienvenido a AristoMarket');

  setTimeout(() => {
    window.location.href = '/';
  }, 1500);
};

window.handleRegisterStore = function(event) {
  event.preventDefault();

  const storeName = document.getElementById('storeName').value;
  const ownerName = document.getElementById('storeOwner').value;
  const email = document.getElementById('storeEmail').value;
  const phone = document.getElementById('storePhone').value;
  const location = document.getElementById('storeLocation').value;
  const description = document.getElementById('storeDescription').value;
  const password = document.getElementById('storePassword').value;
  const passwordConfirm = document.getElementById('storePasswordConfirm').value;
  const termsAccepted = document.getElementById('storeTermsAccept').checked;

  // Validaciones
  if (!storeName || !ownerName || !email || !phone || !location || !description || !password || !passwordConfirm) {
    MarketplaceApp.showNotification('Por favor completa todos los campos', 'error');
    return;
  }

  if (password !== passwordConfirm) {
    MarketplaceApp.showNotification('Las contraseñas no coinciden', 'error');
    return;
  }

  if (!termsAccepted) {
    MarketplaceApp.showNotification('Debes aceptar los términos y condiciones para comerciantes', 'error');
    return;
  }

  if (password.length < 6) {
    MarketplaceApp.showNotification('La contraseña debe tener al menos 6 caracteres', 'error');
    return;
  }

  // Crear comercio y usuario
  const newStore = {
    id: Math.random(),
    nombre: storeName,
    ubicacion: location,
    telefono: phone,
    email: email,
    descripcion: description,
    rating: 5.0,
    foto: 'https://via.placeholder.com/100?text=' + storeName.replace(/\s/g, '+')
  };

  const storeUser = {
    id: Math.random(),
    nombre: ownerName,
    email: email,
    telefono: phone,
    comercioId: newStore.id,
    tipo: 'comercio',
    fecha_registro: new Date().toISOString()
  };

  // Agregar el comercio
  MarketplaceApp.comercios.push(newStore);
  MarketplaceApp.setCurrentUser(storeUser);
  updateUserDisplay();
  MarketplaceApp.showNotification('¡Comercio registrado exitosamente! Ahora puedes empezar a vender');

  setTimeout(() => {
    window.location.href = '/mis-comercios';
  }, 1500);
};

// ======================== INICIALIZACIÓN ========================
window.addEventListener('DOMContentLoaded', function() {
  MarketplaceApp.init();
  updateUserDisplay();
  updateCartBadge();
  
  // Determinar el tab activo inicial
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab') || 'login';
  
  if (document.getElementById(tab + 'Tab')) {
    switchTab(tab);
  }
  
  document.addEventListener('click', function(e) {
    const menu = document.getElementById('userMenu');
    const userInfo = document.querySelector('.user-info');
    if (menu && userInfo && !menu.contains(e.target) && !userInfo.contains(e.target)) {
      menu.classList.remove('active');
    }
  });
});
