<?php $__env->startSection('title', 'Marketplace Local - Inicio'); ?>

<?php $__env->startSection('content'); ?>
  <section class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 20px; border-radius: 8px; margin-bottom: 40px; text-align: center;">
    <h2 style="font-size: 2.5em; margin-bottom: 15px;">Descubre Productos Locales</h2>
    <p style="font-size: 1.2em; margin-bottom: 30px; opacity: 0.9;">Apoya a tus negocios locales y encuentra lo que necesitas en tu comunidad</p>
    <button class="btn btn-primary" onclick="window.location.href='/productos'">Comenzar a Buscar</button>
  </section>

  <section class="categories-section">
    <h3 class="categories-title">📂 Categorías</h3>
    <div class="categories-grid" id="categoriesContainer"></div>
  </section>

  <section class="filters-section">
    <h3 style="margin-bottom: 15px; margin-top: 0;">Filtrar Resultados</h3>
    <div class="filters-grid">
      <div class="filter-group">
        <label for="filterLocation">📍 Ubicación</label>
        <input type="text" id="filterLocation" placeholder="Buscar por ubicación...">
      </div>
      <div class="filter-group">
        <label for="filterPriceMin">💰 Precio Mínimo</label>
        <input type="number" id="filterPriceMin" placeholder="0" min="0">
      </div>
      <div class="filter-group">
        <label for="filterPriceMax">💰 Precio Máximo</label>
        <input type="number" id="filterPriceMax" placeholder="1000" min="0">
      </div>
      <div class="filter-group">
        <label for="filterCategory">📁 Categoría</label>
        <select id="filterCategory">
          <option value="">Todas las categorías</option>
          <option value="General">General</option>
          <option value="Accesorios">Accesorios</option>
          <option value="Ropa">Ropa</option>
          <option value="Alimentos">Alimentos</option>
          <option value="Electrónica">Electrónica</option>
          <option value="Hogar">Hogar</option>
          <option value="Belleza">Belleza</option>
        </select>
      </div>
      <div class="filter-group" style="justify-content: flex-end; align-self: end;">
        <button class="btn btn-primary" onclick="applyFilters()">Aplicar Filtros</button>
      </div>
    </div>
  </section>

  <section class="products-section">
    <h2 class="section-title">⭐ Productos Destacados</h2>
    <div class="products-grid" id="featuredProducts"></div>
  </section>

  <section class="products-section">
    <h2 class="section-title">🆕 Productos Recientes</h2>
    <div class="products-grid" id="recentProducts"></div>
  </section>

  <section class="products-section">
    <h2 class="section-title">🏪 Comercios Destacados</h2>
    <div class="store-grid" id="storesGrid"></div>
  </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  const pageStores = <?php echo json_encode($comercios, 15, 512) ?>;
  const pageProducts = <?php echo json_encode($productos, 15, 512) ?>;

  function initializeHomePage() {
    if (!window.MarketplaceApp) {
      setTimeout(initializeHomePage, 50);
      return;
    }

    MarketplaceApp.comercios = pageStores.map(store => ({
      id: store.id_vendedor,
      nombre: store.nombre_negocio,
      ubicacion: store.ubicacion || 'Ubicación no disponible',
      telefono: store.telefono || '(Sin teléfono)',
      email: store.email || 'info@marketplace.local',
      descripcion: store.descripcion || 'Comercio local',
      rating: 4.7,
      foto: '',
    }));

    MarketplaceApp.productos = pageProducts.map(product => ({
      id: product.id_producto,
      nombre: product.nombre,
      descripcion: product.descripcion,
      precio: parseFloat(product.precio) || 0,
      stock: product.stock,
      foto: product.imagen_url || '',
      comercioId: product.id_vendedor,
      categoria: product.categoria || 'General',
      ubicacion: MarketplaceApp.comercios.find(c => c.id === product.id_vendedor)?.ubicacion || 'Local',
      rating: 4.5,
      vendidos: 0,
    }));

    initHomePage();
  }

  function createProductCard(product) {
    const comercio = MarketplaceApp.getComercioById(product.comercioId) || { nombre: 'Comercio local' };
    return `
      <div class="product-card" onclick="goToProductDetail(${product.id})">
        <img src="${product.foto}" alt="${product.nombre}" class="product-image" onerror="this.style.display='none';">
        <div class="product-info">
          <div class="product-category">${product.categoria}</div>
          <h3 class="product-name">${product.nombre}</h3>
          <div class="product-store">🏪 ${comercio.nombre}</div>
          <div class="product-description">${product.descripcion}</div>
          <div class="product-footer">
            <div class="product-price">$${product.precio.toFixed(2)}</div>
            <div class="product-rating">⭐ ${product.rating} <span>(${product.vendidos})</span></div>
          </div>
        </div>
      </div>
    `;
  }

  function createStoreCard(store) {
    return `
      <div class="store-card" onclick="goToStore(${store.id})">
        <img src="${store.foto}" alt="${store.nombre}" class="product-image">
        <div class="store-info">
          <h3 class="store-name">${store.nombre}</h3>
          <div class="store-location">📍 ${store.ubicacion}</div>
          <p class="store-description">${store.descripcion}</p>
          <div class="product-footer">
            <div class="product-rating">⭐ ${store.rating}</div>
          </div>
        </div>
      </div>
    `;
  }

  function applyFilters() {
    const location = document.getElementById('filterLocation').value;
    const category = document.getElementById('filterCategory').value;
    const priceMin = parseFloat(document.getElementById('filterPriceMin').value);
    const priceMax = parseFloat(document.getElementById('filterPriceMax').value);

    const filteredProducts = MarketplaceApp.productos.filter(product => {
      const matchesCategory = !category || product.categoria === category;
      const matchesLocation = !location || product.ubicacion.toLowerCase().includes(location.toLowerCase());
      const matchesPriceMin = isNaN(priceMin) || product.precio >= priceMin;
      const matchesPriceMax = isNaN(priceMax) || product.precio <= priceMax;
      return matchesCategory && matchesLocation && matchesPriceMin && matchesPriceMax;
    });

    const featured = filteredProducts.slice(0, 3);
    const recent = filteredProducts.slice(-3);

    document.getElementById('featuredProducts').innerHTML = featured.length ? featured.map(createProductCard).join('') : '<p>No hay productos destacados.</p>';
    document.getElementById('recentProducts').innerHTML = recent.length ? recent.map(createProductCard).join('') : '<p>No hay productos recientes.</p>';
  }

  function initHomePage() {
    document.getElementById('categoriesContainer').innerHTML = MarketplaceApp.categorias.map(cat => `
      <div class="product-card" onclick="window.location.href='/productos?search=${encodeURIComponent(cat.nombre)}'">
        <div style="font-size: 2em; margin-bottom: 10px;">${cat.icono}</div>
        <h3 style="margin-bottom: 10px;">${cat.nombre}</h3>
        <p>Explora productos en la categoría ${cat.nombre}.</p>
      </div>
    `).join('');

    document.getElementById('storesGrid').innerHTML = MarketplaceApp.comercios.map(createStoreCard).join('');
    applyFilters();
  }

  document.addEventListener('DOMContentLoaded', function() {
    initializeHomePage();
  });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Desktop\frere\ProjectMarket\market\resources\views/welcome.blade.php ENDPATH**/ ?>