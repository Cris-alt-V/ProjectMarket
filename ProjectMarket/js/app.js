// Sistema de datos del Marketplace
const MarketplaceApp = {
  // Fotos locales por defecto para los productos iniciales
  defaultProductImages: {
    1: "../imagenes/plata%20collar.png",
    2: "../imagenes/bolso.png",
    3: "../imagenes/blusa.png",
    4: "../imagenes/vakero.png",
    5: "../imagenes/cafe.jpg",
    6: "../imagenes/pan.png",
    7: "../imagenes/blusa.png",
    8: "../imagenes/bolso.png",
    9: "../imagenes/plata%20collar.png"
  },

  // Datos simulados de comercios
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

  // Datos simulados de productos
  productos: [
    {
      id: 1,
      nombre: "Collar Artesanal de Plata",
      precio: 35.00,
      comercioId: 1,
      categoria: "Accesorios",
      ubicacion: "Centro, Ciudad",
      descripcion: "Collar único hecho a mano con plata pura",
      foto: "../imagenes/plata%20collar.png",
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
      foto: "../imagenes/bolso.png",
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
      foto: "../imagenes/blusa.png",
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
      foto: "../imagenes/vakero.png",
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
      foto: "../imagenes/cafe.jpg",
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
      foto: "../imagenes/pan.png",
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
      foto: "../imagenes/blusa.png",
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
      foto: "../imagenes/bolso.png",
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
      foto: "../imagenes/plata%20collar.png",
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
      foto: "../imagenes/blusa.png",
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
      foto: "../imagenes/bolso.png",
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
      foto: "../imagenes/plata%20collar.png",
      rating: 4.8,
      vendidos: 6
    }
  ],

  // Categorías disponibles
  categorias: [
    { id: 1, nombre: "Accesorios", icono: "👜" },
    { id: 2, nombre: "Ropa", icono: "👕" },
    { id: 3, nombre: "Alimentos", icono: "🍽️" },
    { id: 4, nombre: "Electrónica", icono: "📱" },
    { id: 5, nombre: "Hogar", icono: "🏠" },
    { id: 6, nombre: "Belleza", icono: "💄" }
  ],

  // Inicialización
  init: function() {
    this.loadFromStorage();
    this.syncCartImages();
    this.setupNavigation();
  },

  // Cargar datos de localStorage
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

  // Guardar datos en localStorage
  saveToStorage: function() {
    localStorage.setItem('marketplace', JSON.stringify({
      comercios: this.comercios,
      productos: this.productos
    }));
  },

  // Obtener usuario actual
  getCurrentUser: function() {
    return JSON.parse(localStorage.getItem('currentUser')) || null;
  },

  // Guardar usuario actual
  setCurrentUser: function(user) {
    if (user) {
      localStorage.setItem('currentUser', JSON.stringify(user));
    } else {
      localStorage.removeItem('currentUser');
    }
  },

  // Obtener carrito
  getCart: function() {
    return JSON.parse(localStorage.getItem('cart')) || [];
  },

  // Guardar carrito
  setCart: function(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
  },

  // Agregar al carrito
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

  // Mostrar notificación
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

  // Buscar productos
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

  // Obtener producto por ID
  getProductById: function(id) {
    return this.productos.find(p => p.id === parseInt(id));
  },

  // Obtener comercio por ID
  getComercioById: function(id) {
    return this.comercios.find(c => c.id === parseInt(id));
  },

  // Obtener productos por comercio
  getProductosByComercio: function(comercioId) {
    return this.productos.filter(p => p.comercioId === parseInt(comercioId));
  },

  // Setup de navegación
  setupNavigation: function() {
    const user = this.getCurrentUser();
    const accountLink = document.querySelector('[href="micuenta.html"]');
    
    if (user && accountLink) {
      accountLink.textContent = `Mi Cuenta (${user.nombre})`;
    }
  }
};

// Inicializar cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
  MarketplaceApp.init();
});

