<?php

namespace fluXtore\Core\Runners;
use Elementor\TemplateLibrary\Source_Local;
use fluXtore\Core\Admin\Providers\fluXtoreS3FreeTemplateProvider;
use fluXtore\Core\Admin\Providers\fluXtoreS3ProTemplateProvider;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Avoid defining the class twice.
if (!class_exists('fluXtore_ImportTemplate')) {      
    if(FLUXTORE_DEFAULT_EDITIOR == "Elementor"){
        class fluXtore_ImportTemplate extends Source_Local {
            /**
             * @var self $instance
             */
            private static $instance;
    
            /**
             * Initializes the core
             */
            public static function init() {
                if ( !is_null( self::$instance ) ) {
                    return;
                }
    
                self::$instance = new self();
            }
    
            public static function maybe_import_template_in_post( $post_id, $file_name, $pro = false ) {
                if (!empty($file_name)) {
                    self::import_template_in_post( $post_id, $file_name, $pro );
                }
            }
    
            public static function import_template_in_post( $post_id, $file_name, $pro ) {
                self::$instance->__import_template_in_post( $post_id, $file_name, $pro );
            }
    
            public function __import_template_in_post( $post_id, $file_name, $pro ) {
                if ( class_exists( "\\Elementor\\Plugin" ) ) {
                    \Elementor\Plugin::$instance->files_manager->clear_cache();
    
                    delete_post_meta( $post_id, '_elementor_data' );
                    delete_post_meta( $post_id, '_elementor_version' );
    
                    if ($pro) {
                        $builder = fluXtoreS3ProTemplateProvider::getContent($file_name);
                    } else {
                        $builder = fluXtoreS3FreeTemplateProvider::getContent($file_name);
                    }
    
                    $builder = add_magic_quotes( $builder );
                    $builder = json_decode( $builder, true );
    
                    $temp = tempnam(".", "tmp");
                    file_put_contents($temp, json_encode( $builder ));
    
                    $result = $this->import_template( "landing", $temp );
                    unlink($temp);
                    $template_id = $result[ 0 ][ 'template_id' ];
    
                    $content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, true );
                    wp_delete_post( $template_id );
    
                    $builder = $this->process_export_import_content( $builder[ 'content' ], 'on_import' );
    
                    update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
                    update_post_meta( $post_id, '_elementor_data', $builder );
                    update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
    
                    $content = \Elementor\Plugin::$instance->frontend->apply_builder_in_content( $content );
    
                    wp_update_post( [
                        "ID" => $post_id,
                        "post_content" => $content
                    ] );
    
                    \Elementor\Plugin::$instance->files_manager->clear_cache();
                }
            }
        }
    } else {
    class fluXtore_ImportTemplate
    {
        /**
         * @var self $instance
         */
        private static $instance;

        /**
         * Initializes the core
         */
        public static function init()
        {
            if (!is_null(self::$instance)) {
                return;
            }

            self::$instance = new self();
        }

        public static function maybe_import_template_in_post($post_id, $file_name, $pro = false)
        {
            if (!empty($file_name)) {
                self::import_template_in_post($post_id, $file_name, $pro);
            }
        }

        public static function import_template_in_post($post_id, $file_name, $pro)
        {
            self::$instance->__import_template_in_post($post_id, $file_name, $pro);
        }

        public function __import_template_in_post($post_id, $file_name, $pro)
        {

        }
    }
    }
    fluXtore_ImportTemplate::init();
}
