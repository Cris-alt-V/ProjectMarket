# Pruebas de Validación: Carrito por Usuario

## Escenario 1: Invitado agrega productos, luego inicia sesión
1. ✅ Acceder a /productos sin estar logueado
2. ✅ Agregar 2-3 productos al carrito
3. ✅ Verificar que los productos están en localStorage bajo `cart_guest`
4. ✅ Ir a /registro e iniciar sesión como Usuario A
5. ✅ Verificar que el carrito se migró a `cart_${user_id_A}` y sigue mostrando los productos
6. ✅ Verificar que `cart_guest` se eliminó

**Resultado esperado:** Los productos del carrito se mantienen después de iniciar sesión

---

## Escenario 2: Usuario A agrega productos, cierra sesión, inicia sesión como Usuario B
1. ✅ Iniciar sesión como Usuario A
2. ✅ Agregar 3-4 productos al carrito (visible en badge)
3. ✅ Ir a carrito y verificar los productos
4. ✅ Hacer clic en "Cerrar Sesión"
5. ✅ Verificar que `cart_${user_id_A}` se eliminó del localStorage
6. ✅ Iniciar sesión como Usuario B (diferente cuenta)
7. ✅ Ir a carrito y verificar que está VACÍO

**Resultado esperado:** El carrito se limpió al cambiar de usuario

---

## Escenario 3: Usuario A agrega productos, recarga la página, verifica que los productos persisten
1. ✅ Iniciar sesión como Usuario A
2. ✅ Agregar 2 productos al carrito
3. ✅ Recargar la página (F5)
4. ✅ Verificar que los productos siguen en el carrito

**Resultado esperado:** El carrito persiste después de recargar

---

## Escenario 4: Usuario A cierra sesión sin ser reemplazado
1. ✅ Iniciar sesión como Usuario A
2. ✅ Agregar productos al carrito
3. ✅ Hacer clic en "Cerrar Sesión"
4. ✅ Verificar que el carrito está vacío para invitados
5. ✅ Agregar nuevos productos como invitado
6. ✅ Verificar que se guardan en `cart_guest`

**Resultado esperado:** El carrito invitado funciona independiente del usuario

---

## Notas Técnicas
- localStorage keys:
  - `cart_guest` - Carrito de invitado
  - `cart_${user_id}` - Carrito de usuario autenticado
  - `marketplaceUser` - Información del usuario actual
  
- Los cambios se realizaron en:
  - `resources/js/market.js` - Lógica de carrito
  - `resources/views/layouts/app.blade.php` - Validación y limpieza en logout
