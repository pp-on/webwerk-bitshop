<?php
/**
 * Post Type functions.
 *
 * @package Webwerk Shop
 **/

 /**
  * Register Custom Taxonomy for Products and Magazines.
  **/

 // Register Custom Taxonomy
 function publication_form() {
 	// // global current user
 	//  $current_user = wp_get_current_user();

 	$labels = array(
 		'name'                       => _x( 'Erscheinungsform', 'Taxonomy General Name', 'webwerk' ),
 		'singular_name'              => _x( 'Erscheinungsformen', 'Taxonomy Singular Name', 'webwerk' ),
 		'menu_name'                  => __( 'Erscheinungsform', 'webwerk' ),
 		'all_items'                  => __( 'All Items', 'webwerk' ),
 		'parent_item'                => __( 'Parent Item', 'webwerk' ),
 		'parent_item_colon'          => __( 'Parent Item:', 'webwerk' ),
 		'new_item_name'              => __( 'New Item Name', 'webwerk' ),
 		'add_new_item'               => __( 'Neue Erscheinungsform hinzufügen', 'webwerk' ),
 		'edit_item'                  => __( 'Edit Item', 'webwerk' ),
 		'update_item'                => __( 'Update Item', 'webwerk' ),
 		'view_item'                  => __( 'View Item', 'webwerk' ),
 		'separate_items_with_commas' => __( 'Separate items with commas', 'webwerk' ),
 		'add_or_remove_items'        => __( 'Add or remove items', 'webwerk' ),
 		'choose_from_most_used'      => __( 'Choose from the most used', 'webwerk' ),
 		'popular_items'              => __( 'Popular Items', 'webwerk' ),
 		'search_items'               => __( 'Erscheinungsform suchen', 'webwerk' ),
 		'not_found'                  => __( 'Not Found', 'webwerk' ),
 		'no_terms'                   => __( 'No items', 'webwerk' ),
 		'items_list'                 => __( 'Items list', 'webwerk' ),
 		'items_list_navigation'      => __( 'Items list navigation', 'webwerk' ),
 	);
 	$args   = array(
 		'labels'            => $labels,
 		'hierarchical'      => true,
 		'public'            => true,
 		'show_ui'           => true,
 		'show_admin_column' => true,
 		'show_in_nav_menus' => true,
 		'show_tagcloud'     => true,
 		'show_in_rest'      => true,
 		'meta_box_cb'       => 'post_categories_meta_box',
 	);
 	register_taxonomy( 'publicationform', array( 'product', 'magazine_cpt' ), $args );

 }
 add_action( 'init', 'publication_form', 0 );
?>
