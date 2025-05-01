<?php 
function price() {
    ob_start();
    
    if (isset($_POST['submit'])) {
        $book1 = $_POST['book1'];
        $book2 = $_POST['book2'];
    }
    
    $args = array(
        'post_type'      => 'books',
        'posts_per_page' => 5,
    );
    
    $query1 = new WP_Query($args);
    $query2 = new WP_Query($args);
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

    <form action="<?php echo esc_url(plugin_dir_url(__FILE__) . 'price.php'); ?>" method="POST">
        <div class="book-selection-container">
            <div class="book-container">
                <h3>Book List 1</h3>
                <input type="text" name="book_search_1" placeholder="Search Books..." id="myInput1">
                <select name="book1">
                    <option value="">Select a book</option>
                    <?php 
                    if ($query1->have_posts()) :
                        while ($query1->have_posts()) : $query1->the_post(); 
                            $title1 = get_the_title(); 
                            ?>
                            <option value="<?php echo esc_attr($title1); ?>" <?php if(isset($book1) && $title1 == $book1) echo "selected"; ?>>
                                <?php echo esc_html($title1); ?>
                            </option>
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
                    if ($query2->have_posts()) :
                        while ($query2->have_posts()) : $query2->the_post(); 
                            $title2 = get_the_title(); 
                            ?>
                            <option value="<?php echo esc_attr($title2); ?>" <?php if(isset($book2) && $title2 == $book2) echo "selected"; ?>>
                                <?php echo esc_html($title2); ?>
                            </option>
                        <?php endwhile; 
                        wp_reset_postdata();
                    else :
                        echo '<option value="">No books found.</option>';
                    endif;
                    ?>
                </select>
            </div>
        </div>

        <button type="submit" name="submit">Go to Price Page</button>
    </form>
    
    <?php
    return ob_get_clean();
}
add_shortcode('pricedata', 'price');
