@extends('layouts.app')

@section('title', 'Marketplace Local - Inicio')

@section('content')
  <style>
    .products-grid {
      transition: transform 0.3s ease;
    }
    
    .product-card-item {
      flex: 0 0 calc(33.333% - 10px);
      min-width: 220px;
      max-width: 320px;
      transition: transform 0.2s ease;
    }
    
    .product-card-item:hover {
      transform: translateY(-5px);
    }
  </style>

  <section class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 20px; border-radius: 8px; margin-bottom: 40px; text-align: center;">
    <h2 style="font-size: 2.5em; margin-bottom: 15px;">Descubre Productos Locales</h2>
    <p style="font-size: 1.2em; margin-bottom: 30px; opacity: 0.9;">Apoya a tus negocios locales y encuentra lo que necesitas en tu comunidad</p>
    <button class="btn btn-primary" onclick="window.location.href='/productos'">Comenzar a Buscar</button>
  </section>

  <section class="categories-section">
    <h3 class="categories-title">📂 Categorías</h3>
    <div class="categories-grid" id="categoriesContainer"></div>
  </section>

  <section class="products-section">
    <h2 class="section-title">⭐ Productos Populares</h2>
    <div class="products-grid homepage-products-grid" style="display: grid; grid-template-columns: repeat(3, minmax(380px, 1fr)); gap: 15px; width: 100%;">
      @foreach($popularProducts as $product)
        <div class="product-card" onclick="goToProductDetail({{ $product->id_producto }})" style="cursor: pointer;">
          <div style="width: 100%; height: 220px; background: white; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <img src="{{ $product->imagen_url }}" alt="{{ $product->nombre }}" class="product-image" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.style.display='none';">
          </div>
          <div class="product-info">
            <div class="product-category">{{ $product->categoria ?? 'General' }}</div>
            <h3 class="product-name">{{ $product->nombre }}</h3>
            <div class="product-store">🏪 {{ $product->nombre_negocio }}</div>
            <div class="product-description">{{ $product->descripcion }}</div>
            <div class="product-footer">
              <div class="product-price">${{ number_format($product->precio, 2) }}</div>
              <div class="product-rating">⭐ {{ number_format($product->avg_rating, 1) }} <span>({{ $product->reviews_count }} reseñas)</span></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>

  <section class="products-section">
    <h2 class="section-title">🆕 Productos Recientes</h2>
    <div class="products-grid homepage-products-grid" style="display: grid; grid-template-columns: repeat(3, minmax(380px, 1fr)); gap: 15px; width: 100%;">
      @foreach($recentProducts as $product)
        <div class="product-card" onclick="goToProductDetail({{ $product->id_producto }})" style="cursor: pointer;">
          <div style="width: 100%; height: 220px; background: white; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <img src="{{ $product->imagen_url }}" alt="{{ $product->nombre }}" class="product-image" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.style.display='none';">
          </div>
          <div class="product-info">
            <div class="product-category">{{ $product->categoria ?? 'General' }}</div>
            <h3 class="product-name">{{ $product->nombre }}</h3>
            <div class="product-store">🏪 {{ $product->nombre_negocio }}</div>
            <div class="product-description">{{ $product->descripcion }}</div>
            <div class="product-footer">
              <div class="product-price">${{ number_format($product->precio, 2) }}</div>
              <div class="product-rating">⭐ {{ number_format($product->avg_rating, 1) }} <span>({{ $product->reviews_count }} reseñas)</span></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>

  <section class="products-section">
    <h2 class="section-title">🏪 Comercios Destacados</h2>
    <div class="store-grid" id="storesGrid"></div>
  </section>
@endsection

@push('scripts')
<script>
  const pageStores = @json($comercios);

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

    if (document.getElementById('storesGrid')) {
      document.getElementById('storesGrid').innerHTML = MarketplaceApp.comercios.map(store => `
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
      `).join('');
    }
  }

  document.addEventListener('DOMContentLoaded', initializeHomePage);
</script>
@endpush
