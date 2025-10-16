<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú - Sistema de Meseros</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .product-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .category-tab {
            transition: all 0.3s ease;
        }
        .category-tab.active {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            transform: translateY(-2px);
        }
        .cart-item {
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .floating-cart {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .quantity-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.2s ease;
        }
        .search-container {
            position: relative;
        }
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="min-h-screen p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="card rounded-xl shadow-xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">🍽️ Menú del Restaurante</h1>
                        <p class="text-gray-600 mt-2">Selecciona productos para crear un pedido</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Búsqueda -->
                        <div class="search-container">
                            <input
                                type="text"
                                id="search-input"
                                placeholder="Buscar productos..."
                                class="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <div id="search-results" class="search-results hidden"></div>
                        </div>
                        <!-- Selector de Mesa -->
                        <div>
                            <label for="table-select" class="block text-sm font-medium text-gray-700 mb-1">Mesa:</label>
                            <select id="table-select" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar mesa...</option>
                            </select>
                        </div>
                        <!-- Botón Dashboard -->
                        <a href="/waiter/dashboard" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            📊 Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categorías -->
        <div class="mb-8">
            <div class="card rounded-xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Categorías</h2>
                <div id="categories-container" class="flex flex-wrap gap-4">
                    <!-- Las categorías se cargarán aquí -->
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Lista de Productos -->
            <div class="lg:col-span-3">
                <div class="card rounded-xl shadow-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800" id="products-title">Todos los Productos</h2>
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full" id="products-count">0 productos</span>
                    </div>
                    <div id="products-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <!-- Los productos se cargarán aquí -->
                    </div>
                </div>
            </div>

            <!-- Carrito -->
            <div>
                <div class="card rounded-xl shadow-xl p-6 sticky top-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">🛒 Carrito</h2>
                        <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full" id="cart-count">0 items</span>
                    </div>

                    <div id="cart-items" class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                        <!-- Los items del carrito se cargarán aquí -->
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-semibold text-gray-800">Total:</span>
                            <span class="text-2xl font-bold text-green-600" id="cart-total">$0.00</span>
                        </div>

                        <div class="space-y-3">
                            <button
                                id="clear-cart"
                                class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg transition-colors"
                                onclick="clearCart()"
                            >
                                🗑️ Limpiar Carrito
                            </button>
                            <button
                                id="create-order"
                                class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg font-medium transition-colors"
                                onclick="createOrder()"
                                disabled
                            >
                                ✅ Crear Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalles del Producto -->
    <div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 id="modal-product-name" class="text-xl font-bold text-gray-800">
                    <span class="sr-only">Nombre del producto</span>
                    <span id="modal-product-name-text"></span>
                </h3>
                <button onclick="closeProductModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="modal-product-details" class="space-y-3">
                <!-- Detalles del producto se cargarán aquí -->
            </div>

            <div class="flex items-center justify-between mt-6 pt-4 border-t">
                <div class="flex items-center space-x-3">
                    <button onclick="decreaseQuantity()" class="quantity-btn bg-red-500 hover:bg-red-600 text-white">-</button>
                    <span id="modal-quantity" class="text-xl font-semibold">1</span>
                    <button onclick="increaseQuantity()" class="quantity-btn bg-green-500 hover:bg-green-600 text-white">+</button>
                </div>
                <button onclick="addToCartFromModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                    Agregar al Carrito
                </button>
            </div>
        </div>
    </div>

    <script>
        let categories = [];
        let products = [];
        let tables = [];
        let cart = [];
        let currentCategory = null;
        let selectedProduct = null;
        let modalQuantity = 1;

        // Inicializar aplicación
        document.addEventListener('DOMContentLoaded', function() {
            loadInitialData();
            setupSearch();
        });

        // Cargar datos iniciales
        async function loadInitialData() {
            try {
                await Promise.all([
                    loadCategories(),
                    loadProducts(),
                    loadTables()
                ]);
                displayCategories();
                displayProducts();
                updateCartDisplay();
            } catch (error) {
                console.error('Error cargando datos:', error);
                showNotification('Error cargando datos del menú', 'error');
            }
        }

        // Cargar categorías
        async function loadCategories() {
            const response = await fetch('/api/waiter/menu/categories');
            const data = await response.json();
            if (data.success) {
                categories = data.data;
            }
        }

        // Cargar productos
        async function loadProducts() {
            const response = await fetch('/api/waiter/menu/products');
            const data = await response.json();
            if (data.success) {
                products = data.data;
            }
        }

        // Cargar mesas
        async function loadTables() {
            const response = await fetch('/api/waiter/tables');
            const data = await response.json();
            if (data.success) {
                tables = data.data;
                displayTables();
            }
        }

        // Mostrar categorías
        function displayCategories() {
            const container = document.getElementById('categories-container');

            const allButton = `
                <button onclick="selectCategory(null)" class="category-tab px-6 py-3 rounded-lg font-medium ${!currentCategory ? 'active' : 'bg-gray-200 hover:bg-gray-300'}">
                    Todos (${products.length})
                </button>
            `;

            const categoryButtons = categories.map(category => `
                <button onclick="selectCategory(${category.id})" class="category-tab px-6 py-3 rounded-lg font-medium ${currentCategory === category.id ? 'active' : 'bg-gray-200 hover:bg-gray-300'}">
                    ${category.name} (${category.products_count})
                </button>
            `).join('');

            container.innerHTML = allButton + categoryButtons;
        }

        // Mostrar mesas
        function displayTables() {
            const select = document.getElementById('table-select');
            const availableTables = tables.filter(table => table.is_available);

            select.innerHTML = '<option value="">Seleccionar mesa...</option>' +
                availableTables.map(table => `
                    <option value="${table.id}">${table.name} (${table.capacity} personas)</option>
                `).join('');
        }

        // Seleccionar categoría
        function selectCategory(categoryId) {
            currentCategory = categoryId;
            displayCategories();
            displayProducts();
        }

        // Mostrar productos
        function displayProducts() {
            const container = document.getElementById('products-container');
            const title = document.getElementById('products-title');
            const count = document.getElementById('products-count');

            let filteredProducts = products;

            if (currentCategory) {
                filteredProducts = products.filter(product => product.category.id === currentCategory);
                const categoryName = categories.find(cat => cat.id === currentCategory)?.name || 'Categoría';
                title.textContent = categoryName;
            } else {
                title.textContent = 'Todos los Productos';
            }

            count.textContent = `${filteredProducts.length} productos`;

            if (filteredProducts.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-5L9 8l10 10M9 8l10 10"></path>
                        </svg>
                        <p class="text-lg font-medium">No hay productos en esta categoría</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = filteredProducts.map(product => `
                <div class="product-card bg-white rounded-lg shadow-md p-4" onclick="openProductModal(${product.id})">
                    <div class="mb-3">
                        <h3 class="font-semibold text-gray-800 text-lg mb-1">${product.name}</h3>
                        <p class="text-gray-600 text-sm line-clamp-2">${product.description}</p>
                    </div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-2xl font-bold text-green-600">${product.price_formatted}</span>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">${product.category.name}</span>
                    </div>
                    <button onclick="event.stopPropagation(); quickAddToCart(${product.id})" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg font-medium transition-colors">
                        ➕ Agregar al Carrito
                    </button>
                </div>
            `).join('');
        }

        // Configurar búsqueda
        function setupSearch() {
            const searchInput = document.getElementById('search-input');
            const searchResults = document.getElementById('search-results');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            // Cerrar resultados al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });
        }

        // Realizar búsqueda
        async function performSearch(query) {
            try {
                const response = await fetch(`/api/waiter/menu/search?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (data.success) {
                    displaySearchResults(data.data);
                }
            } catch (error) {
                console.error('Error en búsqueda:', error);
            }
        }

        // Mostrar resultados de búsqueda
        function displaySearchResults(results) {
            const container = document.getElementById('search-results');

            if (results.length === 0) {
                container.innerHTML = '<div class="p-4 text-gray-500 text-center">No se encontraron productos</div>';
            } else {
                container.innerHTML = results.map(product => `
                    <div class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0" onclick="selectSearchResult(${product.id})">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800">${product.name}</h4>
                                <p class="text-sm text-gray-600">${product.description}</p>
                                <span class="text-xs text-blue-600">${product.category.name}</span>
                            </div>
                            <span class="font-bold text-green-600 ml-2">${product.price_formatted}</span>
                        </div>
                    </div>
                `).join('');
            }

            container.classList.remove('hidden');
        }

        // Seleccionar resultado de búsqueda
        function selectSearchResult(productId) {
            document.getElementById('search-results').classList.add('hidden');
            document.getElementById('search-input').value = '';
            openProductModal(productId);
        }

        // Abrir modal de producto
        function openProductModal(productId) {
            selectedProduct = products.find(p => p.id === productId);
            modalQuantity = 1;

            if (!selectedProduct) return;

            document.getElementById('modal-product-name-text').textContent = selectedProduct.name;
            document.getElementById('modal-quantity').textContent = modalQuantity;

            document.getElementById('modal-product-details').innerHTML = `
                <p class="text-gray-600">${selectedProduct.description}</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Precio:</span>
                    <span class="text-xl font-bold text-green-600">${selectedProduct.price_formatted}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Categoría:</span>
                    <span class="text-sm text-blue-600">${selectedProduct.category.name}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Unidad:</span>
                    <span class="text-sm text-gray-600">${selectedProduct.unit}</span>
                </div>
            `;

            const modal = document.getElementById('product-modal');
            modal.style.display = 'flex';
        }

        // Cerrar modal de producto
        function closeProductModal() {
            const modal = document.getElementById('product-modal');
            modal.style.display = 'none';
            selectedProduct = null;
            modalQuantity = 1;
        }

        // Aumentar cantidad en modal
        function increaseQuantity() {
            modalQuantity++;
            document.getElementById('modal-quantity').textContent = modalQuantity;
        }

        // Disminuir cantidad en modal
        function decreaseQuantity() {
            if (modalQuantity > 1) {
                modalQuantity--;
                document.getElementById('modal-quantity').textContent = modalQuantity;
            }
        }

        // Agregar rápido al carrito
        function quickAddToCart(productId) {
            const product = products.find(p => p.id === productId);
            if (product) {
                addToCart(product, 1);
            }
        }

        // Agregar al carrito desde modal
        function addToCartFromModal() {
            if (selectedProduct) {
                addToCart(selectedProduct, modalQuantity);
                closeProductModal();
            }
        }

        // Agregar al carrito
        function addToCart(product, quantity) {
            const existingItem = cart.find(item => item.product.id === product.id);

            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({
                    product: product,
                    quantity: quantity
                });
            }

            updateCartDisplay();
            showNotification(`${product.name} agregado al carrito`, 'success');
        }

        // Actualizar visualización del carrito
        function updateCartDisplay() {
            const container = document.getElementById('cart-items');
            const count = document.getElementById('cart-count');
            const total = document.getElementById('cart-total');
            const createButton = document.getElementById('create-order');

            count.textContent = `${cart.length} items`;

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0L4 5m6 12a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4z"></path>
                        </svg>
                        <p class="text-sm">Carrito vacío</p>
                    </div>
                `;
                total.textContent = '$0.00';
                createButton.disabled = true;
                return;
            }

            const cartTotal = cart.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
            total.textContent = '$' + cartTotal.toFixed(2);
            createButton.disabled = false;

            container.innerHTML = cart.map((item, index) => `
                <div class="cart-item bg-gray-50 rounded-lg p-3">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium text-gray-800 text-sm">${item.product.name}</h4>
                        <button onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <button onclick="changeQuantity(${index}, -1)" class="w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600">-</button>
                            <span class="text-sm font-medium">${item.quantity}</span>
                            <button onclick="changeQuantity(${index}, 1)" class="w-6 h-6 bg-green-500 text-white rounded-full text-xs hover:bg-green-600">+</button>
                        </div>
                        <span class="text-sm font-bold text-green-600">$${(item.product.price * item.quantity).toFixed(2)}</span>
                    </div>
                </div>
            `).join('');
        }

        // Cambiar cantidad en carrito
        function changeQuantity(index, change) {
            cart[index].quantity += change;

            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }

            updateCartDisplay();
        }

        // Remover del carrito
        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartDisplay();
        }

        // Limpiar carrito
        function clearCart() {
            if (cart.length === 0) return;

            if (confirm('¿Estás seguro de que quieres limpiar el carrito?')) {
                cart = [];
                updateCartDisplay();
                showNotification('Carrito limpiado', 'info');
            }
        }

        // Crear pedido
        async function createOrder() {
            const tableId = document.getElementById('table-select').value;

            if (!tableId) {
                showNotification('Por favor selecciona una mesa', 'error');
                return;
            }

            if (cart.length === 0) {
                showNotification('El carrito está vacío', 'error');
                return;
            }

            try {
                // Preparar datos del pedido
                const orderData = {
                    table_id: parseInt(tableId),
                    items: cart.map(item => ({
                        product_id: item.product.id,
                        quantity: item.quantity
                    })),
                    notes: null // Puedes agregar un campo para notas si es necesario
                };

                // Crear el pedido
                const response = await fetch('/api/waiter/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });

                const data = await response.json();

                if (data.success) {
                    showNotification(`Pedido #${data.data.order_id} creado exitosamente para ${data.data.table_name}`, 'success');

                    // Limpiar carrito
                    cart = [];
                    updateCartDisplay();

                    // Reset mesa
                    document.getElementById('table-select').value = '';

                    // Opcional: redirigir al dashboard después de unos segundos
                    setTimeout(() => {
                        if (confirm('¿Deseas ir al dashboard para ver el pedido?')) {
                            window.location.href = '/waiter/dashboard';
                        }
                    }, 2000);
                } else {
                    showNotification('Error al crear pedido: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error creando pedido:', error);
                showNotification('Error de conexión al crear pedido', 'error');
            }
        }

        // Mostrar notificación
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                type === 'info' ? 'bg-blue-500 text-white' : 'bg-gray-500 text-white'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>
</html>
