<?php
 /**
  * Register Custom Post Type für Bestellungenn.
  **/
 function order_post_type() {

     $labels = array(
		'name'                  => _x( 'Bestellungen', 'Post Type General Name', 'webwerk-shop' ),
		'singular_name'         => _x( 'Bestellung', 'Post Type Singular Name', 'webwerk-shop' ),
		'menu_name'             => __( 'Bestellungen', 'webwerk-shop' ),
		'name_admin_bar'        => __( 'Bestellungen', 'webwerk-shop' ),
		'archives'              => __( 'Bestellungübersicht', 'webwerk-shop' ),
		'attributes'            => __( 'Bestellungenigenschaften', 'webwerk-shop' ),
		'parent_item_colon'     => __( 'Elternelement', 'webwerk-shop' ),
		'all_items'             => __( 'Alle Bestellungen', 'webwerk-shop' ),
		'add_new_item'          => __( 'Neues Bestellung hinzufügen', 'webwerk-shop' ),
		'add_new'               => __( 'Neu hinzufügen', 'webwerk-shop' ),
		'new_item'              => __( 'Neues Bestellung', 'webwerk-shop' ),
		'edit_item'             => __( 'Bestellung bearbeiten', 'webwerk-shop' ),
		'update_item'           => __( 'Bestellung aktualisieren', 'webwerk-shop' ),
		'view_item'             => __( 'Bestellung ansehen', 'webwerk-shop' ),
		'view_items'            => __( 'Bestellungen ansehen', 'webwerk-shop' ),
		'search_items'          => __( 'Bestellung suchen', 'webwerk-shop' ),
		'not_found'             => __( 'Nichts gefunden', 'webwerk-shop' ),
		'not_found_in_trash'    => __( 'Nicht im Papierkorb gefunden', 'webwerk-shop' ),
		'featured_image'        => __( 'Bestellungbild', 'webwerk-shop' ),
		'set_featured_image'    => __( 'Bestellungbild hinzufügen', 'webwerk-shop' ),
		'remove_featured_image' => __( 'Bestellungbild entfernen', 'webwerk-shop' ),
		'use_featured_image'    => __( 'Als Bestellungbild verwenden', 'webwerk-shop' ),
		'insert_into_item'      => __( 'In Bestellung einfügen', 'webwerk-shop' ),
		'uploaded_to_this_item' => __( 'Zu diesem Bestellung hochgeladen', 'webwerk-shop' ),
		'items_list'            => __( 'Bestellungliste', 'webwerk-shop' ),
		'items_list_navigation' => __( 'Bestellung-Listennavigation', 'webwerk-shop' ),
		'filter_items_list'     => __( 'Bestellungliste filtern', 'webwerk-shop' ),
 	);
 	$rewrite = array(
 		'slug'                  => 'bestellung',
 		'with_front'            => true,
 		'pages'                 => true,
 		'feeds'                 => true,
 	);
 	$args = array(
 		'label'                 => __( 'Bestellung', 'webwerk-shop' ),
 		'description'           => __( 'Bestellungen', 'webwerk-shop' ),
 		'labels'                => $labels,
 		'supports'              => array( 'title', 'editor', 'author', 'comments', 'custom-fields' ),
 		'hierarchical'          => false,
 		'public'                => true,
        'has_archive'           => true,
 		'show_ui'               => true,
 		'show_in_menu'          => 'shop', // Parent menu slug
         // 'menu_position'         => 5,
        'menu_icon'             => 'dashicons-money',
 		'show_in_admin_bar'     => true,
 		'show_in_nav_menus'     => true,
 		'can_export'            => true,
 		'has_archive'           => true,
 		'exclude_from_search'   => false,
 		'publicly_queryable'    => true,
 		'rewrite'               => $rewrite,
 		'capability_type'       => 'post',
 		'show_in_rest'          => false,
 	);
 	register_post_type( 'customer_order', $args );

 }
 add_action( 'init', 'order_post_type', 0 );
?>

