@extends('layouts.app')

@section('title', 'Detalle del Producto - Marketplace Local')

@section('content')
  <div class="product-detail-container">
    <div class="product-detail-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 40px; padding: 40px;">
      <div>
        <img src="" alt="Producto" id="productImage" class="product-image-large" onerror="this.onerror=null;this.src='/imagenes/blusa.png';">
        <div class="product-gallery" id="productGallery"></div>
      </div>
      <div class="product-details">
        <h1 id="productName"></h1>
        <div class="product-meta" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
          <div class="meta-item"><strong>Precio:</strong> <span id="productPrice"></span></div>
          <div class="meta-item"><strong>Categoría:</strong> <span id="productCategory"></span></div>
          <div class="meta-item"><strong>Ubicación:</strong> <span id="productLocation"></span></div>
        </div>
        <div class="store-info" style="background: #f8f8f8; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
          <h3>Comercio</h3>
          <p id="storeName"></p>
          <p id="storeLocation"></p>
          <p id="storeContact"></p>
        </div>
        <div class="quantity-selector" style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
          <label for="productQuantity">Cantidad</label>
          <input type="number" id="productQuantity" value="1" min="1" style="width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        </div>
        <div class="price-section" style="margin-bottom: 30px;">
          <div class="price-large" id="productPriceLarge"></div>
        </div>
        <div class="actions" style="display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap;">
          <button class="btn btn-primary" id="btnAddToCart">Agregar al Carrito</button>
          <button class="btn btn-secondary" onclick="goToCart()">Ver Carrito</button>
        </div>
        <div class="description-section" style="margin-top: 40px; padding-top: 40px; border-top: 2px solid #eee;">
          <h2>Descripción</h2>
          <p id="productDescription" style="color: #666; line-height: 1.8;"></p>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const productId = getQueryParam('id');
    const product = MarketplaceApp.getProductById(productId);
    if (!product) {
      document.querySelector('.product-detail-container').innerHTML = '<p>Producto no encontrado.</p>';
      return;
    }

    const store = MarketplaceApp.getComercioById(product.comercioId);
    document.getElementById('productImage').src = product.foto;
    document.getElementById('productImage').alt = product.nombre;
    document.getElementById('productName').textContent = product.nombre;
    document.getElementById('productPrice').textContent = `$${product.precio.toFixed(2)}`;
    document.getElementById('productPriceLarge').textContent = `$${product.precio.toFixed(2)}`;
    document.getElementById('productCategory').textContent = product.categoria;
    document.getElementById('productLocation').textContent = product.ubicacion;
    document.getElementById('productDescription').textContent = product.descripcion;
    document.getElementById('storeName').textContent = store.nombre;
    document.getElementById('storeLocation').textContent = `Ubicación: ${store.ubicacion}`;
    document.getElementById('storeContact').textContent = `Contacto: ${store.email}`;

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
