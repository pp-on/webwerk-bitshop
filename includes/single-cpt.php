<?php
/**
 * Single CPT Template
 *Diese Datei wird die Logik enthalten, um zwischen den verschiedenen Custom Post Types zu unterscheiden und die entsprechenden Template-Dateien einzubinden.
 * @package Webwerk Bitshop
 */

get_header(); 

global $post;

// Check the post type and include the appropriate template part
if ( 'product' === $post->post_type ) {
    get_template_part( 'template-parts/', 'single-product' );
} elseif ( 'magazine_cpt' === $post->post_type ) {
    get_template_part( 'template-parts/', 'single-magazine' );
}

get_footer();

