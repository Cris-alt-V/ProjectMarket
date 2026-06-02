<?php $__env->startSection('title', 'Comercios - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <h1 style="margin-bottom: 30px; color: #333;">Nuestros Comercios</h1>

  <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div class="filters-grid">
      <div class="filter-group">
        <label for="filterLocation">📍 Ubicación</label>
        <input type="text" id="filterLocation" placeholder="Buscar por ubicación..." onchange="applyFilters()">
      </div>
      <div class="filter-group">
        <label for="filterRating">⭐ Calificación</label>
        <select id="filterRating" onchange="applyFilters()">
          <option value="">Todas</option>
          <option value="5">5.0 y superior</option>
          <option value="4.5">4.5 y superior</option>
          <option value="4">4.0 y superior</option>
          <option value="3.5">3.5 y superior</option>
          <option value="3">3.0 y superior</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="sortBy">Ordenar por</label>
        <select id="sortBy" onchange="applyFilters()">
          <option value="nombre">Nombre (A-Z)</option>
          <option value="nombre_desc">Nombre (Z-A)</option>
          <option value="rating">Rating (Mayor primero)</option>
          <option value="rating_asc">Rating (Menor primero)</option>
          <option value="ubicacion">Ubicación</option>
        </select>
      </div>
      <div class="filter-group" style="justify-content: flex-end; align-self: end;">
        <button class="btn btn-primary" onclick="resetFilters()">Limpiar Filtros</button>
      </div>
    </div>
  </div>

  <div class="store-grid" id="storesContainer">
    <?php $__empty_1 = true; $__currentLoopData = $comercios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="store-card" onclick="goToStore(<?php echo e($store->id_vendedor); ?>)">
        <div style="background: #f5f5f5; border-radius: 8px; padding: 20px; text-align: center; font-size: 2em;">🏪</div>
        <div style="margin-top: 15px;">
          <h3 class="store-name"><?php echo e($store->nombre_negocio ?? 'Comercio local'); ?></h3>
          <div class="store-location">📍 <?php echo e($store->ubicacion ?? 'Ubicación no disponible'); ?></div>
          <p class="store-description"><?php echo e($store->descripcion ?? 'Descripción no disponible'); ?></p>
          <div class="product-footer" style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
            <div class="store-rating">⭐ <?php echo e(number_format($store->avg_rating ?? 0, 1)); ?></div>
            <button class="btn btn-small btn-primary" onclick="event.stopPropagation(); goToStore(<?php echo e($store->id_vendedor); ?>)">Ver Tienda</button>
          </div>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p style="color: #666;">No hay comercios disponibles.</p>
    <?php endif; ?>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  const pageStores = <?php echo json_encode($comercios, 15, 512) ?>;

  function initializeComerciosPage() {
    if (!window.MarketplaceApp) {
      setTimeout(initializeComerciosPage, 50);
      return;
    }

    MarketplaceApp.comercios = pageStores.map(store => ({
      id: store.id_vendedor,
      nombre: store.nombre_negocio || store.nombre || 'Comercio local',
      ubicacion: store.ubicacion || 'Ubicación no disponible',
      descripcion: store.descripcion || 'Descripción no disponible',
      rating: parseFloat(store.avg_rating) || 0,
      foto: store.imagen_url || '',
    }));

    const initialQuery = getQueryParam('search');
    if (initialQuery) {
      const filterInput = document.getElementById('filterLocation');
      if (filterInput) {
        filterInput.value = initialQuery;
      }
    }

    applyFilters();
    updateUserDisplay();
    updateCartBadge();
  }

  function getQueryParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name) || '';
  }

  function createStoreCard(store) {
    return `
      <div class="store-card" onclick="goToStore(${store.id})">
        <div style="background: #f5f5f5; border-radius: 8px; padding: 20px; text-align: center; font-size: 2em;">🏪</div>
        <div style="margin-top: 15px;">
          <h3 class="store-name">${store.nombre}</h3>
          <div class="store-location">📍 ${store.ubicacion}</div>
          <p class="store-description">${store.descripcion}</p>
          <div class="product-footer" style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
            <div class="store-rating">⭐ ${store.rating}</div>
            <button class="btn btn-small btn-primary" onclick="event.stopPropagation(); goToStore(${store.id})">Ver Tienda</button>
          </div>
        </div>
      </div>
    `;
  }

  function applyFilters() {
    const query = document.getElementById('filterLocation').value.trim() || getQueryParam('search');
    const minRating = parseFloat(document.getElementById('filterRating').value) || 0;
    const sortBy = document.getElementById('sortBy').value;

    let results = MarketplaceApp.comercios.filter(store => {
      const normalizedQuery = query.toLowerCase();
      const matchesQuery = !normalizedQuery || store.nombre.toLowerCase().includes(normalizedQuery) || store.descripcion.toLowerCase().includes(normalizedQuery) || store.ubicacion.toLowerCase().includes(normalizedQuery);
      const matchesRating = minRating === 0 || store.rating >= minRating;
      return matchesQuery && matchesRating;
    });

    if (sortBy === 'rating') {
      results.sort((a, b) => b.rating - a.rating);
    } else if (sortBy === 'rating_asc') {
      results.sort((a, b) => a.rating - b.rating);
    } else if (sortBy === 'nombre_desc') {
      results.sort((a, b) => b.nombre.localeCompare(a.nombre));
    } else if (sortBy === 'ubicacion') {
      results.sort((a, b) => a.ubicacion.localeCompare(b.ubicacion));
    } else {
      results.sort((a, b) => a.nombre.localeCompare(b.nombre));
    }

    document.getElementById('storesContainer').innerHTML = results.length ? results.map(createStoreCard).join('') : '<p style="color: #666;">No se encontraron comercios.</p>';
  }

  function resetFilters() {
    document.getElementById('filterLocation').value = '';
    document.getElementById('filterRating').value = '';
    document.getElementById('sortBy').value = 'nombre';
    applyFilters();
  }

  document.addEventListener('DOMContentLoaded', initializeComerciosPage);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\franc\OneDrive\Escritorio\sexopaye\ProjectMarket\market\resources\views/comercios.blade.php ENDPATH**/ ?>