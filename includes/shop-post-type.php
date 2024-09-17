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

// Create the Parent Menu "Shop"
function create_shop_parent_menu() {
    add_menu_page(
        __( 'Shop', 'webwerk-shop' ), // Page title
        __( 'Shop', 'webwerk-shop' ), // Menu title
        'manage_options',             // Capability
        'shop',                       // Menu slug
        '',                           // Callback function (if no content, leave it blank)
        'dashicons-store',            // Icon (Dashicon for a store)
        3                             // Menu position
    );
}
add_action( 'admin_menu', 'create_shop_parent_menu' );


 /**
  * Register Custom Post Type für Bestellungen.
  **/
 function order_post_type() {

 	$labels = array(
 		'name'                  => _x( 'Bestellungen', 'Post Type General Name', 'webwerk-shop' ),
 		'singular_name'         => _x( 'Bestellung', 'Post Type Singular Name', 'webwerk-shop' ),
 		'menu_name'             => __( 'Bestellungen', 'webwerk-shop' ),
 		'name_admin_bar'        => __( 'Warenkorb', 'webwerk-shop' ),
 		'archives'              => __( 'Item Archives', 'webwerk-shop' ),
 		'attributes'            => __( 'Item Attributes', 'webwerk-shop' ),
 		'parent_item_colon'     => __( 'Parent Item:', 'webwerk-shop' ),
 		'all_items'             => __( 'All Items', 'webwerk-shop' ),
 		'add_new_item'          => __( 'Add New Item', 'webwerk-shop' ),
 		'add_new'               => __( 'Neue Bestellung', 'webwerk-shop' ),
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
 		'menu_position'         => 4,
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
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-book',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
    'rewrite' => array( 'slug' => 'shop' ), //changes permalink to cpt archive
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
	);
	register_post_type( 'product', $args );

}
add_action( 'init', 'product_cpt', 0 );

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
		'menu_position'         => 5,
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
		'menu_position'         => 5,
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
