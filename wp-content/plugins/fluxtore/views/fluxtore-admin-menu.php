<div id="fx-root"
     data-user="<?php echo wp_get_current_user()->display_name ?>"
     data-email="<?php echo wp_get_current_user()->user_email ?>"
     data-path="<?php echo esc_html( $path ) ?>"
     data-pro="<?php echo ( is_fluxtore_pro() ? "true" : "false" ) ?>"
     data-fluxtore-page="<?php echo get_admin_url( null, 'admin.php?page=fluxtore' ) ?>"
></div>
