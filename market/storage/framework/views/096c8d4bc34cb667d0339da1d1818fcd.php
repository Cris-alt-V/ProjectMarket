<?php $__env->startSection('title', 'Tienda - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <div class="store-header" id="storeHeader">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 20px; border-radius: 8px; margin-bottom: 40px; text-align: center;">
      <h1><?php echo e($comercio->nombre_negocio); ?></h1>
      <p><?php echo e($comercio->descripcion); ?></p>
      <div class="store-header-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
        <div class="info-box"><strong>⭐ Calificación</strong> 4.7/5.0</div>
        <div class="info-box"><strong>📍 Ubicación</strong> <?php echo e($comercio->ubicacion ?? 'Ubicación no disponible'); ?></div>
        <div class="info-box"><strong>📦 Productos</strong> <?php echo e($productos->count()); ?> disponibles</div>
        <div class="info-box"><strong>📞 Contacto</strong> <?php echo e($comercio->telefono ?? '(Sin teléfono)'); ?></div>
      </div>
    </div>
  </div>

  <section class="filters-section">
    <h3 style="margin-bottom: 15px; margin-top: 0;">Filtrar Productos</h3>
    <div class="filters-grid">
      <div class="filter-group">
        <label for="filterCategory">📁 Categoría</label>
        <select id="filterCategory" onchange="applyFilters()">
          <option value="">Todas las categorías</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="filterPriceMin">💰 Precio Mínimo</label>
        <input type="number" id="filterPriceMin" placeholder="0" min="0" onchange="applyFilters()">
      </div>
      <div class="filter-group">
        <label for="filterPriceMax">💰 Precio Máximo</label>
        <input type="number" id="filterPriceMax" placeholder="1000" min="0" onchange="applyFilters()">
      </div>
      <div class="filter-group" style="justify-content: flex-end; align-self: end;">
        <button class="btn btn-primary" onclick="applyFilters()">Aplicar Filtros</button>
      </div>
    </div>
  </section>

  <section class="products-section">
    <h2 class="section-title">Productos de esta Tienda</h2>
    <div class="products-grid" id="productsContainer">
      <?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="product-card" onclick="goToProductDetail(<?php echo e($producto->id_producto); ?>)">
          <img src="<?php echo e($producto->imagen_url); ?>" alt="<?php echo e($producto->nombre); ?>" class="product-image" onerror="this.style.display='none';">
          <div class="product-info">
            <div class="product-category">General</div>
            <h3 class="product-name"><?php echo e($producto->nombre); ?></h3>
            <div class="product-store">🏪 <?php echo e($comercio->nombre_negocio); ?></div>
            <div class="product-description"><?php echo e($producto->descripcion); ?></div>
            <div class="product-footer">
              <div class="product-price">$<?php echo e(number_format($producto->precio, 2)); ?></div>
              <div class="product-rating">⭐ 4.5 <span>(0)</span></div>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p style="text-align: center; grid-column: 1/-1; color: #666;">No se encontraron productos</p>
      <?php endif; ?>
    </div>
  </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  const store = <?php echo json_encode($comercio, 15, 512) ?>;
  const pageProducts = <?php echo json_encode($productos, 15, 512) ?>;

  function initializeTiendaPage() {
    if (!window.MarketplaceApp) {
      setTimeout(initializeTiendaPage, 50);
      return;
    }

    MarketplaceApp.productos = pageProducts.map(product => ({
      id: product.id_producto,
      nombre: product.nombre,
      descripcion: product.descripcion,
      precio: parseFloat(product.precio) || 0,
      stock: product.stock,
      foto: product.imagen_url || '/imagenes/blusa.png',
      comercioId: product.id_vendedor,
      categoria: product.categoria || 'General',
      ubicacion: store.ubicacion || 'Local',
      rating: 4.5,
      vendidos: 0,
    }));

    initializePage();
  }

  function createProductCard(product) {
    return `
      <div class="product-card" onclick="goToProductDetail(${product.id})">
        <img src="${product.foto}" alt="${product.nombre}" class="product-image" onerror="this.style.display='none';">
        <div class="product-info">
          <div class="product-category">${product.categoria}</div>
          <h3 class="product-name">${product.nombre}</h3>
          <div class="product-store">🏪 ${store.nombre_negocio}</div>
          <div class="product-description">${product.descripcion}</div>
          <div class="product-footer">
            <div class="product-price">$${product.precio.toFixed(2)}</div>
            <div class="product-rating">⭐ ${product.rating} <span>(${product.vendidos})</span></div>
          </div>
        </div>
      </div>
    `;
  }

  function applyFilters() {
    const searchInput = document.getElementById('searchInput');
    const query = searchInput ? searchInput.value : '';
    const category = document.getElementById('filterCategory').value;
    const priceMin = parseFloat(document.getElementById('filterPriceMin').value) || 0;
    const priceMax = parseFloat(document.getElementById('filterPriceMax').value) || 10000;

    let products = MarketplaceApp.productos;
    if (query) {
      const q = query.toLowerCase();
      products = products.filter(p => p.nombre.toLowerCase().includes(q) || p.descripcion.toLowerCase().includes(q));
    }
    if (category) {
      products = products.filter(p => p.categoria === category);
    }
    products = products.filter(p => p.precio >= priceMin && p.precio <= priceMax);

    const container = document.getElementById('productsContainer');
    if (products.length === 0) {
      container.innerHTML = '<p style="text-align: center; grid-column: 1/-1; color: #666;">No se encontraron productos</p>';
    } else {
      container.innerHTML = products.map(createProductCard).join('');
    }
  }

  function initializePage() {
    const products = MarketplaceApp.productos;
    const categories = [...new Set(products.map(p => p.categoria))];

    document.getElementById('storeHeader').innerHTML = `
      <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 20px; border-radius: 8px; margin-bottom: 40px; text-align: center;">
        <h1>${store.nombre_negocio}</h1>
        <p>${store.descripcion}</p>
        <div class="store-header-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
          <div class="info-box"><strong>⭐ Calificación</strong>4.7/5.0</div>
          <div class="info-box"><strong>📍 Ubicación</strong>${store.ubicacion || 'Ubicación no disponible'}</div>
          <div class="info-box"><strong>📦 Productos</strong>${products.length} disponibles</div>
          <div class="info-box"><strong>📞 Contacto</strong>${store.telefono || '(Sin teléfono)'}</div>
        </div>
      </div>
    `;

    const categorySelect = document.getElementById('filterCategory');
    categories.forEach(cat => {
      const option = document.createElement('option');
      option.value = cat;
      option.textContent = cat;
      categorySelect.appendChild(option);
    });

    updateUserDisplay();
    updateCartBadge();
    applyFilters();
  }

  document.addEventListener('DOMContentLoaded', function() {
    initializeTiendaPage();
  });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\castr\OneDrive\Desktop\laravel\hola\ProjectMarket\market\resources\views/tienda.blade.php ENDPATH**/ ?>