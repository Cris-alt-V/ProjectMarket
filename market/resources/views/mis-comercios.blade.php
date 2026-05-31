@extends('layouts.app')

@section('title', 'Mis Comercios - Marketplace Local')

@section('content')
  <h1 style="margin-bottom: 30px; color: #333;">Gestionar mis Comercios</h1>

  @if (!session('user') || session('user')['tipo_usuario'] !== 'vendedor')
    <div id="loginPrompt" style="background: white; border-radius: 8px; padding: 40px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <h2>Por favor inicia sesión</h2>
      <p style="color: #666; margin-bottom: 20px;">Solo los vendedores registrados pueden gestionar sus comercios</p>
      <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
        <a href="/registro" class="btn btn-primary">Iniciar Sesión</a>
        <a href="/registro?tab=store" class="btn btn-secondary">Registrar Comercio</a>
      </div>
    </div>
  @else
    <div id="storesContent">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 10px;">
        <h2 style="margin: 0;">Mis Tiendas</h2>
        <button class="btn btn-primary" onclick="document.getElementById('formAgregarProducto').style.display='block'">Agregar Producto</button>
      </div>

      <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;">
          <h3 style="margin: 0 0 10px 0; font-size: 1.5em;">🏪 {{ session('user')['nombre_negocio'] ?? 'Mi Tienda' }}</h3>
          <p style="margin: 0; opacity: 0.9;">{{ session('user')['descripcion'] ?? 'Descripción no disponible' }}</p>
        </div>
        <div style="padding: 20px;">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 20px;">
            <div>
              <label style="color: #666; font-size: 0.9em;">Ubicación</label>
              <p style="margin: 5px 0; font-weight: bold;">{{ session('user')['ubicacion'] ?? 'No especificada' }}</p>
            </div>
            <div>
              <label style="color: #666; font-size: 0.9em;">Correo</label>
              <p style="margin: 5px 0; font-weight: bold;">{{ session('user')['correo'] ?? 'No disponible' }}</p>
            </div>
            <div>
              <label style="color: #666; font-size: 0.9em;">Propietario</label>
              <p style="margin: 5px 0; font-weight: bold;">{{ session('user')['nombre'] ?? 'No disponible' }}</p>
            </div>
          </div>
        </div>
      </div>

      <h3 style="margin-bottom: 20px;">Mis Productos</h3>
      @if ($productos->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
          @foreach ($productos as $producto)
            <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
              <img src="{{ $producto->imagen_url ?? '/imagenes/blusa.png' }}" alt="{{ $producto->nombre }}" style="width: 100%; height: 200px; object-fit: contain; background: #f8f8f8;">
              <div style="padding: 15px;">
                <h4 style="margin: 0 0 10px 0;">{{ $producto->nombre }}</h4>
                <p style="color: #666; font-size: 0.9em; margin: 5px 0;">{{ Str::limit($producto->descripcion, 100) }}</p>
                <div style="margin: 10px 0; font-weight: bold; color: #667eea;">
                  ${{ number_format($producto->precio, 2) }}
                </div>
                <div style="margin: 10px 0; font-size: 0.9em; color: #666;">
                  Stock: {{ $producto->stock }}
                </div>
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                  <button class="btn btn-primary" style="flex: 1;" onclick="editProduct({{ $producto->id_producto }})">Editar</button>
                  <form action="/productos/{{ $producto->id_producto }}" method="POST" style="flex: 1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width: 100%;" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                  </form>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div style="background: #f0f0f0; border-radius: 8px; padding: 30px; text-align: center;">
          <p style="color: #666; margin: 0;">No tienes productos publicados. ¡Comienza ahora!</p>
        </div>
      @endif

      <!-- Formulario agregar producto -->
      <div id="formAgregarProducto" style="display: none; background: white; border-radius: 8px; padding: 30px; margin-top: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0;">Agregar Nuevo Producto</h3>
        <form action="/productos/crear" method="POST" enctype="multipart/form-data" style="display: grid; gap: 15px;">
          @csrf
          <div>
            <label>Nombre del Producto</label>
            <input type="text" name="nombre" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
          </div>
          <div>
            <label>Descripción</label>
            <textarea name="descripcion" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; height: 100px;"></textarea>
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
            <input type="file" id="imagenFile" name="imagen_file" accept="image/*" style="display: none;">
            <input type="hidden" id="imagenData" name="imagen_data" value="">
            <div id="imagePreviewWrapper" style="margin-top: 15px; display: none;">
              <label>Vista previa</label>
              <img id="imagePreview" src="" alt="Vista previa de la imagen" style="width: 100%; max-height: 240px; object-fit: contain; border: 1px solid #ddd; border-radius: 8px; display: block; margin-top: 10px;">
            </div>
          </div>
          <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar Producto</button>
            <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="clearImageInputs(); document.getElementById('formAgregarProducto').style.display='none'">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  @endif
@endsection

@push('scripts')
<script>
  function editProduct(id) {
    MarketplaceApp.showNotification('Edición de productos próximamente');
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
    if (fileInput) {
      fileInput.value = null;
    }
    if (dataInput) {
      dataInput.value = '';
    }
    updateImagePreview(null);
  }

  document.addEventListener('DOMContentLoaded', function() {
    const dropArea = document.getElementById('imageDropArea');
    const fileInput = document.getElementById('imagenFile');
    const dataInput = document.getElementById('imagenData');

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
          };
          reader.readAsDataURL(fileInput.files[0]);
        } else {
          updateImagePreview(null);
        }
      });

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
              };
              reader.readAsDataURL(blob);
              event.preventDefault();
            }
          }
        }
      });
    }

    updateUserDisplay();
    updateCartBadge();
  });
</script>
@endpush