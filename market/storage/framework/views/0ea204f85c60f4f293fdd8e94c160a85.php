<?php $__env->startSection('title', 'Detalle del Producto - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <div class="product-detail-container">
    <div class="product-detail-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 40px; padding: 40px;">
      <div>
        <img src="<?php echo e($producto->imagen_url ? (\Illuminate\Support\Str::startsWith($producto->imagen_url, ['http://','https://','//']) ? $producto->imagen_url : asset(ltrim($producto->imagen_url, '/'))) : asset('imagenes/blusa.png')); ?>" alt="<?php echo e($producto->nombre); ?>" id="productImage" class="product-image-large" onerror="this.onerror=null;this.src='<?php echo e(asset('imagenes/blusa.png')); ?>';">
        <div class="product-gallery" id="productGallery"></div>
      </div>
      <div class="product-details">
        <h1 id="productName"><?php echo e($producto->nombre); ?></h1>
        <div class="product-meta" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
          <div class="meta-item"><strong>Precio:</strong> <span id="productPrice">$<?php echo e(number_format($producto->precio, 2)); ?></span></div>
          <div class="meta-item"><strong>Categoría:</strong> <span id="productCategory"><?php echo e($producto->categoria ?? 'General'); ?></span></div>
          <div class="meta-item"><strong>Existencias:</strong> <span id="productStock"><?php echo e($producto->stock > 0 ? $producto->stock : 'sin existencias'); ?></span></div>
          <div class="meta-item"><strong>Ubicación:</strong> <span id="productLocation"><?php echo e($comercio->ubicacion); ?></span></div>
        </div>
        <div class="store-info" style="background: #f8f8f8; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
          <h3>Comercio</h3>
          <p id="storeName"><?php echo e($comercio->nombre_negocio); ?></p>
          <p id="storeLocation">Ubicación: <?php echo e($comercio->ubicacion); ?></p>
          <p id="storeContact">Correo: <?php echo e($comercio->vendedor_correo ?? 'No disponible'); ?></p>
          <p id="storeContact">Vendedor: <?php echo e($comercio->vendedor_nombre ?? 'No disponible'); ?></p>
          <p id="storeDescription"><?php echo e($comercio->descripcion ?? 'Descripción no disponible'); ?></p>
        </div>
        <div class="quantity-selector" style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
          <label for="productQuantity">Cantidad</label>
          <input type="number" id="productQuantity" value="1" min="1" style="width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        </div>
        <div class="price-section" style="margin-bottom: 30px;">
          <div class="price-large" id="productPriceLarge">$<?php echo e(number_format($producto->precio, 2)); ?></div>
        </div>
        <div class="actions" style="display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap;">
          <button class="btn btn-primary" id="btnAddToCart">Agregar al Carrito</button>
          <button class="btn btn-secondary" onclick="goToCart()">Ver Carrito</button>
        </div>
        <div class="description-section" style="margin-top: 40px; padding-top: 40px; border-top: 2px solid #eee;">
          <h2>Descripción</h2>
          <p id="productDescription" style="color: #666; line-height: 1.8;"><?php echo e($producto->descripcion); ?></p>
        </div>

        <div class="reviews-section" style="margin-top: 30px;">
          <h2>Reseñas</h2>
          <div class="rating-summary" style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div id="avgRatingStars" style="font-size:20px; color:#f5b50a;"></div>
            <div style="color:#666;">Promedio: <span id="avgRatingValue"><?php echo e(round($avgRating,2)); ?></span> / 5</div>
            <div style="color:#999; font-size:13px;">(<span id="reviewsCount"><?php echo e($reviewsCount ?? 0); ?></span> reseñas)</div>
          </div>
          <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
          <?php endif; ?>

          <?php if(!empty($reviews) && count($reviews) > 0): ?>
            <ul class="reviews-list" style="list-style: none; padding: 0;">
              <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li style="border-bottom: 1px solid #eee; padding: 12px 0;">
                  <div style="font-weight: 600;">Calificación: <?php echo e($r->rating); ?> / 5</div>
                  <div style="color:#666;"><?php echo e($r->comment); ?></div>
                  <div style="font-size: 12px; color:#999;">Publicado: <?php echo e(date('d/m/Y H:i', strtotime($r->created_at))); ?></div>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          <?php else: ?>
            <p>No hay reseñas todavía. Sé el primero en opinar.</p>
          <?php endif; ?>

          <?php if(session('user') && session('user')['id_usuario'] === $producto->id_vendedor): ?>
            <div style="margin-top:20px; padding: 15px; background: #f5f5f5; border-radius: 8px; border-left: 4px solid #667eea;">
              <p style="margin: 0; color: #666;"><strong>ℹ️ Información:</strong> No puedes reseñar tu propio producto.</p>
            </div>
          <?php elseif(session('user') && $userAlreadyReviewed): ?>
            <div style="margin-top:20px; padding: 15px; background: #f5f5f5; border-radius: 8px; border-left: 4px solid #667eea;">
              <p style="margin: 0; color: #666;"><strong>✓ Ya reseñaste este producto</strong><br>Solo puedes dejar una reseña por producto.</p>
            </div>
          <?php elseif(session('user')): ?>
            <div class="review-form" style="margin-top:20px;">
              <h3>Escribir una reseña</h3>
              <form id="reviewForm" method="POST" action="/producto/<?php echo e($producto->id_producto); ?>/review">
                <?php echo csrf_field(); ?>
                <div style="margin-bottom:8px;">
                  <label>Calificación</label>
                  <div id="starRating" style="display:inline-block; margin-left:8px;">
                    <span class="star" data-value="1">☆</span>
                    <span class="star" data-value="2">☆</span>
                    <span class="star" data-value="3">☆</span>
                    <span class="star" data-value="4">☆</span>
                    <span class="star" data-value="5">☆</span>
                  </div>
                </div>
                <div style="margin-bottom:8px;">
                  <label>Comentario</label><br>
                  <textarea name="comment" id="reviewComment" rows="3" style="width:100%;"></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Enviar reseña</button>
              </form>
            </div>
          <?php else: ?>
            <div style="margin-top:20px; padding: 15px; background: #f5f5f5; border-radius: 8px; border-left: 4px solid #667eea;">
              <p style="margin: 0; color: #666;"><a href="/registro" style="color: #667eea; text-decoration: underline;"><strong>Inicia sesión</strong></a> para dejar una reseña.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const product = {
      id: <?php echo e($producto->id_producto); ?>,
      nombre: `<?php echo e(addslashes($producto->nombre)); ?>`,
      descripcion: `<?php echo e(addslashes($producto->descripcion)); ?>`,
      precio: parseFloat(<?php echo e($producto->precio); ?>),
      foto: `<?php echo e($producto->imagen_url ?? '/imagenes/blusa.png'); ?>`,
      categoria: `<?php echo e(addslashes($producto->categoria ?? 'General')); ?>`,
      ubicacion: `<?php echo e(addslashes($comercio->ubicacion)); ?>`,
      comercioId: <?php echo e($comercio->id_vendedor); ?>,
      stock: parseInt(<?php echo e($producto->stock ?? 0); ?>, 10) || 0,
    };

    const btnAdd = document.getElementById('btnAddToCart');
    if (btnAdd) {
      if (product.stock <= 0) {
        btnAdd.disabled = true;
        btnAdd.textContent = 'Agotado';
        btnAdd.style.opacity = '0.6';
        btnAdd.style.cursor = 'not-allowed';
      }

      btnAdd.addEventListener('click', function() {
        const quantity = parseInt(document.getElementById('productQuantity').value, 10) || 1;
        try {
          if (window.MarketplaceApp && typeof window.MarketplaceApp.addToCart === 'function') {
            window.MarketplaceApp.addToCart(product, quantity);
          } else if (typeof window.addToCart === 'function') {
            window.addToCart(product, quantity);
          }
        } catch (err) {
          console.error('Error agregando al carrito', err);
        }
        if (window.updateCartBadge) updateCartBadge();
      });
    }

    updateUserDisplay();
    updateCartBadge();

    // --- Review stars and AJAX submission ---
    const starInit = (function() {
      const starEls = document.querySelectorAll('#starRating .star');
      if (!starEls || !starEls.length) return;
      let selectedRating = 5;

      // Render average stars summary
      function renderAvgStars(avg) {
        const container = document.getElementById('avgRatingStars');
        if (!container) return;
        const rounded = Math.round(avg || 0);
        container.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
          const star = document.createElement('span');
          star.textContent = i <= rounded ? '★' : '☆';
          star.style.marginRight = '2px';
          container.appendChild(star);
        }
      }

      // initialize with server values
      renderAvgStars(<?php echo e(round($avgRating,2)); ?>);

      function fillStars(r) {
        starEls.forEach(s => {
          const val = parseInt(s.getAttribute('data-value'), 10);
          s.textContent = val <= r ? '★' : '☆';
          s.style.color = val <= r ? '#f5b50a' : '#666';
          s.style.fontSize = '22px';
          s.style.cursor = 'pointer';
          s.style.marginRight = '4px';
        });
      }

      starEls.forEach(s => {
        s.addEventListener('mouseover', () => fillStars(parseInt(s.getAttribute('data-value'), 10)));
        s.addEventListener('mouseout', () => fillStars(selectedRating));
        s.addEventListener('click', () => {
          selectedRating = parseInt(s.getAttribute('data-value'), 10);
          fillStars(selectedRating);
        });
      });

      fillStars(selectedRating);

      const reviewForm = document.getElementById('reviewForm');
      if (!reviewForm) return;

      reviewForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!window.currentSessionUser) { window.location.href = '/registro'; return; }
        const comment = document.getElementById('reviewComment').value;
        const payload = { rating: selectedRating, comment };
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
          const res = await fetch(reviewForm.action, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
          });

          if (res.ok) {
            const data = await res.json();
            updateReviewsList(data.latest || []);
            if (typeof data.average !== 'undefined') {
              renderAvgStars(data.average);
              document.getElementById('avgRatingValue').textContent = parseFloat(data.average).toFixed(2);
            }
            if (typeof data.count !== 'undefined') {
              document.getElementById('reviewsCount').textContent = data.count;
            }
            showTemporaryMessage('Reseña enviada.');
            document.getElementById('reviewComment').value = '';
          } else {
            showTemporaryMessage('Error al enviar reseña.');
          }
        } catch (err) {
          console.error(err);
          showTemporaryMessage('Error de conexión.');
        }
      });

      function updateReviewsList(latest) {
        let ul = document.querySelector('.reviews-list');
        if (!ul) {
          ul = document.createElement('ul');
          ul.className = 'reviews-list';
          ul.style.listStyle = 'none';
          ul.style.padding = '0';
          const ref = document.querySelector('.review-form');
          ref.parentNode.insertBefore(ul, ref);
        }

        ul.innerHTML = latest.map(r => `
          <li style="border-bottom: 1px solid #eee; padding: 12px 0;">
            <div style="font-weight: 600;">Calificación: ${r.rating} / 5</div>
            <div style="color:#666;">${r.comment || ''}</div>
            <div style="font-size: 12px; color:#999;">Publicado: ${new Date(r.created_at).toLocaleString()}</div>
          </li>
        `).join('');
      }

      function showTemporaryMessage(msg) {
        const el = document.createElement('div');
        el.className = 'alert alert-success';
        el.textContent = msg;
        const reviews = document.querySelector('.reviews-section');
        reviews.insertBefore(el, reviews.firstChild);
        setTimeout(() => el.remove(), 3000);
      }
    })();
  });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\castr\OneDrive\Desktop\laravel\hola\ProjectMarket\market\resources\views/detalle-producto.blade.php ENDPATH**/ ?>