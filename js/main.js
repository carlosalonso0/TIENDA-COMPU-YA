document.addEventListener('DOMContentLoaded', function() {
    // Menú móvil
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            
            // Cambiar ícono del menú
            const icon = menuToggle.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
    
    // Dropdown en dispositivos móviles
const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
        // Solo prevenir el comportamiento predeterminado en dispositivos móviles
        if (window.innerWidth < 768) {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('active');
            
            // Cambiar ícono del dropdown
            const icon = this.querySelector('i');
            if (icon) {
                if (icon.classList.contains('fa-chevron-down')) {
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                } else if (icon.classList.contains('fa-chevron-up')) {
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                } else if (icon.classList.contains('fa-chevron-right')) {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-down');
                } else if (icon.classList.contains('fa-chevron-down')) {
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-right');
                }
            }
        }
    });
});
    
    // Carrito de compras (funcionalidad básica)
    const addToCartButtons = document.querySelectorAll('.btn-add-cart');
    const cartCount = document.querySelector('.cart-count');
    
    let itemsInCart = 0;
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            itemsInCart++;
            cartCount.textContent = itemsInCart;
            
            // Obtener información del producto
            const productCard = this.closest('.product-card');
            const productTitle = productCard.querySelector('.product-title').textContent;
            const productPrice = productCard.querySelector('.price-current').textContent;
            
            // Mostrar mensaje de confirmación
            alert(`¡Producto añadido al carrito!\n${productTitle}\nPrecio: ${productPrice}`);
            
            // Aquí se podría implementar la lógica real de carrito
            // Por ejemplo, guardar en localStorage o enviar a backend
        });
    });
    
    // Animación de scroll suave para los enlaces
    const scrollLinks = document.querySelectorAll('a[href^="#"]');
    
    scrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href !== '#') {
                e.preventDefault();
                const targetElement = document.querySelector(href);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
    // Añadir media queries con JavaScript para el responsive
    function handleResponsiveChanges() {
        if (window.innerWidth < 768) {
            mainNav.classList.add('mobile');
        } else {
            mainNav.classList.remove('mobile', 'active');
            
            // Restablecer ícono del menú
            if (menuToggle) {
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
            
            // Restablecer dropdowns
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                dropdown.classList.remove('active');
                const icon = dropdown.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                }
            });
        }
    }
    
    // Ejecutar al cargar la página
    handleResponsiveChanges();
    
    // Ejecutar cuando la ventana cambia de tamaño
    window.addEventListener('resize', handleResponsiveChanges);
});

// Funcionalidad para actualizar el contenido cuando se tenga la integración con el stock
// Esta función se usará más adelante cuando implementemos la funcionalidad completa
function updateProductInventory(productId, newStock) {
    // Aquí iría la lógica para actualizar el stock de productos
    console.log(`Actualizando inventario para producto ${productId}, nuevo stock: ${newStock}`);
    
    // Esta función se conectará con la base de datos más adelante
}
