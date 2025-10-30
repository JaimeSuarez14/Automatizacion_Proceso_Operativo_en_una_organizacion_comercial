// Menu items data
const menuItems = [
    {
        id: 1,
        name: "Ceviche Mixto",
        description: "Fresco ceviche con pescado, camarones y pulpo",
        price: 35.00,
        emoji: "🍤"
    },
    {
        id: 2,
        name: "Pescado a la Plancha",
        description: "Filete de pescado fresco con guarnición",
        price: 28.00,
        emoji: "🐟"
    },
    {
        id: 3,
        name: "Parrilla Marina",
        description: "Variedad de mariscos a la parrilla",
        price: 45.00,
        emoji: "🦐"
    },
    {
        id: 4,
        name: "Arroz con Mariscos",
        description: "Delicioso arroz con mariscos frescos",
        price: 32.00,
        emoji: "🍚"
    },
    {
        id: 5,
        name: "Chicharrón de Calamar",
        description: "Calamar fresco rebozado y frito",
        price: 25.00,
        emoji: "🦑"
    },
    {
        id: 6,
        name: "Sopa de Mariscos",
        description: "Reconfortante sopa con variedad de mariscos",
        price: 22.00,
        emoji: "🍲"
    }
];

let cart = [];
let quantities = {};

// Inicializar cantidades
menuItems.forEach(item => {
    quantities[item.id] = 0;
});

// Navegación
function showSection(sectionId) {
    document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
    });
    const sectionEl = document.getElementById(sectionId);
    if (sectionEl) {
        sectionEl.classList.add('active');
    }
    const navLinks = document.querySelector('.nav-links');
    if (navLinks) {
        navLinks.classList.remove('active');
    }
}

function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    if (navLinks) {
        navLinks.classList.toggle('active');
    }
}

// Renderizar menú
function renderMenu() {
    const menuGrid = document.getElementById('menu-grid');
    if (!menuGrid) return;
    menuGrid.innerHTML = '';

    menuItems.forEach(item => {
        const menuItemHtml = `
            <div class="menu-item">
                <div class="menu-item-image">${item.emoji}</div>
                <div class="menu-item-content">
                    <h3>${item.name}</h3>
                    <p>${item.description}</p>
                    <div class="price">S/. ${item.price.toFixed(2)}</div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseQuantity(${item.id})">-</button>
                        <span class="quantity" id="qty-${item.id}">${quantities[item.id]}</span>
                        <button class="quantity-btn" onclick="increaseQuantity(${item.id})">+</button>
                    </div>
                    <button class="btn add-to-cart" onclick="addToCart(${item.id})">
                        Agregar al Carrito
                    </button>
                </div>
            </div>
        `;
        menuGrid.innerHTML += menuItemHtml;
    });
}

// Control de cantidades
function increaseQuantity(itemId) {
    quantities[itemId]++;
    document.getElementById(`qty-${itemId}`).textContent = quantities[itemId];
}

function decreaseQuantity(itemId) {
    if (quantities[itemId] > 0) {
        quantities[itemId]--;
        document.getElementById(`qty-${itemId}`).textContent = quantities[itemId];
    }
}

// Carrito
function addToCart(itemId) {
    const quantity = quantities[itemId];
    if (quantity === 0) {
        alert('Por favor selecciona una cantidad');
        return;
    }

    const item = menuItems.find(item => item.id === itemId);
    if (!item) return;

    const existingItem = cart.find(cartItem => cartItem.id === itemId);

    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({ ...item, quantity });
    }

    // Reset cantidad
    quantities[itemId] = 0;
    document.getElementById(`qty-${itemId}`).textContent = 0;

    updateCartCount();
    showCartNotification();
}

function updateCartCount() {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCountEl = document.getElementById('cart-count');
    if (cartCountEl) {
        cartCountEl.textContent = totalItems;
    }
}

function showCartNotification() {
    const cartEl = document.querySelector('.cart');
    if (!cartEl) return;
    cartEl.style.transform = 'scale(1.2)';
    setTimeout(() => {
        cartEl.style.transform = 'scale(1)';
    }, 300);
}

function toggleCart() {
    const modal = document.getElementById('cart-modal');
    if (!modal) return;
    modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
    if (modal.style.display === 'block') {
        renderCart();
    }
}

function renderCart() {
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    if (!cartItems || !cartTotal) return;

    if (cart.length === 0) {
        cartItems.innerHTML = '<p style="text-align: center; color: #666;">Tu carrito está vacío</p>';
        cartTotal.innerHTML = 'Total: S/. 0.00';
        return;
    }

    let html = '';
    let total = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        html += `
            <div class="cart-item">
                <div>
                    <strong>${item.name}</strong><br>
                    <small>S/. ${item.price.toFixed(2)} x ${item.quantity}</small>
                </div>
                <div>
                    <strong>S/. ${itemTotal.toFixed(2)}</strong>
                    <button onclick="removeFromCart(${item.id})" 
                            style="margin-left: 10px; background: #ff6b6b; color: white; border: none; border-radius: 5px; padding: 5px 10px; cursor: pointer;">
                        ×
                    </button>
                </div>
            </div>
        `;
    });

    cartItems.innerHTML = html;
    cartTotal.innerHTML = `Total: S/. ${total.toFixed(2)}`;
}

function removeFromCart(itemId) {
    cart = cart.filter(item => item.id !== itemId);
    updateCartCount();
    renderCart();
}

function proceedToOrder() {
    if (cart.length === 0) {
        alert('Tu carrito está vacío');
        return;
    }
    toggleCart();
    showSection('order');
}

// Formularios
function validatePasswords() {
    const passwords = document.querySelectorAll('#register-form input[type="password"]');
    return passwords.length === 2 && passwords[0].value === passwords[1].value;
}

function resetForm(formId) {
    const form = document.getElementById(formId);
    if (form) form.reset();
}

function showSuccessMessage(messageId) {
    const message = document.getElementById(messageId);
    if (!message) return;
    message.style.display = 'block';
    setTimeout(() => {
        message.style.display = 'none';
    }, 3000);
}

// Inicialización
document.addEventListener('DOMContentLoaded', function () {
    renderMenu();
    updateCartCount();

    // Fecha mínima para pedidos
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.querySelector('input[type="date"]');
    if (dateInput) {
        dateInput.setAttribute('min', today);
    }

    // Manejo de formulario de pedido
    const orderForm = document.getElementById('order-form');
    if (orderForm) {
        orderForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (cart.length === 0) {
                alert('No hay items en el carrito para procesar el pedido');
                return;
            }

            showSuccessMessage('order-success');

            setTimeout(() => {
                cart = [];
                updateCartCount();
                resetForm('order-form');
                showSection('home');
            }, 3000);
        });
    }

    // Manejo de formulario de registro
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!validatePasswords()) {
                alert('Las contraseñas no coinciden. Por favor, inténtalo de nuevo.');
                return;
            }

            const termsCheckbox = document.getElementById('terms');
            if (termsCheckbox && !termsCheckbox.checked) {
                alert('Debes aceptar los términos y condiciones');
                return;
            }

            showSuccessMessage('register-success');

            setTimeout(() => {
                resetForm('register-form');
                showSection('home');
            }, 3000);
        });
    }
});
