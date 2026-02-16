<?php
// helpers/functions.php

/**
 * Start the session if not already started.
 */
function start_session_safe()
{
    if (session_status() === PHP_SESSION_NONE) {
        // Force a custom session name to avoid conflicts and legacy cookies
        session_name('CafeOnlineSession');

        // Ensure session cookie is accessible across all directories
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '', // Default to current domain
            'secure' => false, // Set to true if using HTTPS
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        session_start();
    }
}

/**
 * Sanitize User Input
 */
function sanitize_input($data)
{
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if User is Logged In
 */
function isLoggedIn()
{
    start_session_safe();
    return isset($_SESSION['user_id']);
}

/**
 * Check if current user is Admin
 */
function isAdmin()
{
    start_session_safe();
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

/**
 * Redirect Helper
 */
function redirect($url)
{
    header("Location: " . $url);
    exit();
}

/**
 * Format Price
 */
function format_price($price)
{
    return '₹' . number_format($price, 2);
}

/**
 * Get Site Setting
 */
function get_setting($key, $default = '')
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        return $value !== false ? $value : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

/**
 * Get Image Source (Handles File Path vs Base64)
 */
function get_image_src($imageVal)
{
    if (empty($imageVal)) {
        return 'https://via.placeholder.com/150?text=No+Image'; // Default placeholder
    }
    // Check if Base64
    if (strpos($imageVal, 'data:') === 0) {
        return $imageVal;
    }
    // Return relative path from root
    return BASE_URL . 'images/' . $imageVal;
}