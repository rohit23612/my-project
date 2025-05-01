<?php
/*
Plugin Name: Custom Post Type Plugin
Plugin URI: https://yourwebsite.com
Description: A simple plugin to add a custom post type called "Books".
Version: 1.0
Author: Your Name
Author URI: https://yourwebsite.com
License: GPL2
*/
include_once plugin_dir_path(__FILE__) . 'price.php';

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Function to register the custom post type
function cpt_register_books() {
    $labels = array(
        'name'               => 'Books',
        'singular_name'      => 'Book',
        'menu_name'          => 'Books',
        'name_admin_bar'     => 'Book',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Book',
        'new_item'           => 'New Book',
        'edit_item'          => 'Edit Book',
        'view_item'          => 'View Book',
        'all_items'          => 'All Books',
        'search_items'       => 'Search Books',
        'not_found'          => 'No books found',
        'not_found_in_trash' => 'No books found in trash'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'books'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('books', $args);
}
add_action('init', 'cpt_register_books');
function book_selection_shortcode() {
    ob_start(); // Output buffering शुरू करें

    $args = array(
        'post_type'      => 'books',
        'posts_per_page' => 5,
    );

    $query = new WP_Query($args);
    ?>
    
    <style>
        .book-selection-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .book-container {
            width: 45%;
        }
        select {
            width: 100%;
            padding: 10px;
        }
    </style>

<form action="<?php echo site_url('/price-page/'); ?>" method="POST">
        <div class="book-selection-container">
            <div class="book-container">
                <h3>Book List 1</h3>
                <input type="text" name="book_search_1" placeholder="Search Books..." id="myInput1">
                <select name="book1">
                    <option value="">Select a book</option>
                    <?php 
                    if ($query->have_posts()) :
                        while ($query->have_posts()) : $query->the_post(); ?>
                            <option value="<?php echo get_the_title(); ?>"><?php the_title(); ?></option>
                        <?php endwhile; 
                        wp_reset_postdata();
                    else :
                        echo '<option value="">No books found.</option>';
                    endif;
                    ?>
                </select>
            </div>

            <div class="book-container">
                <h3>Book List 2</h3>
                <input type="text" name="book_search_2" placeholder="Search Books..." id="myInput2">
                <select name="book2">
                    <option value="">Select a book</option>
                    <?php 
                    if ($query->have_posts()) :
                        while ($query->have_posts()) : $query->the_post(); ?>
                            <option value="<?php echo get_the_title(); ?>"><?php the_title(); ?></option>
                        <?php endwhile; 
                        wp_reset_postdata();
                    else :
                        echo '<option value="">No books found.</option>';
                    endif;
                    ?>
                </select>
            </div>
        </div>

        <button type="submit" name='submit'>Go to Price Page</button>
    </form>

    <?php
    return ob_get_clean();
}

add_shortcode('book_selector', 'book_selection_shortcode');



function my_plugin_enqueue_assets() {
    // Plugin directory URL
    $plugin_url = plugin_dir_url( __FILE__ );

    // CSS File Enqueue
    // wp_enqueue_style( 'my-plugin-style', $plugin_url . 'assets/css/style.css', array(), '1.0.0', 'all' );

    // JavaScript File Enqueue
    wp_enqueue_script( 'my-plugin-script', $plugin_url . 'js/custom.js', array('jquery'), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'my_plugin_enqueue_assets' );

// Flush rewrite rules on plugin activation
function cpt_plugin_activate() {
    cpt_register_books();
    update_option('my_custom_plugin_status', true);
    flush_rewrite_rules();
    
}
register_activation_hook(__FILE__, 'cpt_plugin_activate');

// Flush rewrite rules on plugin deactivation
function cpt_plugin_deactivate() {
    flush_rewrite_rules();
    delete_option('my_custom_plugin_status'); 
}
register_deactivation_hook(__FILE__, 'cpt_plugin_deactivate');


// Template ko register karne ke liye filter add karein
add_filter('theme_page_templates', 'ctp_add_custom_template');
function ctp_add_custom_template($templates) {
    $templates['custom-template.php'] = 'Custom Page Template';
    return $templates;
}

// Agar user ne custom template select kiya hai toh usko load karo
add_filter('template_include', 'ctp_load_custom_template');
function ctp_load_custom_template($template) {
    if (is_page()) {
        $meta = get_post_meta(get_the_ID(), '_wp_page_template', true);
        if ($meta == 'custom-template.php') {
            $template = plugin_dir_path(__FILE__) . 'custom-template.php';
        }
    }
    return $template;
}