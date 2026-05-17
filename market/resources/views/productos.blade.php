@extends('layouts.app')

@section('title', 'Explorar Productos - Marketplace Local')

@section('content')
  <h1 style="margin-bottom: 30px; color: #333;">Explorar Productos</h1>

  <div style="display: grid; grid-template-columns: 250px 1fr; gap: 30px; margin-bottom: 40px;">
    <aside style="background: white; padding: 20px; border-radius: 8px; height: fit-content; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px;">Filtros</h3>

      <div class="filter-group">
        <label for="filterCategory">📁 Categoría</label>
        <select id="filterCategory" onchange="applyFilters()">
          <option value="">Todas las categorías</option>
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
        <input type="range" id="filterPrice" min="0" max="1000" value="1000" style="width: 100%; margin-bottom: 10px;" onchange="applyFilters()">
        <div style="display: flex; justify-content: space-between; font-size: 0.85em; color: #666;">
          <span>$0</span>
          <span id="priceDisplay">$1000</span>
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

      <div class="products-grid" id="productsContainer"></div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  function createProductCard(product) {
    const comercio = MarketplaceApp.getComercioById(product.comercioId);
    return `
      <div class="product-card" onclick="goToProductDetail(${product.id})">
        <img src="${product.foto}" alt="${product.nombre}" class="product-image">
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

  function updateResultCount(products) {
    const countText = products.length > 0 ? `Mostrando ${products.length} productos` : 'No se encontraron productos';
    document.getElementById('resultCount').textContent = countText;
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
    const priceMax = parseFloat(document.getElementById('filterPrice').value) || 1000;

    let results = MarketplaceApp.productos.filter(product => {
      const matchesQuery = query ? product.nombre.toLowerCase().includes(query.toLowerCase()) || product.descripcion.toLowerCase().includes(query.toLowerCase()) || product.categoria.toLowerCase().includes(query.toLowerCase()) : true;
      const matchesCategory = !category || product.categoria === category;
      const matchesLocation = !location || product.ubicacion.toLowerCase().includes(location.toLowerCase());
      const matchesRating = rating === 0 || product.rating >= rating;
      const matchesPrice = product.precio <= priceMax;
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

    document.getElementById('productsContainer').innerHTML = results.length ? results.map(createProductCard).join('') : '<p style="grid-column: 1/-1; text-align: center; color: #666;">No se encontraron productos.</p>';
    updateResultCount(results);
  }

  function resetFilters() {
    document.getElementById('filterCategory').value = '';
    document.getElementById('filterLocation').value = '';
    document.getElementById('filterRating').value = '';
    document.getElementById('filterPrice').value = 1000;
    document.getElementById('priceDisplay').textContent = '$1000';
    applyFilters();
  }

  document.addEventListener('DOMContentLoaded', function() {
    const query = getSearchQuery();
    if (query) {
      document.getElementById('searchInput').value = query;
    }
    document.getElementById('filterPrice').addEventListener('input', function() {
      document.getElementById('priceDisplay').textContent = '$' + this.value;
    });

    applyFilters();
    updateUserDisplay();
    updateCartBadge();
  });
</script>
@endpush