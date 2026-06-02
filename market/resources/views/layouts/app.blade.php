<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Marketplace Local')</title>
    @vite(['resources/css/styles.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <header>
        <div class="header-container">
            <div class="header-top">
                <div class="logo">
                    <span class="icon">🏪</span>
                    <h1>Marketplace Local</h1>
                </div>

                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Buscar productos, tiendas..." autocomplete="off">
                    <button onclick="search()">🔍</button>
                    <div class="search-suggestions" id="searchSuggestions"></div>
                </div>

                <div class="header-actions">
                    <div class="cart-badge" onclick="goToCart()">
                        🛒
                        <span class="badge" id="cartBadge" style="display: none;">0</span>
                    </div>

                    @if(session('user'))
                        @include('partials.notifications')
                    @endif

                    <div class="user-menu">
                        <div class="user-info" onclick="toggleUserMenu()">
                            <span id="userDisplay">👤</span>
                            <span id="userName">Iniciar</span>
                        </div>
                        <div class="dropdown-menu" id="userMenu">
                            @if (session('user'))
                                <a href="/micuenta" id="accountLink">Mi Cuenta</a>
                                @if (session('user')['tipo_usuario'] === 'vendedor')
                                    <a href="/mis-comercios" id="storesLink">Mis Comercios</a>
                                @endif
                                <form action="/auth/logout" method="POST" style="display: inline;" id="logoutForm">
                                    @csrf
                                    <button type="submit" id="logoutBtn" style="display: block; width: 100%; text-align: left; background: none; border: none; padding: 10px; cursor: pointer; color: #667eea;">Cerrar Sesión</button>
                                </form>
                            @else
                                <a href="/registro" id="loginLink">Iniciar Sesión</a>
                                <a href="/registro?tab=register" id="registerLink">Registrarse</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <nav>
                <a href="{{ url('/') }}" class="{{ Request::is('/') ? 'active' : '' }}">Inicio</a>
                <a href="{{ url('/productos') }}" class="{{ Request::is('productos*') ? 'active' : '' }}">Explorar</a>
                <a href="{{ url('/comercios') }}" class="{{ Request::is('comercios*') ? 'active' : '' }}">Comercios</a>
                <a href="{{ url('/carrito') }}" class="{{ Request::is('carrito') ? 'active' : '' }}">Carrito</a>
                <a href="{{ url('/acerca-de') }}" class="{{ Request::is('acerca-de') ? 'active' : '' }}">Acerca de</a>
            </nav>
        </div>
    </header>

    <script>
        window.currentSessionUser = @json(session('user'));
        
        // Validar cambio de usuario y migrar carrito/cupón de invitado
        document.addEventListener('DOMContentLoaded', function() {
            const currentUser = window.currentSessionUser;
            const storedUser = MarketplaceApp.getCurrentUser();
            
            // Caso 1: Usuario inicia sesión (había invitado, ahora hay usuario)
            if (currentUser && !storedUser) {
                // Migrar carrito de invitado a usuario
                const guestCart = localStorage.getItem('cart_guest');
                if (guestCart) {
                    const userCartKey = `cart_${currentUser.id_usuario}`;
                    localStorage.setItem(userCartKey, guestCart);
                    localStorage.removeItem('cart_guest');
                }
                
                // Migrar cupón activo de invitado a usuario
                const guestPromo = localStorage.getItem('promo_active_guest');
                if (guestPromo) {
                    const userPromoKey = `promo_active_${currentUser.id_usuario}`;
                    localStorage.setItem(userPromoKey, guestPromo);
                    localStorage.removeItem('promo_active_guest');
                }
            }
            
            // Caso 2: Usuario cambió (usuario A a usuario B)
            if (currentUser && storedUser && currentUser.id_usuario !== storedUser.id_usuario) {
                // Limpiar cupón activo del usuario anterior
                const oldPromoKey = `promo_active_${storedUser.id_usuario}`;
                localStorage.removeItem(oldPromoKey);
            }
            
            // Actualizar el usuario en localStorage si hay sesión activa
            if (currentUser) {
                MarketplaceApp.setCurrentUser(currentUser);
            } else if (storedUser) {
                // Si la sesión del servidor terminó, limpiar el usuario guardado localmente.
                MarketplaceApp.clearCurrentUser();
            }
            
            // Actualizar badge del carrito
            if (typeof MarketplaceApp.updateCartBadge === 'function') {
                MarketplaceApp.updateCartBadge();
            }
        });
    </script>

    <main class="main-container">
        @yield('content')
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3>Sobre Nosotros</h3>
                <p>Marketplace Local es una plataforma dedicada a conectar comercios locales con clientes de su comunidad.</p>
            </div>
            <div class="footer-section">
                <h3>Navegación</h3>
                <ul>
                    <li><a href="/{{ '' }}">Inicio</a></li>
                    <li><a href="/productos">Explorar Productos</a></li>
                    <li><a href="/comercios">Ver Comercios</a></li>
                    <li><a href="/acerca-de">Acerca de</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Para Vendedores</h3>
                <ul>
                    <li><a href="/registro?tab=store">Registrar Comercio</a></li>
                    <li><a href="/mis-comercios">Gestionar Productos</a></li>
                    <li><a href="#">Centro de Ayuda</a></li>
                    <li><a href="#">Políticas</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contacto</h3>
                <ul>
                    <li>📧 info@marketplace.local</li>
                    <li>📞 (555) 123-4567</li>
                    <li>📍 Tu Ciudad, País</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Marketplace Local. Todos los derechos reservados.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
