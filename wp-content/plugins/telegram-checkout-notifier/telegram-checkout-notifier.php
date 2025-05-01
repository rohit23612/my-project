<?php
/**
 * Plugin Name: Telegram Checkout Notifier
 * Description: WooCommerce checkout notifications & OTP via Telegram.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit;
}

// Replace with your actual Telegram bot token and chat ID
define('TELEGRAM_BOT_TOKEN', '7899663492:AAE_Z1Aw9Y4dByOQOmEPwmLQlBfU1j2l5h8');
define('TELEGRAM_CHAT_ID', '6087456240');

/**
 * Function to send a static Telegram message
 */
function send_telegram_static_message() {
    $message = "🚀 Plugin Activated! Telegram integration is working.";
    $message = "your otp is".rand(100000, 999999);
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage?chat_id=" . TELEGRAM_CHAT_ID . "&text=" . urlencode($message);
    
    // Send request using wp_remote_get()
    $response = wp_remote_get($url);
    
    // Alternative method using cURL (for debugging if needed)
    if (is_wp_error($response)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
    }
}

// Hook function to plugin activation
register_activation_hook(__FILE__, 'send_telegram_static_message');

?>
