<?php

/**
 * @package fluXtore
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>

    <style>
        #fluxtore-loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            background-color: rgba(1, 1, 1, 0.8);
            width: 100vw;
            height: 100vh;
            z-index: 999;
        }

        #fluxtore-loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
            vertical-align: middle;
            horiz-align: center;
            position: absolute;
            top: calc(50vh - 60px);
            left: calc(50vw - 60px);
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
		

		a button.upsell-downsell-yes-button {
			cursor: pointer;
			padding:5px 5px;
		}
		a button.upsell-downsell-no-button {
			cursor: pointer;
			padding:5px 5px;
		}
		

    </style>

</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<div class="fluxtore-container" >
    <div class="fluxtore-primary">        
    <?php
    global $fluxtore_show_header, $fluxtore_show_footer;
	/* Include header into the default template */
	    if ($fluxtore_show_header) {
            get_header();
        }
		 
        if(FLUXTORE_DEFAULT_EDITIOR == "Bricks"){
           
            if ( have_posts() ) {
                while ( have_posts() ) {
                    the_post();
            
                    $post_id     = get_the_ID();
                    $post_type   = get_post_type();

                    if (class_exists('Bricks\Helpers')) {
                        $bricks_data = Bricks\Helpers::get_bricks_data( $post_id, 'content' );
                        $preview_id  = Bricks\Helpers::get_template_setting( 'templatePreviewPostId', $post_id );

                        // Render Bricks data
                        if ( $bricks_data ) {
                            Bricks\Frontend::render_content( $bricks_data );
                        }

                        // Render default post layout
                        elseif ( $post_type === 'post' ) {
                            get_template_part( 'template-parts/post' );
                        }

                        // Previewing Bricks Template without content template assigned: Fallback to preview ID WordPress content
                        elseif ( $post_type === BRICKS_DB_TEMPLATE_SLUG && $preview_id ) {
                            echo '<main id="brx-content">' . apply_filters( 'the_content', get_post( $preview_id )->post_content ) . '</main>';
                        }

                        // Default content
                        else {
                            echo '<main id="brx-content" class="brxe-container layout-default">';
                            the_content();
                            echo Bricks\Helpers::page_break_navigation();
                            echo '</main>';
                        }
                    } else {
                        // Fallback if Bricks\Helpers class does not exist
                        echo '<main id="brx-content" class="brxe-container layout-default">';
                        the_content();
                        echo '</main>';
                    }
                }
            }
        } else if(FLUXTORE_DEFAULT_EDITIOR == "Divi"){
            $show_default_title = get_post_meta( get_the_ID(), '_et_pb_show_title', true );

            $is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );

            ?>
            <div id="page-container">
            <div id="main-content">
                <?php
                    if ( et_builder_is_product_tour_enabled() ):
                        // load fullwidth page in Product Tour mode
                        while ( have_posts() ): the_post(); ?>

                            <article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?>>
                                <div class="entry-content">
                                <?php
                                    the_content();
                                ?>
                                </div>

                            </article>

                    <?php endwhile;
                    else:
                ?>
                <div class="container">
                    <div id="content-area" class="clearfix">
                        <div id="left-area">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <?php
                            /**
                             * Fires before the title and post meta on single posts.
                             *
                             * @since 3.18.8
                             */
                            do_action( 'et_before_post' );
                            ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?>>
                                <?php if ( ( 'off' !== $show_default_title && $is_page_builder_used ) || ! $is_page_builder_used ) { ?>
                                    <div class="et_post_meta_wrapper">
                                        <!-- <h1 class="entry-title"><?php the_title(); ?></h1> -->

                                    <?php
                                        if ( ! post_password_required() ) :

                                            //et_divi_post_meta();

                                            $thumb = '';

                                            $width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

                                            $height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
                                            $classtext = 'et_featured_image';
                                            $titletext = get_the_title();
                                            $alttext = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
                                            $thumbnail = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );
                                            $thumb = $thumbnail["thumb"];

                                            $post_format = et_pb_post_format();

                                            if ( 'video' === $post_format && false !== ( $first_video = et_get_first_video() ) ) {
                                                printf(
                                                    '<div class="et_main_video_container">
                                                        %1$s
                                                    </div>',
                                                    et_core_esc_previously( $first_video )
                                                );
                                            } else if ( ! in_array( $post_format, array( 'gallery', 'link', 'quote' ) ) && 'on' === et_get_option( 'divi_thumbnails', 'on' ) && '' !== $thumb ) {
                                                print_thumbnail( $thumb, $thumbnail["use_timthumb"], $alttext, $width, $height );
                                            } else if ( 'gallery' === $post_format ) {
                                                et_pb_gallery_images();
                                            }
                                        ?>

                                        <?php
                                            $text_color_class = et_divi_get_post_text_color();

                                            $inline_style = et_divi_get_post_bg_inline_style();

                                            switch ( $post_format ) {
                                                case 'audio' :
                                                    $audio_player = et_pb_get_audio_player();

                                                    if ( $audio_player ) {
                                                        printf(
                                                            '<div class="et_audio_content%1$s"%2$s>
                                                                %3$s
                                                            </div>',
                                                            esc_attr( $text_color_class ),
                                                            et_core_esc_previously( $inline_style ),
                                                            et_core_esc_previously( $audio_player )
                                                        );
                                                    }

                                                    break;
                                                case 'quote' :
                                                    printf(
                                                        '<div class="et_quote_content%2$s"%3$s>
                                                            %1$s
                                                        </div>',
                                                        et_core_esc_previously( et_get_blockquote_in_content() ),
                                                        esc_attr( $text_color_class ),
                                                        et_core_esc_previously( $inline_style )
                                                    );

                                                    break;
                                                case 'link' :
                                                    printf(
                                                        '<div class="et_link_content%3$s"%4$s>
                                                            <a href="%1$s" class="et_link_main_url">%2$s</a>
                                                        </div>',
                                                        esc_url( et_get_link_url() ),
                                                        esc_html( et_get_link_url() ),
                                                        esc_attr( $text_color_class ),
                                                        et_core_esc_previously( $inline_style )
                                                    );

                                                    break;
                                            }

                                        endif;
                                    ?>
                                </div>
                            <?php  } ?>

                                <div class="entry-content">
                                <?php
                                    do_action( 'et_before_content' );

                                    the_content();

                                    wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
                                ?>
                                </div>
                                <div class="et_post_meta_wrapper">
                                <?php
                                if ( et_get_option('divi_468_enable') === 'on' ){
                                    echo '<div class="et-single-post-ad">';
                                    if ( et_get_option('divi_468_adsense') !== '' ) echo et_core_intentionally_unescaped( et_core_fix_unclosed_html_tags( et_get_option('divi_468_adsense') ), 'html' );
                                    else { ?>
                                        <a href="<?php echo esc_url( strval( et_get_option( 'divi_468_url' ) ) ); ?>"><img src="<?php echo esc_attr( et_get_option( 'divi_468_image' ) ); ?>" alt="468" class="foursixeight" /></a>
                            <?php 	}
                                    echo '</div>';
                                }

                                /**
                                 * Fires after the post content on single posts.
                                 *
                                 * @since 3.18.8
                                 */
                                do_action( 'et_after_post' );

                                    if ( ( comments_open() || get_comments_number() ) && 'on' === et_get_option( 'divi_show_postcomments', 'on' ) ) {
                                        comments_template( '', true );
                                    }
                                ?>
                                </div>
                            </article>

                        <?php endwhile; ?>
                        </div>
                     
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
            <?php
        } else {
            while ( have_posts() ) {
                the_post();
                the_content();
            }
        }
       
        ?>       
    </div>
</div>

<?php wp_footer(); ?>

<div id="fluxtore-loader-wrapper" style="display: none">
    <div id="fluxtore-loader"></div>
</div>

<script>
    let loader = jQuery('#fluxtore-loader-wrapper');

    const show_loader = () => {
        loader.show();
    }

    const hide_loader = () => {
        loader.hide();
    }
</script>

<?php

if ($fluxtore_show_footer) get_footer();

?>

</body>
</html>
