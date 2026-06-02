<?php $__env->startSection('title', 'Mis Comercios - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <h1 style="margin-bottom: 30px; color: #333;">Gestionar mis Comercios</h1>

  <?php if(!session('user') || session('user')['tipo_usuario'] !== 'vendedor'): ?>
    <div id="loginPrompt" style="background: white; border-radius: 8px; padding: 40px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <h2>Por favor inicia sesión</h2>
      <p style="color: #666; margin-bottom: 20px;">Solo los vendedores registrados pueden gestionar sus comercios</p>
      <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
        <a href="/registro" class="btn btn-primary">Iniciar Sesión</a>
        <a href="/registro?tab=store" class="btn btn-secondary">Registrar Comercio</a>
      </div>
    </div>
  <?php else: ?>
    <div id="storesContent">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 10px;">
        <h2 style="margin: 0;">Mis Tiendas</h2>
        <button class="btn btn-primary" onclick="showAddProductForm()">Agregar Producto</button>
      </div>
      <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;">
          <h3 style="margin: 0 0 10px 0; font-size: 1.5em;">🏪 <?php echo e(session('user')['nombre_negocio'] ?? 'Mi Tienda'); ?></h3>
          <p style="margin: 0; opacity: 0.9;"><?php echo e(session('user')['descripcion'] ?? 'Descripción no disponible'); ?></p>
        </div>
        <div style="padding: 20px;">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 20px;">
            <div>
              <label style="color: #666; font-size: 0.9em;">Ubicación</label>
              <p style="margin: 5px 0; font-weight: bold;"><?php echo e(session('user')['ubicacion'] ?? 'No especificada'); ?></p>
            </div>
            <div>
              <label style="color: #666; font-size: 0.9em;">Correo</label>
              <p style="margin: 5px 0; font-weight: bold;"><?php echo e(session('user')['correo'] ?? 'No disponible'); ?></p>
            </div>
            <div>
              <label style="color: #666; font-size: 0.9em;">Propietario</label>
              <p style="margin: 5px 0; font-weight: bold;"><?php echo e(session('user')['nombre'] ?? 'No disponible'); ?></p>
            </div>
          </div>
        </div>
      </div>

      <h3 style="margin-bottom: 20px;">Mis Productos</h3>
      <?php if($productos->count() > 0): ?>
        <div id="productosGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
          <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="product-card-item" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
              <div style="width: 100%; height: 200px; background: white; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                <img src="<?php echo e($producto->imagen_url ? (\Illuminate\Support\Str::startsWith($producto->imagen_url, ['http://','https://','//']) ? $producto->imagen_url : asset(ltrim($producto->imagen_url, '/'))) : ''); ?>" alt="<?php echo e($producto->nombre); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.style.display='none';">
              </div>
              <div style="padding: 15px;">
                <h4 style="margin: 0 0 10px 0;"><?php echo e($producto->nombre); ?></h4>
                <p style="color: #666; font-size: 0.9em; margin: 5px 0;"><?php echo e(Str::limit($producto->descripcion, 100)); ?></p>
                <div style="margin: 10px 0; font-weight: bold; color: #667eea;">
                  $<?php echo e(number_format($producto->precio, 2)); ?>

                </div>
                <div style="margin: 10px 0; font-size: 0.9em; color: #666;">
                  Categoría: <?php echo e($producto->categoria ?? 'General'); ?>

                </div>
                <div style="margin: 10px 0; font-size: 0.9em; color: #666;">
                  Stock: <?php echo e($producto->stock); ?>

                </div>
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                  <button class="btn btn-primary" style="flex: 1;" onclick='editProduct(<?php echo json_encode($producto, 15, 512) ?>)'>Editar</button>
                  <form action="/productos/<?php echo e($producto->id_producto); ?>" method="POST" style="flex: 1;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger" style="width: 100%;" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div id="productPagination" style="display: flex; justify-content: center; align-items: center; gap: 12px; margin-top: 25px;">
          <button type="button" id="prevProductsBtn" class="btn btn-secondary">← Anteriores</button>
          <span id="productPageInfo" style="color: #666; font-size: 0.95em;">Página 1</span>
          <button type="button" id="nextProductsBtn" class="btn btn-secondary">Siguientes →</button>
        </div>
      <?php else: ?>
        <div style="background: #f0f0f0; border-radius: 8px; padding: 30px; text-align: center;">
          <p style="color: #666; margin: 0;">No tienes productos publicados. ¡Comienza ahora!</p>
        </div>
      <?php endif; ?>

      <!-- Formulario agregar producto -->
      <div id="productModalOverlay" style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0, 0, 0, 0.55); align-items: center; justify-content: center; padding: 12px; overflow-y: auto;">
        <div id="formAgregarProducto" style="width: 100%; max-width: 620px; background: white; border-radius: 12px; padding: 20px; box-shadow: 0 14px 50px rgba(0,0,0,0.25); position: relative; max-height: calc(100vh - 40px); overflow-y: auto;">
          <button type="button" onclick="cancelProductForm()" style="position: absolute; top: 18px; right: 18px; width: 34px; height: 34px; border-radius: 50%; border: none; background: #ccc; color: #333; font-size: 20px; line-height: 1; cursor: pointer;">×</button>
          <form id="productForm" action="/productos/crear" method="POST" enctype="multipart/form-data" style="display: grid; gap: 15px;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="imagen_url" id="imagenUrl" value="">
            <input type="hidden" id="editingProductId" value="">
          <div>
            <h3 id="formTitle" style="margin-top: 0;">Agregar Nuevo Producto</h3>
            <label>Nombre del Producto</label>
            <input type="text" id="productoNombre" name="nombre" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
          </div>
          <div>
            <label>Descripción</label>
            <textarea name="descripcion" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; height: 100px;"></textarea>
          </div>
          <div>
            <label>Categoría</label>
            <select id="productoCategoria" name="categoria" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
              <option value="">Selecciona categoría</option>
              <option value="General">General</option>
              <option value="Accesorios">Accesorios</option>
              <option value="Ropa">Ropa</option>
              <option value="Alimentos">Alimentos</option>
              <option value="Electrónica">Electrónica</option>
              <option value="Hogar">Hogar</option>
              <option value="Belleza">Belleza</option>
            </select>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
              <label>Precio</label>
              <input type="number" name="precio" required step="0.01" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
              <label>Stock</label>
              <input type="number" name="stock" required min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
          </div>
          <div>
            <label>Imagen del producto</label>
            <div id="imageDropArea" style="border: 2px dashed #ccc; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; color: #666;">
              <p style="margin: 0 0 10px 0;">Haz clic, arrastra o pega una imagen aquí</p>
              <p style="margin: 0; font-size: 0.9em;">También puedes usar el botón de archivo o pegar desde el portapapeles</p>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-top: 10px;">
              <button type="button" id="pasteImageButton" class="btn btn-secondary" style="flex: 1; min-width: 140px;">Pegar imagen</button>
              <span id="pasteStatus" style="color: #666; font-size: 0.9em; flex: 2;">Subir imagen desde el portapapeles.</span>
            </div>
            <input type="file" id="imagenFile" name="imagen_file" accept="image/*" style="display: none;">
            <input type="hidden" id="imagenData" name="imagen_data" value="">
            <div id="imagePreviewWrapper" style="margin-top: 15px; display: none; position: relative; border: 1px solid #ddd; border-radius: 8px; padding: 12px; background: #fafafa;">
              <label style="display: block; margin-bottom: 8px;">Vista previa</label>
              <button type="button" id="clearImageButton" onclick="clearImageInputs()" style="position: absolute; top: 10px; right: 10px; border: none; background: rgba(0,0,0,0.6); color: white; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; font-size: 16px; line-height: 1;">×</button>
              <img id="imagePreview" src="" alt="Vista previa de la imagen" style="width: 100%; max-height: 240px; object-fit: contain; background: transparent; border-radius: 8px; display: block; margin-top: 10px;">
            </div>
          </div>
          <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;" id="formSubmitButton">Guardar Producto</button>
            <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="cancelProductForm()">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  function showAddProductForm() {
    const form = document.getElementById('productForm');
    const formTitle = document.getElementById('formTitle');
    const formMethod = document.getElementById('formMethod');
    const editId = document.getElementById('editingProductId');
    const submitButton = document.getElementById('formSubmitButton');
    const nameInput = document.getElementById('productoNombre');
    const categorySelect = document.getElementById('productoCategoria');
    const descriptionInput = document.querySelector('textarea[name="descripcion"]');
    const priceInput = document.querySelector('input[name="precio"]');
    const stockInput = document.querySelector('input[name="stock"]');
    const imagenUrl = document.getElementById('imagenUrl');
    const modalOverlay = document.getElementById('productModalOverlay');

    formTitle.textContent = 'Agregar Nuevo Producto';
    formMethod.value = 'POST';
    editId.value = '';
    form.action = '/productos/crear';
    submitButton.textContent = 'Guardar Producto';

    nameInput.value = '';
    categorySelect.value = '';
    descriptionInput.value = '';
    priceInput.value = '';
    stockInput.value = '';
    imagenUrl.value = '';
    clearImageInputs();
    if (modalOverlay) {
      modalOverlay.style.display = 'flex';
    }
  }

  function editProduct(product) {
    const form = document.getElementById('productForm');
    const formTitle = document.getElementById('formTitle');
    const formMethod = document.getElementById('formMethod');
    const editId = document.getElementById('editingProductId');
    const submitButton = document.getElementById('formSubmitButton');
    const nameInput = document.getElementById('productoNombre');
    const categorySelect = document.getElementById('productoCategoria');
    const descriptionInput = document.querySelector('textarea[name="descripcion"]');
    const priceInput = document.querySelector('input[name="precio"]');
    const stockInput = document.querySelector('input[name="stock"]');
    const imagenUrl = document.getElementById('imagenUrl');
    const modalOverlay = document.getElementById('productModalOverlay');

    formTitle.textContent = 'Editar Producto';
    formMethod.value = 'PUT';
    editId.value = product.id_producto;
    submitButton.textContent = 'Actualizar Producto';
    form.action = '/productos/' + product.id_producto;

    nameInput.value = product.nombre || '';
    descriptionInput.value = product.descripcion || '';
    priceInput.value = product.precio || '';
    stockInput.value = product.stock || '';
    categorySelect.value = product.categoria || '';
    imagenUrl.value = product.imagen_url || '';
    updateImagePreview(product.imagen_url || '');
    if (modalOverlay) {
      modalOverlay.style.display = 'flex';
    }
    document.getElementById('imagenFile').value = null;
  }

  function cancelProductForm() {
    const modalOverlay = document.getElementById('productModalOverlay');
    if (modalOverlay) {
      modalOverlay.style.display = 'none';
    }
    clearImageInputs();
    const imagenUrl = document.getElementById('imagenUrl');
    if (imagenUrl) {
      imagenUrl.value = '';
    }
  }

  function updateImagePreview(src) {
    const wrapper = document.getElementById('imagePreviewWrapper');
    const preview = document.getElementById('imagePreview');
    if (! wrapper || !preview) {
      return;
    }

    if (src) {
      preview.src = src;
      wrapper.style.display = 'block';
    } else {
      preview.src = '';
      wrapper.style.display = 'none';
    }
  }

  function clearImageInputs() {
    const fileInput = document.getElementById('imagenFile');
    const dataInput = document.getElementById('imagenData');
    const imagenUrl = document.getElementById('imagenUrl');
    const pasteStatus = document.getElementById('pasteStatus');
    if (fileInput) {
      fileInput.value = null;
    }
    if (dataInput) {
      dataInput.value = '';
    }
    if (imagenUrl) {
      imagenUrl.value = '';
    }
    if (pasteStatus) {
      pasteStatus.textContent = 'Usa este botón para subir una imagen desde el portapapeles.';
    }
    updateImagePreview(null);
  }

  async function pasteClipboardImage() {
    const pasteStatus = document.getElementById('pasteStatus');
    const fileInput = document.getElementById('imagenFile');
    const dataInput = document.getElementById('imagenData');

    if (!navigator.clipboard || !navigator.clipboard.read) {
      if (pasteStatus) {
        pasteStatus.textContent = 'Tu navegador no soporta pegar imágenes directamente. Usa Ctrl+V en el área o selecciona un archivo.';
      }
      return;
    }

    try {
      const clipboardItems = await navigator.clipboard.read();
      for (const item of clipboardItems) {
        const imageType = item.types.find(type => type.startsWith('image/'));
        if (!imageType) {
          continue;
        }

        const blob = await item.getType(imageType);
        if (!blob) {
          continue;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          if (dataInput) {
            dataInput.value = e.target.result;
          }
          if (fileInput) {
            fileInput.value = null;
          }
          updateImagePreview(e.target.result);
          if (pasteStatus) {
            pasteStatus.textContent = 'Imagen pegada desde el portapapeles.';
          }
        };
        reader.readAsDataURL(blob);
        return;
      }

      if (pasteStatus) {
        pasteStatus.textContent = 'No se encontró una imagen en el portapapeles.';
      }
    } catch (error) {
      if (pasteStatus) {
        pasteStatus.textContent = 'Error al leer el portapapeles. Copia una imagen y vuelve a intentar.';
      }
      console.error('Clipboard paste error:', error);
    }
  }

  let currentProductPage = 1;
  const productsPerPage = 15;

  function renderProductPage(page) {
    const cards = Array.from(document.querySelectorAll('.product-card-item'));
    const prevBtn = document.getElementById('prevProductsBtn');
    const nextBtn = document.getElementById('nextProductsBtn');
    const pageInfo = document.getElementById('productPageInfo');
    const paginationWrapper = document.getElementById('productPagination');
    const totalPages = Math.max(1, Math.ceil(cards.length / productsPerPage));

    if (page < 1) {
      page = 1;
    }
    if (page > totalPages) {
      page = totalPages;
    }

    currentProductPage = page;
    const startIndex = (page - 1) * productsPerPage;
    const endIndex = startIndex + productsPerPage;

    cards.forEach((card, index) => {
      card.style.display = index >= startIndex && index < endIndex ? 'block' : 'none';
    });

    if (pageInfo) {
      pageInfo.textContent = `Página ${page} de ${totalPages}`;
    }
    if (prevBtn) {
      prevBtn.disabled = page <= 1;
    }
    if (nextBtn) {
      nextBtn.disabled = page >= totalPages;
    }
    if (paginationWrapper) {
      paginationWrapper.style.display = totalPages > 1 ? 'flex' : 'none';
    }
  }

  function changeProductPage(delta) {
    renderProductPage(currentProductPage + delta);
  }

  document.addEventListener('DOMContentLoaded', function() {
    const dropArea = document.getElementById('imageDropArea');
    const fileInput = document.getElementById('imagenFile');
    const dataInput = document.getElementById('imagenData');
    const pasteButton = document.getElementById('pasteImageButton');
    const pasteStatus = document.getElementById('pasteStatus');

    if (dropArea && fileInput) {
      dropArea.addEventListener('click', function() {
        fileInput.click();
      });

      dropArea.addEventListener('dragover', function(event) {
        event.preventDefault();
        dropArea.style.borderColor = '#667eea';
      });

      dropArea.addEventListener('dragleave', function() {
        dropArea.style.borderColor = '#ccc';
      });

      dropArea.addEventListener('drop', function(event) {
        event.preventDefault();
        dropArea.style.borderColor = '#ccc';
        const files = event.dataTransfer.files;
        if (files && files.length > 0) {
          const dt = new DataTransfer();
          dt.items.add(files[0]);
          fileInput.files = dt.files;
          const reader = new FileReader();
          reader.onload = function(e) {
            dataInput.value = '';
            updateImagePreview(e.target.result);
            if (pasteStatus) {
              pasteStatus.textContent = 'Imagen cargada desde archivo.';
            }
          };
          reader.readAsDataURL(files[0]);
        }
      });

      fileInput.addEventListener('change', function() {
        if (fileInput.files && fileInput.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            dataInput.value = '';
            updateImagePreview(e.target.result);
            if (pasteStatus) {
              pasteStatus.textContent = 'Imagen cargada desde archivo.';
            }
          };
          reader.readAsDataURL(fileInput.files[0]);
        } else {
          updateImagePreview(null);
        }
      });

      if (pasteButton) {
        pasteButton.addEventListener('click', function() {
          pasteClipboardImage();
        });
      }

      dropArea.addEventListener('paste', function(event) {
        const items = event.clipboardData?.items;
        if (!items) {
          return;
        }

        for (let i = 0; i < items.length; i++) {
          const item = items[i];
          if (item.type.indexOf('image') === 0) {
            const blob = item.getAsFile();
            if (blob) {
              const reader = new FileReader();
              reader.onload = function(e) {
                dataInput.value = e.target.result;
                if (fileInput) {
                  fileInput.value = null;
                }
                updateImagePreview(e.target.result);
                if (pasteStatus) {
                  pasteStatus.textContent = 'Imagen pegada desde el portapapeles.';
                }
              };
              reader.readAsDataURL(blob);
              event.preventDefault();
            }
          }
        }
      });
    }

    const prevBtn = document.getElementById('prevProductsBtn');
    const nextBtn = document.getElementById('nextProductsBtn');
    if (prevBtn) {
      prevBtn.addEventListener('click', function() {
        changeProductPage(-1);
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', function() {
        changeProductPage(1);
      });
    }

    renderProductPage(1);
    updateUserDisplay();
    updateCartBadge();
  });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\franc\OneDrive\Escritorio\sexopaye\ProjectMarket\market\resources\views/mis-comercios.blade.php ENDPATH**/ ?>