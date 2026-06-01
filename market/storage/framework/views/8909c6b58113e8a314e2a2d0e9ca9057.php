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
          <option value="4.5">4.5 y superior</option>
          <option value="4">4.0 y superior</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="sortBy">Ordenar por</label>
        <select id="sortBy" onchange="applyFilters()">
          <option value="nombre">Nombre (A-Z)</option>
          <option value="rating">Mejor Calificados</option>
        </select>
      </div>
      <div class="filter-group" style="justify-content: flex-end; align-self: end;">
        <button class="btn btn-primary" onclick="resetFilters()">Limpiar Filtros</button>
      </div>
    </div>
  </div>

  <div class="store-grid" id="storesContainer"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
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
    const query = getQueryParam('search') || document.getElementById('filterLocation').value;
    const minRating = parseFloat(document.getElementById('filterRating').value) || 0;
    const sortBy = document.getElementById('sortBy').value;

    let results = MarketplaceApp.comercios.filter(store => {
      const matchesQuery = !query || store.nombre.toLowerCase().includes(query.toLowerCase()) || store.descripcion.toLowerCase().includes(query.toLowerCase());
      const matchesRating = minRating === 0 || store.rating >= minRating;
      return matchesQuery && matchesRating;
    });

    if (sortBy === 'rating') {
      results.sort((a, b) => b.rating - a.rating);
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

  document.addEventListener('DOMContentLoaded', function() {
    applyFilters();
    updateUserDisplay();
    updateCartBadge();
  });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Desktop\asdf\ProjectMarket\market\resources\views/comercios.blade.php ENDPATH**/ ?>