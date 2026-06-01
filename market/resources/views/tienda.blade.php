@extends('layouts.app')

@section('title', 'Tienda - Marketplace Local')

@section('content')
  <div class="store-header" id="storeHeader">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 20px; border-radius: 8px; margin-bottom: 40px; text-align: center;">
      <h1>{{ $comercio->nombre_negocio }}</h1>
      <p>{{ $comercio->descripcion }}</p>
      <div class="store-header-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
        <div class="info-box"><strong>⭐ Calificación</strong> 4.7/5.0</div>
        <div class="info-box"><strong>📍 Ubicación</strong> {{ $comercio->ubicacion ?? 'Ubicación no disponible' }}</div>
        <div class="info-box"><strong>📦 Productos</strong> {{ $productos->count() }} disponibles</div>
        <div class="info-box"><strong>📞 Contacto</strong> {{ $comercio->telefono ?? '(Sin teléfono)' }}</div>
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
      @forelse ($productos as $producto)
        <div class="product-card" onclick="goToProductDetail({{ $producto->id_producto }})">
          <img src="{{ $producto->imagen_url ? (\Illuminate\Support\Str::startsWith($producto->imagen_url, ['http://','https://','//']) ? $producto->imagen_url : asset(ltrim($producto->imagen_url, '/'))) : asset('imagenes/blusa.png') }}" alt="{{ $producto->nombre }}" class="product-image" onerror="this.onerror=null;this.src='{{ asset('imagenes/blusa.png') }}';">
          <div class="product-info">
            <div class="product-category">{{ $producto->categoria ?? 'General' }}</div>
            <h3 class="product-name">{{ $producto->nombre }}</h3>
            <div class="product-store">🏪 {{ $comercio->nombre_negocio }}</div>
            <div class="product-description">{{ $producto->descripcion }}</div>
            <div class="product-meta" style="display:flex;gap:12px;flex-wrap:wrap;margin-top:10px;color:#555;font-size:0.95em;">
              <span>Existencias: {{ $producto->stock > 0 ? $producto->stock : 'sin existencias' }}</span>
            </div>
            <div class="product-footer">
              <div class="product-price">${{ number_format($producto->precio, 2) }}</div>
              <div class="product-rating">⭐ 4.5 <span>(0)</span></div>
            </div>
            <div style="margin-top: 12px;">
              <button class="btn btn-primary" onclick="event.stopPropagation(); addToCartById({{ $producto->id_producto }});" {{ $producto->stock <= 0 ? 'disabled style="opacity:.6;cursor:not-allowed;"' : '' }}>{{ $producto->stock > 0 ? 'Agregar al Carrito' : 'Agotado' }}</button>
            </div>
          </div>
        </div>
      @empty
        <p style="text-align: center; grid-column: 1/-1; color: #666;">No se encontraron productos</p>
      @endforelse
    </div>
  </section>
@endsection

@push('scripts')
<script>
  const store = @json($comercio);
  const pageProducts = @json($productos);

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
      stock: Number(product.stock || 0),
      foto: product.imagen_url || '/imagenes/blusa.png',
      comercioId: product.id_vendedor,
      categoria: product.categoria || 'General',
      ubicacion: store.ubicacion || 'Local',
      rating: 4.5,
      vendidos: 0,
    }));
    MarketplaceApp.applyStockAdjustments(MarketplaceApp.productos);

    initializePage();
  }

  function createProductCard(product) {
    return `
      <div class="product-card" onclick="goToProductDetail(${product.id})">
        <img src="${product.foto}" alt="${product.nombre}" class="product-image" onerror="this.onerror=null;this.src='/imagenes/blusa.png';">
        <div class="product-info">
          <div class="product-category">${product.categoria}</div>
          <h3 class="product-name">${product.nombre}</h3>
          <div class="product-store">🏪 ${store.nombre_negocio}</div>
          <div class="product-description">${product.descripcion}</div>
          <div class="product-meta" style="display:flex;gap:12px;flex-wrap:wrap;margin-top:10px;color:#555;font-size:0.95em;">
            <span>Existencias: ${product.stock > 0 ? product.stock : 'sin existencias'}</span>
          </div>
          <div class="product-footer">
            <div class="product-price">$${product.precio.toFixed(2)}</div>
            <div class="product-rating">⭐ ${product.rating} <span>(${product.vendidos})</span></div>
          </div>
          <div style="margin-top: 12px;">
            <button class="btn btn-primary" style="background:${product.stock > 0 ? '#4caf50' : '#ccc'}; cursor:${product.stock > 0 ? 'pointer' : 'not-allowed'};" onclick="event.stopPropagation(); ${product.stock > 0 ? `addToCartById(${product.id})` : ''}" ${product.stock > 0 ? '' : 'disabled'}>${product.stock > 0 ? 'Agregar al Carrito' : 'Agotado'}</button>
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
@endpush
