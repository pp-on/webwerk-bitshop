<?php
/**
 * Register Custom Post Type für Produkte.
 **/

 function product_cpt() {

	$labels = array(
		'name'                  => _x( 'Produkte', 'Post Type General Name', 'webwerk-shop' ),
		'singular_name'         => _x( 'Produkt', 'Post Type Singular Name', 'webwerk-shop' ),
		'menu_name'             => __( 'Produkte', 'webwerk-shop' ),
		'name_admin_bar'        => __( 'Produkte', 'webwerk-shop' ),
		'archives'              => __( 'Produktübersicht', 'webwerk-shop' ),
		'attributes'            => __( 'Produkteigenschaften', 'webwerk-shop' ),
		'parent_item_colon'     => __( 'Elternelement', 'webwerk-shop' ),
		'all_items'             => __( 'Alle Produkte', 'webwerk-shop' ),
		'add_new_item'          => __( 'Neues Produkt hinzufügen', 'webwerk-shop' ),
		'add_new'               => __( 'Neu hinzufügen', 'webwerk-shop' ),
		'new_item'              => __( 'Neues Produkt', 'webwerk-shop' ),
		'edit_item'             => __( 'Produkt bearbeiten', 'webwerk-shop' ),
		'update_item'           => __( 'Produkt aktualisieren', 'webwerk-shop' ),
		'view_item'             => __( 'Produkt ansehen', 'webwerk-shop' ),
		'view_items'            => __( 'Produkte ansehen', 'webwerk-shop' ),
		'search_items'          => __( 'Produkt suchen', 'webwerk-shop' ),
		'not_found'             => __( 'Nichts gefunden', 'webwerk-shop' ),
		'not_found_in_trash'    => __( 'Nicht im Papierkorb gefunden', 'webwerk-shop' ),
		'featured_image'        => __( 'Produktbild', 'webwerk-shop' ),
		'set_featured_image'    => __( 'Produktbild hinzufügen', 'webwerk-shop' ),
		'remove_featured_image' => __( 'Produktbild entfernen', 'webwerk-shop' ),
		'use_featured_image'    => __( 'Als Produktbild verwenden', 'webwerk-shop' ),
		'insert_into_item'      => __( 'In Produkt einfügen', 'webwerk-shop' ),
		'uploaded_to_this_item' => __( 'Zu diesem Produkt hochgeladen', 'webwerk-shop' ),
		'items_list'            => __( 'Produktliste', 'webwerk-shop' ),
		'items_list_navigation' => __( 'Produkt-Listennavigation', 'webwerk-shop' ),
		'filter_items_list'     => __( 'Produktliste filtern', 'webwerk-shop' ),
	);
 	$rewrite = array(
 		'slug'                  => 'produkt',
 		'with_front'            => true,
 		'pages'                 => true,
 		'feeds'                 => true,
 	);
	$args = array(
		'label'                 => __( 'Produkt', 'webwerk-shop' ),
		'description'           => __( 'Product', 'webwerk-shop' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'comments', 'revisions', 'custom-fields', 'page-attributes' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => 'shop', // Parent menu slug
		// 'menu_position'         => 5,
		'menu_icon'             => 'dashicons-book',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
        'rewrite' => $rewrite, 
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
	);
	register_post_type( 'product', $args );

}
add_action( 'init', 'product_cpt', 0 );
?>

