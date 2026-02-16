// js/script.js
let floatingCartTimer;

// Add to Cart
function addToCart(menuId) {
    fetch('pages/add_to_cart.php', {
        method: 'POST',
        credentials: 'include', // Ensure cookies are sent
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'menu_id=' + menuId
    })
        .then(async response => {
            const text = await response.text();
            try {
                if (!response.ok) {
                    throw new Error(`Server Error: ${response.status} ${response.statusText}`);
                }
                return JSON.parse(text);
            } catch (e) {
                console.error('Server Invalid JSON:', text);
                throw new Error('Server returned invalid content. Check console.');
            }
        })
        .then(data => {
            if (data.success) {
                // Update cart count
                // Update cart count for all instances (navbar + floating)
                const countEls = document.querySelectorAll('.cart-count-display');
                countEls.forEach(el => {
                    el.innerText = data.cart_count;
                    // Add simple animation
                    el.style.transform = 'scale(1.5)';
                    setTimeout(() => el.style.transform = 'scale(1)', 200);
                });

                // Show Floating Cart for 5 seconds
                const floatingCart = document.getElementById('floating-cart');
                if (floatingCart) {
                    floatingCart.style.display = 'flex';

                    // Clear existing timer if any
                    if (floatingCartTimer) {
                        clearTimeout(floatingCartTimer);
                    }

                    // Set new timer to hide after 5 seconds
                    floatingCartTimer = setTimeout(() => {
                        floatingCart.style.display = 'none';
                    }, 5000);
                }

                showToast(data.message || 'Item added to cart!');

            } else {
                // Handle specific errors like Login Required
                if (data.message && data.message.includes('login')) {
                    if (confirm(data.message + " Go to login page?")) {
                        window.location.href = 'login.php';
                    }
                } else {
                    alert(data.message || 'Error adding to cart');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error: " + error.message);
        });
}

// Booking Form Submission
function submitBooking(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    fetch('pages/process_booking.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Booking request sent successfully! We will confirm shortly.');
                closeBookingModal();
                form.reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Navbar Scroll Logic
document.addEventListener('DOMContentLoaded', function () {
    console.log("Navbar script loaded");
    let lastScrollTop = 0;
    const navbar = document.querySelector('.navbar');

    if (navbar) {
        console.log("Navbar element found");
        window.addEventListener('scroll', function () {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Limit sensitivity or bounce effects
            if (scrollTop < 0) return;

            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling DOWN -> Hide
                if (!navbar.classList.contains('navbar-hidden')) {
                    console.log("Hiding navbar");
                    navbar.classList.add('navbar-hidden');
                }
            } else {
                // Scrolling UP -> Show
                if (navbar.classList.contains('navbar-hidden')) {
                    console.log("Showing navbar");
                    navbar.classList.remove('navbar-hidden');
                }
            }
            lastScrollTop = scrollTop;
        });
    } else {
        console.error("Navbar element NOT found");
    }
});

// Ensure functions are available globally
window.addToCart = addToCart;
window.submitBooking = submitBooking;

// Toast Notification Logic
// Toast Notification Logic
function showToast(message) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    // Force single toast: Remove all existing toasts
    while (container.firstChild) {
        container.removeChild(container.firstChild);
    }

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    // Trigger animation
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    // Auto remove
    setTimeout(() => {
        toast.classList.remove('show');
        toast.classList.add('hide');
        toast.addEventListener('transitionend', () => {
            if (toast.parentNode) {
                toast.remove();
            }
        });
    }, 5000); // 5 seconds
}

// Keep name for compatibility but redirect to toast
window.showCartModal = showToast;
window.closeCartModal = function () { }; // No-op now

// Product Modal Logic
function openProductModal(item) {
    const modal = document.getElementById('productModal');
    if (!modal) return;

    // Populate Data
    document.getElementById('modalTitle').textContent = item.item_name;
    document.getElementById('modalDesc').textContent = item.description;

    // Image Handling
    const imgPath = item.image ? 'images/' + item.image : 'https://via.placeholder.com/400x300?text=No+Image';
    document.getElementById('modalImg').src = imgPath;

    // Price Handling
    let priceHtml = '';
    const priceProps = {
        price: parseFloat(item.price),
        discount: parseFloat(item.discount_percent || 0)
    };

    if (priceProps.discount > 0) {
        const discountedPrice = priceProps.price * (1 - priceProps.discount / 100);
        priceHtml = `<span class="text-gold">₹${discountedPrice.toFixed(2)}</span> 
                     <span style="text-decoration: line-through; color: #888; font-size: 1rem; margin-left: 10px;">₹${priceProps.price.toFixed(2)}</span>`;
    } else {
        priceHtml = `<span class="text-gold">₹${priceProps.price.toFixed(2)}</span>`;
    }
    document.getElementById('modalPrice').innerHTML = priceHtml;

    // Add to Cart Action
    const addToCartBtn = document.getElementById('modalAddToCartBtn');
    addToCartBtn.onclick = function () {
        // You might want to handle quantity here too if your backend supports it, 
        // for now just adding the item ID.
        addToCart(item.menu_id);
        closeProductModal();
    };

    modal.style.display = 'block';

    // Add backdrop
    let backdrop = document.getElementById('productBackdrop');
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.id = 'productBackdrop';
        backdrop.style.position = 'fixed';
        backdrop.style.top = '0';
        backdrop.style.left = '0';
        backdrop.style.width = '100%';
        backdrop.style.height = '100%';
        backdrop.style.backgroundColor = 'rgba(0,0,0,0.8)';
        backdrop.style.zIndex = '1999';
        backdrop.onclick = closeProductModal;
        document.body.appendChild(backdrop);
    } else {
        backdrop.style.display = 'block';
    }
}

function closeProductModal() {
    const modal = document.getElementById('productModal');
    const backdrop = document.getElementById('productBackdrop');
    if (modal) modal.style.display = 'none';
    if (backdrop) backdrop.style.display = 'none';
}
window.openProductModal = openProductModal;
window.closeProductModal = closeProductModal;

function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

function toggleMobileMenu() {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('active');
}
window.toggleMobileMenu = toggleMobileMenu;
