<?php
/*
Plugin Name: My Custom Plugin
Description: Custom functionality ke liye plugin.
Version: 1.0
Author: Aapka Naam
*/
function custom_login_message($message) {
    return "Welcome to my custom page!";
}
add_shortcode('display_message', 'custom_login_message');
