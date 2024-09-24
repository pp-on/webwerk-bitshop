<?php
// Register Warenkorb Custom Post Type
function customer_cart_post_type() {

	$labels = array(
		'name'                  => _x( 'Warenkörbe', 'Post Type General Name', 'webwerk-shop' ),
		'singular_name'         => _x( 'Warenkorb', 'Post Type Singular Name', 'webwerk-shop' ),
		'menu_name'             => __( 'Warenkörbe', 'webwerk-shop' ),
		'name_admin_bar'        => __( 'Warenkörbe', 'webwerk-shop' ),
		'archives'              => __( 'Item Archives', 'webwerk-shop' ),
		'attributes'            => __( 'Item Attributes', 'webwerk-shop' ),
		'parent_item_colon'     => __( 'Parent Item:', 'webwerk-shop' ),
		'all_items'             => __( 'Alle Warenkörbe', 'webwerk-shop' ),
		'add_new_item'          => __( 'Neuen Warenkorb erstellen', 'webwerk-shop' ),
		'add_new'               => __( 'Neuer Warenkorb', 'webwerk-shop' ),
		'new_item'              => __( 'New Item', 'webwerk-shop' ),
		'edit_item'             => __( 'Edit Item', 'webwerk-shop' ),
		'update_item'           => __( 'Update Item', 'webwerk-shop' ),
		'view_item'             => __( 'View Item', 'webwerk-shop' ),
		'view_items'            => __( 'View Items', 'webwerk-shop' ),
		'search_items'          => __( 'Search Item', 'webwerk-shop' ),
		'not_found'             => __( 'Not found', 'webwerk-shop' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'webwerk-shop' ),
		'featured_image'        => __( 'Featured Image', 'webwerk-shop' ),
		'set_featured_image'    => __( 'Set featured image', 'webwerk-shop' ),
		'remove_featured_image' => __( 'Remove featured image', 'webwerk-shop' ),
		'use_featured_image'    => __( 'Use as featured image', 'webwerk-shop' ),
		'insert_into_item'      => __( 'Insert into item', 'webwerk-shop' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'webwerk-shop' ),
		'items_list'            => __( 'Items list', 'webwerk-shop' ),
		'items_list_navigation' => __( 'Items list navigation', 'webwerk-shop' ),
		'filter_items_list'     => __( 'Filter items list', 'webwerk-shop' ),
	);
	$rewrite = array(
		'slug'                  => 'warenkorb',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'Warenkorb', 'webwerk-shop' ),
		'description'           => __( 'Warenkorb', 'webwerk-shop' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'author','custom-fields'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => 'shop', // Parent menu slug
		// 'menu_position'         => 5,
    'menu_icon'             => 'dashicons-cart',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
		'show_in_rest'          => false,
	);
	register_post_type( 'customer_cart', $args );

}
add_action( 'init', 'customer_cart_post_type', 0 );
?>

