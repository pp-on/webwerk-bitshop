<?php
// Register Custom Post Type for Zeitschriften
function magazines() {

	$labels = array(
		'name'                  => _x( 'Zeitschriften', 'Post Type General Name', 'webwerk-shop' ),
		'singular_name'         => _x( 'Zeitschrift', 'Post Type Singular Name', 'webwerk-shop' ),
		'menu_name'             => __( 'Zeitschriften', 'webwerk-shop' ),
		'name_admin_bar'        => __( 'Zeitschrift', 'webwerk-shop' ),
		'archives'              => __( 'Zeitschriften Archiv', 'webwerk-shop' ),
		'attributes'            => __( 'Eigenschaften', 'webwerk-shop' ),
		'parent_item_colon'     => __( 'Zeitschriftenabo', 'webwerk-shop' ),
		'all_items'             => __( 'Alle Zeitschriften', 'webwerk-shop' ),
		'add_new_item'          => __( 'Neuen Eintrag hinzufügen', 'webwerk-shop' ),
		'add_new'               => __( 'Neu hinzufügen', 'webwerk-shop' ),
		'new_item'              => __( 'Neue Zeitschrift', 'webwerk-shop' ),
		'edit_item'             => __( 'Zeitschrift bearbeiten', 'webwerk-shop' ),
		'update_item'           => __( 'Zeitschrift aktualisieren', 'webwerk-shop' ),
		'view_item'             => __( 'Zeitschrift ansehen', 'webwerk-shop' ),
		'view_items'            => __( 'Zeitschriften ansehen', 'webwerk-shop' ),
		'search_items'          => __( 'Zeitschrift suchen', 'webwerk-shop' ),
		'not_found'             => __( 'Nicht gefunden', 'webwerk-shop' ),
		'not_found_in_trash'    => __( 'Nicht im Papierkorb', 'webwerk-shop' ),
		'featured_image'        => __( 'Beitragsbild', 'webwerk-shop' ),
		'set_featured_image'    => __( 'Beitragsbild setzen', 'webwerk-shop' ),
		'remove_featured_image' => __( 'Beitragsbild entfernen', 'webwerk-shop' ),
		'use_featured_image'    => __( 'Als Beitragsbild benutzen', 'webwerk-shop' ),
		'insert_into_item'      => __( 'In Zeitschrift einfügen', 'webwerk-shop' ),
		'uploaded_to_this_item' => __( 'Zu dieser Zeitschrift hinzufügen', 'webwerk-shop' ),
		'items_list'            => __( 'Zeitschriftenliste', 'webwerk-shop' ),
		'items_list_navigation' => __( 'Navi für Zeitschriftenliste', 'webwerk-shop' ),
		'filter_items_list'     => __( 'Filtere Zeitschriftenliste', 'webwerk-shop' ),
	);
	$args = array(
		'label'                 => __( 'Zeitschrift', 'webwerk-shop' ),
		'description'           => __( 'Zeitschriften für Abos', 'webwerk-shop' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor' ),
    'supports'              => array( 'title', 'editor', 'comments', 'revisions', 'custom-fields', 'page-attributes' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => 'shop', // Parent menu slug
		// 'menu_position'         => 5,
		'menu_icon'             => 'dashicons-media-document',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
	);
	register_post_type( 'magazine_cpt', $args );

}
add_action( 'init', 'magazines', 0 );
?>

