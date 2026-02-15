<?php
// index.php
require 'config/db_connect.php';
require 'includes/header.php';

// Fetch Menu for Home Display as well
$categories = ['All', 'Coffee', 'Snacks', 'Desserts', 'Beverages'];
?>

<script>
    function toggleCategory(category) {
        // Simple JS filter for demonstration if not AJAX
        var cards = document.querySelectorAll('.menu-card');
        cards.forEach(card => {
            if (category === 'All' || card.dataset.category === category) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

<!-- Hero Section -->
<section class="hero">
    <div class="particles"></div>
    <div class="hero-splash"></div>

    <div class="hero-content">
        <div class="hero-text">
            <h1 class="hero-title">
                Experience the <br>
                <span class="text-gold">Dark Roast</span> Ritual
            </h1>
            <p class="hero-subtitle">
                Premium beans, cinematic vibes, and the perfect brew for your late-night coding sessions. Taste the
                difference of Midnight Cafe.
            </p>
            <div style="display:flex; gap:1rem;">
                <a href="#menu-section" class="btn btn-primary">Order Now</a>
                <button onclick="openBookingModal()" class="btn btn-accent">Book a Table</button>
            </div>
        </div>
        <!-- Right side is handled by background splash image in CSS -->
    </div>

    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="stat-item">
            <h4>50+</h4>
            <p>Premium Items</p>
        </div>
        <div class="stat-item">
            <h4>100+</h4>
            <p>Happy Customers</p>
        </div>
        <div class="stat-item">
            <h4>24/7</h4>
            <p>Support</p>
        </div>
        <div class="stat-item">
            <h4>4.9</h4>
            <p>Average Rating</p>
        </div>
    </div>
</section>

<!-- Specials Section (Spotlight) -->
<?php
$featured_id = get_setting('featured_item_id', 1);
$stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
$stmt->execute([$featured_id]);
$featured_item = $stmt->fetch();

if ($featured_item):
    $final_price = $featured_item['price'];
    if ($featured_item['discount_percent'] > 0) {
        $final_price = $featured_item['price'] * (1 - ($featured_item['discount_percent'] / 100));
    }
    ?>
    <section style="padding: 4rem 0; background: var(--surface);">
        <div class="container" style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <video autoplay muted loop
                    style="width: 100%; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft);">
                    <source src="images/signature_latte.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div style="flex: 1; padding: 2rem;">
                <span class="badge-tape" style="font-size: 1.5rem; transform: rotate(-5deg); margin-bottom: 1rem;">Signature
                    Item</span>
                <h2 style="font-size: 3rem; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($featured_item['item_name']); ?>
                </h2>
                <p style="font-size: 1.2rem; margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($featured_item['description']); ?>
                </p>
                <div style="font-size: 2rem; font-weight: bold; color: var(--primary);">
                    ₹<?php echo number_format($final_price, 2); ?>
                    <?php if ($featured_item['discount_percent'] > 0): ?>
                        <span
                            style="text-decoration: line-through; color: #888; font-size: 1.5rem;">₹<?php echo number_format($featured_item['price'], 2); ?></span>
                    <?php endif; ?>
                </div>
                <button onclick="addToCart(<?php echo $featured_item['menu_id']; ?>)" class="btn btn-primary"
                    style="margin-top: 1rem;">Order Now</button>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Menu Section -->
<section id="menu-section" style="padding: 4rem 0;">
    <div class="container">
        <h2 class="text-center" style="margin-bottom: 2rem;">Our Menu</h2>

        <!-- Category Filter -->
        <div class="text-center"
            style="margin-bottom: 2rem; overflow-x: auto; white-space: nowrap; padding-bottom: 1rem;">
            <?php foreach ($categories as $cat): ?>
                <button onclick="toggleCategory('<?php echo $cat; ?>')" class="btn btn-outline"
                    style="margin: 0 0.5rem; background: var(--surface); color: var(--text-main); border: 1px solid #ccc;"><?php echo $cat; ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Items Grid -->
        <div class="menu-grid">
            <?php
            // Fetch All Items for initial load
            $stmt = $pdo->query("SELECT * FROM menu ORDER BY sort_order ASC");
            $menu_items = $stmt->fetchAll();

            foreach ($menu_items as $item):
                $final_price = $item['price'];
                $has_discount = ($item['discount_percent'] > 0);
                if ($has_discount) {
                    $final_price = $item['price'] * (1 - ($item['discount_percent'] / 100));
                }
                ?>
                <div class="card glass-panel menu-card" data-category="<?php echo htmlspecialchars($item['category']); ?>"
                    style="position: relative; transition: transform 0.3s;">
                    <div style="position: relative; cursor: pointer;"
                        onclick='openProductModal(<?php echo json_encode($item); ?>)'>
                        <!-- Image or Placeholder -->
                        <div
                            style="height: 200px; overflow: hidden; border-radius: var(--radius-lg) var(--radius-lg) 0 0; background: #eee; display: flex; align-items: center; justify-content: center; position: relative;">
                            <?php if (!empty($item['image']) && file_exists('images/' . $item['image'])): ?>
                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>"
                                    alt="<?php echo htmlspecialchars($item['item_name']); ?>"
                                    class="card-img" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="text-align: center; color: #888; padding: 1rem;">
                                    <i class="fas fa-image"
                                        style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i><br>
                                    <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Image
                                        Coming
                                        Soon</span>
                                </div>
                            <?php endif; ?>

                            <!-- View Icon Overlay -->
                            <div class="card-img-overlay">
                                <div class="view-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Discount Badge -->
                        <?php if ($has_discount): ?>
                            <div
                                style="position: absolute; top: 10px; left: 10px; background: #ff4757; color: white; padding: 0.25rem 0.5rem; border-radius: 5px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                <?php echo $item['discount_percent']; ?>% OFF
                            </div>
                        <?php endif; ?>

                        <!-- Out of Stock Badge -->
                        <?php if (!$item['is_available']): ?>
                            <div
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem; border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                                Out of Stock
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-body">
                        <div
                            style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <h3 style="margin: 0; font-size: 1.2rem; cursor: pointer;"
                                onclick='openProductModal(<?php echo json_encode($item); ?>)'>
                                <?php echo htmlspecialchars($item['item_name']); ?>
                            </h3>
                            <div style="text-align: right;">
                                <?php if ($has_discount): ?>
                                    <div style="font-weight: bold; color: var(--primary); font-size: 1.1rem;">
                                        ₹<?php echo number_format($final_price, 2); ?>
                                    </div>
                                    <div style="text-decoration: line-through; color: #999; font-size: 0.8rem;">
                                        ₹<?php echo number_format($item['price'], 2); ?>
                                    </div>
                                <?php else: ?>
                                    <div style="font-weight: bold; color: var(--primary); font-size: 1.1rem;">
                                        ₹<?php echo number_format($item['price'], 2); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p
                            style="font-size: 0.9rem; color: #666; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo htmlspecialchars($item['description']); ?>
                        </p>

                        <div style="display: flex; gap: 0.5rem;">
                            <?php if ($item['is_available']): ?>
                                <button onclick="addToCart(<?php echo $item['menu_id']; ?>)" class="btn btn-primary"
                                    style="width: 100%; font-size: 0.9rem;">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            <?php else: ?>
                                <button class="btn" style="width: 100%; background: #ccc; cursor: not-allowed; font-size: 0.9rem;"
                                    disabled>
                                    Unavailable
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Booking Modal (Same as before) -->
<div id="bookingModal" class="glass-panel"
    style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 2000; padding: 2rem; width: 90%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
    <h2 class="text-center">Book a Table</h2>
    <form id="bookingForm" onsubmit="submitBooking(event)">
        <!-- Fields... (Simplified for brevity, assuming existing form structure via JS logic or include) -->
        <p>Call us at 555-0123 for immediate reservations or use form below.</p>
        <!-- Re-using previous form logic here ideally or verifying if user wants full form again -->
        <div class="mb-4">
            <label>Name</label>
            <input type="text" name="name" required style="width: 100%; padding: 0.5rem;">
        </div>
        <div class="mb-4">
            <label>Date</label>
            <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>"
                style="width: 100%; padding: 0.5rem;">
        </div>
        <div class="mb-4">
            <label>Time</label>
            <input type="time" name="time" required style="width: 100%; padding: 0.5rem;">
        </div>
        <div class="mb-4">
            <label>Phone Number</label>
            <input type="tel" name="phone" required pattern="[0-9]{10}" maxlength="10" minlength="10"
                title="Please enter exactly 10 digits" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                placeholder="10-digit mobile number" style="width: 100%; padding: 0.5rem;">
        </div>
        <div class="mb-4">
            <label>Guests</label>
            <input type="number" name="guests" min="1" value="2" required style="width: 100%; padding: 0.5rem;">
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Confirm</button>
            <button type="button" onclick="closeBookingModal()" class="btn" style="background: #ccc;">Cancel</button>
        </div>
    </form>
</div>

<!-- Product Details Modal -->
<div id="productModal" class="glass-panel"
    style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 2000; padding: 2rem; width: 90%; max-width: 800px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border: 1px solid var(--primary);">
    <button onclick="closeProductModal()" class="btn"
        style="position: absolute; top: 1rem; right: 1rem; background: transparent; color: var(--text-main); font-size: 1.5rem;">&times;</button>
    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <img id="modalImg" src=""
                style="width: 100%; height: 300px; object-fit: cover; border-radius: var(--radius-lg);">
        </div>
        <div style="flex: 1; min-width: 300px;">
            <h2 id="modalTitle" style="color: var(--primary); font-size: 2.5rem; margin-bottom: 0.5rem;"></h2>
            <div id="modalPrice" style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;"></div>
            <p id="modalDesc" style="color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;"></p>
            <div style="display: flex; gap: 1rem;">
                <input type="number" id="modalQty" value="1" min="1" max="10" style="width: 60px; padding: 0.5rem;">
                <button id="modalAddToCartBtn" class="btn btn-primary">Add to Cart</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openBookingModal() {
        document.getElementById('bookingModal').style.display = 'block';
    }
    function closeBookingModal() {
        document.getElementById('bookingModal').style.display = 'none';
    }
</script>

<?php require 'includes/footer.php'; ?>