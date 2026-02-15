<?php
// home.php - New Landing Page
require 'config/db_connect.php';
require 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="particles"></div>
    <!-- Blended overlay for better text readability -->
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: linear-gradient(to right, rgba(26,15,0,0.95) 0%, rgba(26,15,0,0.7) 50%, rgba(26,15,0,0.1) 100%); z-index:1;"></div>
    
    <div class="hero-splash"></div>

    <div class="hero-content">
        <div class="hero-text">
            <span style="color: var(--primary); letter-spacing: 2px; text-transform: uppercase; font-size: 0.9rem; margin-bottom: 1rem; display: block; font-weight: 600;">Welcome to Midnight Cafe</span>
            <h1 class="hero-title">
                Taste the <br>
                <span class="text-gold" style="font-family: var(--font-heading); font-style: italic;">Art of Coffee</span>
            </h1>
            <p class="hero-subtitle">
                Where coding meets caffeine. Experience our signature dark roasts, crafted for the night owls, dreamers, and creators.
            </p>
            <div style="display:flex; gap:1.5rem; flex-wrap: wrap;">
                <a href="index.php" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 1rem;">View Menu <i class="fas fa-arrow-right" style="margin-left: 0.5rem;"></i></a>
                <a href="about.php" class="btn btn-accent" style="padding: 1rem 2.5rem; font-size: 1rem;">About Us</a>
            </div>
            
            <div style="margin-top: 4rem; display: flex; gap: 3rem; align-items: center;">
                 <div>
                     <h4 style="color:var(--primary); font-size: 2rem; margin:0;">4.9</h4>
                     <p style="margin:0; font-size:0.8rem; color: #888;">Average Rating</p>
                 </div>
                 <div style="width: 1px; height: 40px; background: rgba(255,255,255,0.1);"></div>
                 <div>
                     <h4 style="color:var(--primary); font-size: 2rem; margin:0;">24/7</h4>
                     <p style="margin:0; font-size:0.8rem; color: #888;">Open All Night</p>
                 </div>
            </div>
        </div>
        <!-- Right side is handled by background splash image in CSS -->
    </div>
</section>

<!-- Featured Section -->
<section style="padding: 4rem 0; background: var(--bg-dark);">
    <div class="container text-center">
        <h2 style="margin-bottom: 1rem;">Why Midnight Cafe?</h2>
        <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 3rem;">More than just a coffee shop. It's a sanctuary for productivity and relaxation.</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div class="glass-panel" style="padding: 2rem; border: 1px solid rgba(212, 163, 115, 0.2);">
                <i class="fas fa-wifi" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem;"></i>
                <h3>High-Speed WiFi</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted);">Blazing fast internet for seamless streaming and coding.</p>
            </div>
             <div class="glass-panel" style="padding: 2rem; border: 1px solid rgba(212, 163, 115, 0.2);">
                <i class="fas fa-couch" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem;"></i>
                <h3>Cozy Ambiance</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted);">Ergonomic seating and ambient lighting for maximum comfort.</p>
            </div>
             <div class="glass-panel" style="padding: 2rem; border: 1px solid rgba(212, 163, 115, 0.2);">
                <i class="fas fa-mug-hot" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem;"></i>
                <h3>Premium Brews</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted);">Sourced from the finest beans and roasted to perfection.</p>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
