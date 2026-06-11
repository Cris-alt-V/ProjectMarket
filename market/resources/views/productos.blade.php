@extends('layouts.app')

@section('title', 'Explorar Productos - Marketplace Local')

@section('content')
  <h1 style="margin-bottom: 30px; color: #333;">Explorar Productos</h1>

  <style>
    .products-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 20px;
    }
    .product-card-item {
      min-height: auto;
    }
    .product-card-item .product-description {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      overflow-wrap: anywhere;
      word-break: break-word;
      line-height: 1.45;
      min-height: calc(1.45em * 3);
    }
    @media (max-width: 980px) {
      .products-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }
    @media (max-width: 640px) {
      .products-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <div style="display: grid; grid-template-columns: 250px 1fr; gap: 30px; margin-bottom: 40px;">
    <aside style="background: white; padding: 20px; border-radius: 8px; height: fit-content; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px;">Filtros</h3>

      <div class="filter-group">
        <label for="filterCategory">📁 Categoría</label>
        <select id="filterCategory" onchange="applyFilters()">
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

      <div class="filter-group">
        <label for="filterLocation">📍 Ubicación</label>
        <input type="text" id="filterLocation" placeholder="Buscar por ubicación..." onchange="applyFilters()">
      </div>

      <div style="border-top: 1px solid #eee; padding-top: 15px; margin-top: 15px;">
        <label style="display: block; margin-bottom: 10px; font-weight: 600;">💰 Rango de Precio</label>
        <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">
          <div class="filter-group" style="margin-bottom: 0;">
            <label for="filterPriceMin">Precio Mínimo</label>
            <input type="number" id="filterPriceMin" placeholder="0" min="0" step="0.01" onchange="applyFilters()" oninput="applyFilters()">
          </div>
          <div class="filter-group" style="margin-bottom: 0;">
            <label for="filterPriceMax">Precio Máximo</label>
            <input type="number" id="filterPriceMax" placeholder="Sin límite" min="0" step="0.01" onchange="applyFilters()" oninput="applyFilters()">
          </div>
        </div>
      </div>

      <div class="filter-group" style="margin-top: 15px;">
        <label for="filterRating">⭐ Calificación Mínima</label>
        <select id="filterRating" onchange="applyFilters()">
          <option value="">Todas</option>
          <option value="4.5">4.5 y superior</option>
          <option value="4">4.0 y superior</option>
          <option value="3.5">3.5 y superior</option>
        </select>
      </div>

      <button class="btn btn-secondary btn-block" style="margin-top: 15px;" onclick="resetFilters()">Limpiar Filtros</button>
    </aside>

    <div>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div id="resultCount" style="color: #666;">Mostrando todos los productos</div>
        <select id="sortBy" onchange="applyFilters()" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
          <option value="relevancia">Ordenar por: Relevancia</option>
          <option value="precio-asc">Precio: Menor a Mayor</option>
          <option value="precio-desc">Precio: Mayor a Menor</option>
          <option value="rating">Mejor Calificados</option>
          <option value="vendidos">Más Vendidos</option>
        </select>
      </div>

      <div class="products-grid" id="productsContainer" style="max-width: 1200px;">
        @if(count($productos) > 0)
          @foreach($productos as $product)
            @php
              $store = $comercios->firstWhere('id_vendedor', $product->id_vendedor);
            @endphp
            <div class="product-card product-card-item" onclick="goToProductDetail({{ $product->id_producto }})">
              <div style="width: 100%; height: 180px; background: white; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                <img src="{{ $product->imagen_url ? (\Illuminate\Support\Str::startsWith($product->imagen_url, ['http://','https://','//']) ? $product->imagen_url : asset(ltrim($product->imagen_url, '/'))) : '' }}" alt="{{ $product->nombre }}" class="product-image" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.style.display='none';">
              </div>
              <div class="product-info">
                <div class="product-category">{{ $product->categoria ?? 'General' }}</div>
                <h3 class="product-name">{{ $product->nombre }}</h3>
                <div class="product-store">🏪 {{ $store->nombre_negocio ?? 'Comercio local' }}</div>
                <div class="product-description">{{ \Illuminate\Support\Str::limit($product->descripcion, 120) }}</div>
                <div class="product-meta" style="display:flex;gap:12px;flex-wrap:wrap;margin-top:10px;color:#555;font-size:0.95em;">
                  <span>Existencias: {{ $product->stock > 0 ? $product->stock : 'sin existencias' }}</span>
                </div>
                <div class="product-footer">
                  <div class="product-price">${{ number_format($product->precio, 2) }}</div>
                  <div class="product-rating">⭐ {{ number_format($product->avg_rating ?? 0, 2) }} <span>({{ $product->reviews_count ?? 0 }})</span></div>
                </div>
                <div style="margin-top: 12px;">
                  <button class="btn btn-primary" onclick="event.stopPropagation(); addToCartById({{ $product->id_producto }});" {{ $product->stock <= 0 ? 'disabled style="opacity:.6;cursor:not-allowed;"' : '' }}>{{ $product->stock > 0 ? 'Agregar al Carrito' : 'Agotado' }}</button>
                </div>
              </div>
            </div>
          @endforeach
        @else
          <p style="grid-column: 1/-1; text-align: center; color: #666;">No hay productos publicados.</p>
        @endif
      </div>
      <div id="productsPagination" style="display: flex; justify-content: center; align-items: center; gap: 12px; margin-top: 25px;"></div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  const pageStores = @json($comercios);
  const pageProducts = @json($productos);

  function initializeProductosPage() {
    if (!window.MarketplaceApp) {
      setTimeout(initializeProductosPage, 50);
      return;
    }

    MarketplaceApp.comercios = pageStores.map(store => ({
      id: store.id_vendedor,
      nombre: store.nombre_negocio,
      ubicacion: store.ubicacion || 'Ubicación no disponible',
      telefono: store.telefono || '(Sin teléfono)',
      email: store.email || 'info@marketplace.local',
      descripcion: store.descripcion || 'Comercio local',
      rating: parseFloat(store.avg_rating) || 0,
      foto: '',
    }));

    MarketplaceApp.productos = pageProducts.map(product => ({
      id: product.id_producto,
      nombre: product.nombre,
      descripcion: product.descripcion,
      precio: parseFloat(product.precio),
      stock: product.stock,
      foto: product.imagen_url || '',
      comercioId: product.id_vendedor,
      categoria: product.categoria || 'General',
      ubicacion: MarketplaceApp.comercios.find(c => c.id === product.id_vendedor)?.ubicacion || 'Local',
      rating: parseFloat(product.avg_rating) || 0,
      vendidos: product.reviews_count || 0,
    }));
    MarketplaceApp.applyStockAdjustments(MarketplaceApp.productos);

    const query = getSearchQuery();
    if (query) {
      const searchInput = document.getElementById('searchInput');
      if (searchInput) {
        searchInput.value = query;
      }
    }

    applyFilters();
    if (typeof updateUserDisplay === 'function') {
      updateUserDisplay();
    }
    if (typeof updateCartBadge === 'function') {
      updateCartBadge();
    }
  }

  let currentProductPage = 1;
  const productsPerPage = 15;

  function limitText(text, maxLength = 120) {
    const value = String(text || '');
    return value.length > maxLength ? value.slice(0, maxLength).trimEnd() + '...' : value;
  }

  function createProductCard(product) {
    const comercio = MarketplaceApp.getComercioById(product.comercioId) || { nombre: 'Comercio local' };
      const isOutOfStock = Number(product.stock) <= 0;
      const stockLabel = isOutOfStock ? 'sin existencias' : product.stock;
      return `
      <div class="product-card product-card-item" onclick="goToProductDetail(${product.id})">
        <div style="width: 100%; height: 180px; background: white; display: flex; align-items: center; justify-content: center; overflow: hidden;">
          <img src="${product.foto}" alt="${product.nombre}" class="product-image" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.style.display='none';">
        </div>
        <div class="product-info">
          <div class="product-category">Categoría: ${product.categoria}</div>
          <h3 class="product-name">${product.nombre}</h3>
          <div class="product-store">🏪 ${comercio.nombre}</div>
          <div class="product-description">${limitText(product.descripcion)}</div>
          <div class="product-meta" style="display:flex;gap:12px;flex-wrap:wrap;margin-top:10px;color:#555;font-size:0.95em;">
            <span>Existencias: ${stockLabel}</span>
          </div>
          <div class="product-footer">
            <div>
              <div class="product-price">$${product.precio.toFixed(2)}</div>
              <div class="product-rating">⭐ ${product.rating} <span>(${product.vendidos})</span></div>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <button class="btn btn-primary" onclick="event.stopPropagation(); goToProductDetail(${product.id})">Ver producto</button>
              <button class="btn btn-secondary" style="background:#f4f4f9; color:#333;" onclick="event.stopPropagation(); goToStore(${product.comercioId})">Ver tienda</button>
              <button class="btn btn-primary" style="background:${isOutOfStock ? '#ccc' : '#4caf50'}; cursor:${isOutOfStock ? 'not-allowed' : 'pointer'};" onclick="event.stopPropagation(); ${isOutOfStock ? '' : `addToCartById(${product.id})`}" ${isOutOfStock ? 'disabled' : ''}>${isOutOfStock ? 'Agotado' : 'Agregar al Carrito'}</button>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  function renderProductsPage(products, page) {
    const totalPages = Math.max(1, Math.ceil(products.length / productsPerPage));
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    currentProductPage = page;

    const startIndex = (page - 1) * productsPerPage;
    const endIndex = startIndex + productsPerPage;
    const pageProducts = products.slice(startIndex, endIndex);

    document.getElementById('productsContainer').innerHTML = pageProducts.length ? pageProducts.map(createProductCard).join('') : '<p style="grid-column: 1/-1; text-align: center; color: #666;">No se encontraron productos.</p>';

    const paginationDiv = document.getElementById('productsPagination');
    if (totalPages > 1) {
      paginationDiv.innerHTML = `
        <button type="button" class="btn btn-secondary" ${page <= 1 ? 'disabled' : ''} onclick="changeProductPage(-1)">← Anteriores</button>
        <span style="color: #666; font-size: 0.95em;">Página ${page} de ${totalPages}</span>
        <button type="button" class="btn btn-secondary" ${page >= totalPages ? 'disabled' : ''} onclick="changeProductPage(1)">Siguientes →</button>
      `;
      paginationDiv.style.display = 'flex';
    } else {
      paginationDiv.innerHTML = '';
      paginationDiv.style.display = 'none';
    }
  }

  function changeProductPage(delta) {
    const query = getSearchQuery();
    const category = document.getElementById('filterCategory').value;
    const location = document.getElementById('filterLocation').value;
    const rating = parseFloat(document.getElementById('filterRating').value) || 0;
    const sortBy = document.getElementById('sortBy').value;
    const priceMin = parseFloat(document.getElementById('filterPriceMin').value) || 0;
    const priceMaxValue = parseFloat(document.getElementById('filterPriceMax').value);
    const priceMax = Number.isNaN(priceMaxValue) ? Infinity : priceMaxValue;

    let results = MarketplaceApp.productos.filter(product => {
      const matchesQuery = query ? product.nombre.toLowerCase().includes(query.toLowerCase()) || product.descripcion.toLowerCase().includes(query.toLowerCase()) || product.categoria.toLowerCase().includes(query.toLowerCase()) : true;
      const matchesCategory = !category || product.categoria === category;
      const matchesLocation = !location || product.ubicacion.toLowerCase().includes(location.toLowerCase());
      const matchesRating = rating === 0 || product.rating >= rating;
      const matchesPrice = product.precio >= priceMin && product.precio <= priceMax;
      return matchesQuery && matchesCategory && matchesLocation && matchesRating && matchesPrice;
    });

    switch (sortBy) {
      case 'precio-asc':
        results.sort((a, b) => a.precio - b.precio);
        break;
      case 'precio-desc':
        results.sort((a, b) => b.precio - a.precio);
        break;
      case 'rating':
        results.sort((a, b) => b.rating - a.rating);
        break;
      case 'vendidos':
        results.sort((a, b) => b.vendidos - a.vendidos);
        break;
    }

    renderProductsPage(results, currentProductPage + delta);
  }

  function updateResultCount(products) {
    const countText = products.length > 0 ? `Mostrando ${products.length} productos` : 'No se encontraron productos';
    document.getElementById('resultCount').textContent = countText;
  }

  function getQueryParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name) || '';
  }

  function getSearchQuery() {
    return getQueryParam('search') || '';
  }

  function applyFilters() {
    const query = getSearchQuery();
    const category = document.getElementById('filterCategory').value;
    const location = document.getElementById('filterLocation').value;
    const rating = parseFloat(document.getElementById('filterRating').value) || 0;
    const sortBy = document.getElementById('sortBy').value;
    const priceMin = parseFloat(document.getElementById('filterPriceMin').value) || 0;
    const priceMaxValue = parseFloat(document.getElementById('filterPriceMax').value);
    const priceMax = Number.isNaN(priceMaxValue) ? Infinity : priceMaxValue;

    let results = MarketplaceApp.productos.filter(product => {
      const matchesQuery = query ? product.nombre.toLowerCase().includes(query.toLowerCase()) || product.descripcion.toLowerCase().includes(query.toLowerCase()) || product.categoria.toLowerCase().includes(query.toLowerCase()) : true;
      const matchesCategory = !category || product.categoria === category;
      const matchesLocation = !location || product.ubicacion.toLowerCase().includes(location.toLowerCase());
      const matchesRating = rating === 0 || product.rating >= rating;
      const matchesPrice = product.precio >= priceMin && product.precio <= priceMax;
      return matchesQuery && matchesCategory && matchesLocation && matchesRating && matchesPrice;
    });

    switch (sortBy) {
      case 'precio-asc':
        results.sort((a, b) => a.precio - b.precio);
        break;
      case 'precio-desc':
        results.sort((a, b) => b.precio - a.precio);
        break;
      case 'rating':
        results.sort((a, b) => b.rating - a.rating);
        break;
      case 'vendidos':
        results.sort((a, b) => b.vendidos - a.vendidos);
        break;
    }

    renderProductsPage(results, 1);
    updateResultCount(results);
  }

  function resetFilters() {
    document.getElementById('filterCategory').value = '';
    document.getElementById('filterLocation').value = '';
    document.getElementById('filterRating').value = '';
    document.getElementById('filterPriceMin').value = '';
    document.getElementById('filterPriceMax').value = '';
    applyFilters();
  }

  document.addEventListener('DOMContentLoaded', function() {
    initializeProductosPage();
  });
</script>
@endpush
