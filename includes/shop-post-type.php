<?php
/**
 * Filter the single_template for cpt.
 *
 * @param string $single Single template.
 */

function ww_shop_single_template( $single ) {
    global $post;

    if ( 'product' === $post->post_type || 'magazine_cpt' === $post->post_type ) {
        if ( file_exists( plugin_dir_path( __FILE__ ) . 'single-cpt.php' ) ) {
            return plugin_dir_path( __FILE__ ) . 'single-cpt.php';
        }
    }

    return $single;
}
/**
 * Filter the archive for our cpt.
 *
 * @param string $archive_template Archive Template.
 */

function get_custom_post_type_template( $archive_template ) {
 global $post;
 $plugin_root_dir = WP_PLUGIN_DIR.'/webwerk-shop/';

 if (is_archive() && get_post_type($post) == 'product') {
      $archive_template = $plugin_root_dir.'/archive-product.php';
 }
 return $archive_template;
}

add_filter( 'archive_template', 'get_custom_post_type_template' ) ;



