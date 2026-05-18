@extends('layouts.app')

@section('title', 'Detalle del Producto - Marketplace Local')

@section('content')
  <div class="product-detail-container">
    <div class="product-detail-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 40px; padding: 40px;">
      <div>
        <img src="{{ $producto->imagen_url ?? '/imagenes/blusa.png' }}" alt="{{ $producto->nombre }}" id="productImage" class="product-image-large" onerror="this.onerror=null;this.src='/imagenes/blusa.png';">
        <div class="product-gallery" id="productGallery"></div>
      </div>
      <div class="product-details">
        <h1 id="productName">{{ $producto->nombre }}</h1>
        <div class="product-meta" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
          <div class="meta-item"><strong>Precio:</strong> <span id="productPrice">${{ number_format($producto->precio, 2) }}</span></div>
          <div class="meta-item"><strong>Categoría:</strong> <span id="productCategory">General</span></div>
          <div class="meta-item"><strong>Ubicación:</strong> <span id="productLocation">{{ $comercio->ubicacion }}</span></div>
        </div>
        <div class="store-info" style="background: #f8f8f8; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
          <h3>Comercio</h3>
          <p id="storeName">{{ $comercio->nombre_negocio }}</p>
          <p id="storeLocation">Ubicación: {{ $comercio->ubicacion }}</p>
          <p id="storeContact">Contacto: {{ $comercio->telefono ?? 'N/A' }}</p>
        </div>
        <div class="quantity-selector" style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
          <label for="productQuantity">Cantidad</label>
          <input type="number" id="productQuantity" value="1" min="1" style="width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        </div>
        <div class="price-section" style="margin-bottom: 30px;">
          <div class="price-large" id="productPriceLarge">${{ number_format($producto->precio, 2) }}</div>
        </div>
        <div class="actions" style="display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap;">
          <button class="btn btn-primary" id="btnAddToCart">Agregar al Carrito</button>
          <button class="btn btn-secondary" onclick="goToCart()">Ver Carrito</button>
        </div>
        <div class="description-section" style="margin-top: 40px; padding-top: 40px; border-top: 2px solid #eee;">
          <h2>Descripción</h2>
          <p id="productDescription" style="color: #666; line-height: 1.8;">{{ $producto->descripcion }}</p>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const product = {
      id: {{ $producto->id_producto }},
      nombre: `{{ addslashes($producto->nombre) }}`,
      descripcion: `{{ addslashes($producto->descripcion) }}`,
      precio: parseFloat({{ $producto->precio }}),
      foto: `{{ $producto->imagen_url ?? '/imagenes/blusa.png' }}`,
      categoria: 'General',
      ubicacion: `{{ addslashes($comercio->ubicacion) }}`,
      comercioId: {{ $comercio->id_vendedor }},
    };

    document.getElementById('btnAddToCart').addEventListener('click', function() {
      const quantity = parseInt(document.getElementById('productQuantity').value, 10) || 1;
      for (let i = 0; i < quantity; i++) {
        MarketplaceApp.addToCart(product);
      }
      updateCartBadge();
    });

    updateUserDisplay();
    updateCartBadge();
  });
</script>
@endpush
