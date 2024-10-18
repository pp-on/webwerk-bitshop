<?php
/**
 * Plugin Name: Webwerk Bitshop
 * Description: Ein Plugin zum Verwalten von Produkten, Bestellungen und Warenkörben.
 * Version: 1.0
 * Author: Webwerk
 * Author URI: https://webwerk-pfennigparade.de/
 *
 * @version WEBWERK_BITSHOP_VERSION
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /
 *
 * @package Webwerk ACF Forms
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WEBWERK_BITSHOP_VERSION', '1.0' );
// Hook um Admintoolbar für alle Benutzer außer Admin zu verstecken.
add_action( 'after_setup_theme', 'remove_admin_bar' );
/**
 * Entferne Admin-Bar.
 */
function remove_admin_bar() {
	if ( ! current_user_can( 'administrator' ) && ! is_admin() ) {
		show_admin_bar( false );
	}
}

global $query;

require_once plugin_dir_path( __FILE__ ) . 'includes/cart.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/checkout.php';
// Include user-management.php conditionally if needed
require_once plugin_dir_path( __FILE__ ) . 'includes/user-management.php';



/*
 * When User logs in, a shopping cart is prepared.
 */
 // action hook.
add_action( 'wp_login', 'ww_shop_prepare_cart', 10, 2 );
 // callback function.

 /**
  * Einkaufswagen bereitstellen
  *
  * @param string $user_login Username (wird nicht übergeben).
  * @param object $user WP_User Objekt.
  */
function ww_shop_prepare_cart( $user_login, object $user ) {
	// Anmeldung loggen.
	write_log( '*********************************************' );
	write_log( 'User: ' . $user->user_nicename . ' hat sich eingeloggt. [plugin.php Z: ' . __LINE__ . ']' );

		// Diese Abfrage verhindert dass ein Warenkorb für den Admin erstellt wird.
	if ( current_user_can( 'administrator' ) ) {
		return;
	}

		// write_log('User Meta [shopping_cart_exists]: ');
		// write_log(get_user_meta($user->ID)['shopping_cart_exists']);.

		// Check if cart exists.
	if ( ! empty( get_user_meta( $user->ID, 'shopping_cart_exists', true ) ) ) {
		// Existierende Körbe loggen.
		write_log(
			'Für ' . $user->user_nicename . ' existiert schon ein Warenkorb: '
		);
		write_log( get_user_meta( $user->ID, 'shopping_cart_exists', false ) );
		write_log( get_user_meta( $user->ID, 'cart_item_count', true ) . ' Artikel sind im Warenkorb' );
	}
	write_log( '*********************************************' );
			$current_customer = $user->ID;

			// von cart-actions.

			// Get cart of customer.
			$args = array(
				'post_type'   => 'customer_cart',
				'post_status' => 'private',
				'author'      => $current_customer,
			);

			// get his cart and destroy surplus carts.
			$current_user_carts = get_posts( $args );
				// Wenn schon Körbe da sind.
			if ( isset( $current_user_carts[0] ) ) {
				// write_log($current_user_carts);
				// Wenn mehr als einer da ist.
				if ( is_array( $current_user_carts ) && count( $current_user_carts ) > 1 ) {
					// delete all other carts.
						write_log( 'more than one cart exist! ' );
					foreach ( $current_user_carts as $key => $user_cart ) {
						// Alle löschen, deren Post-Name auf eine Zahl endet.
						if ( ! ( $user_cart->post_name == $current_user->user_nicename ) ) { // darf kein -Zahl am Ende haben.
							wp_delete_post( $user_cart->ID );
												write_log( 'deleted surplus cart with ID: ' . $user_cart->ID );
						}
					}
						// Array der Körbe neu holen.
						$current_user_carts = get_posts( $args );
				} // Ende: wenn mehr als ein Korb.
				// Das ist die aktuelle Cart ID.
				$current_user_cart_id = $current_user_carts[0]->ID;
						write_log( 'das ist der aktuelle Warenkorb: ' . $current_user_cart_id );

			} else {
				// wenn noch kein Korb da ist.
				write_log( 'Es existiert noch kein Warenkorb für ' . $user->display_name );
				// custom post warenkorb erstellen.
				$new_cart = array(

					'post_name'   => wp_strip_all_tags( $user->user_nicename ), // The name (slug) for your post.
					'post_title'  => wp_strip_all_tags( $user->display_name ),
					'post_author' => $user->ID, // The author of your post.
					'post_status' => 'private', // Default 'draft'.
					'post_type'   => 'customer_cart', // Default 'post'.
				);

				$createcart_success = wp_insert_post( $new_cart );
				if ( $createcart_success ) {
					write_log( 'Korb mit ID: ' . $createcart_success . ' erstellt.' );
					// ID des neuen Korbes in user meta schreiben.
					update_user_meta( $user->ID, 'shopping_cart_exists', $createcart_success );
					// Fehlerprüfung.
					if ( get_user_meta( $user->ID, 'shopping_cart_exists', true ) != $createcart_success ) {
						$error_msg = 'Ein Fehler bei der Übergabe der Warenkorb-ID an die Benutzerdaten ist aufgetreten! Übergabewert: ' . $createcart_success;
						write_log( $error_msg );
						wp_die( esc_html( $error_msg ) );
					}
				} else {
					write_log( 'Fehler beim Erstellen des Warenkorbes: <br>' );
					write_log( $createcart_success );
                    update_user_meta( $user->ID, 'shopping_cart_exists', 0 );
                
				
			}

            }
} //Ende ww:shop_prepare_cart.


/*
 * Set Page templates for CPT "products"
 */

// Templates for the new Pages - which template still shows 'page.php'.
add_filter( 'template_include', 'ww_shop_templates', 99 );

/**
 * Seitentemplates registrieren.
 *
 * @param string $template Seitentemplate.
 * @return string $template Seitentemplate.
 */
function ww_shop_templates( $template ) {
	// global $shop_pages_added;.
	global $post;

	// if ( is_page('Kundenkonto')  && file_exists( plugin_dir_path(__FILE__) . 'customer-account-page.php') ){
	// $template = plugin_dir_path(__FILE__) . 'customer-account-page.php';
		// include $template;
	// }.

	if ( is_page( 'Warenkorb/Checkout' ) && file_exists( plugin_dir_path( __FILE__ ) .  'templates/cart-checkout-page.php' ) ) {
		$template = plugin_dir_path( __FILE__ ) .  'templates/cart-checkout-page.php';
			// include $template;.
	}
	if ( is_page( 'Meine Bestellungen' ) && file_exists( plugin_dir_path( __FILE__ ) .  'templates/orders-page.php' ) ) {
		$template = plugin_dir_path( __FILE__ ) .  'templates/orders-page.php';
			// include $template;.
	}
	if ( is_singular( 'customer_cart' ) && file_exists( plugin_dir_path( __FILE__ ) .  'templates/single-warenkorb.php' ) ) {
		$template = plugin_dir_path( __FILE__ ) .  'templates/single-warenkorb.php';
	}
	if ( is_singular( 'customer_order' ) && file_exists( plugin_dir_path( __FILE__ ) .  'templates/single-bestellung.php' ) ) {
		$template = plugin_dir_path( __FILE__ ) .  'templates/single-bestellung.php';
	}

	return $template;
}

/**
 * Shopseiten erzeugen wenn Plugin aktiviert wird.
 */
function add_shop_pages() {
	global $shop_pages_added;
	// Hier muss noch geprüft werden, ob schon da!
	$shop_pages_added = array();
	$post_id          = null;
	// $post_ids = array();

	// Create post objects.
		$pages = array(
			'customer_account' => array(
				'post_title'   => wp_strip_all_tags( 'Kundenkonto' ),
				'post_content' => 'Das ist Ihr Kundenkonto',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'page',
			),
			// Kasse
			'cart_checkout'    => array(
				'post_title'   => wp_strip_all_tags( 'Warenkorb/Checkout' ),
				'post_content' => 'Das ist Ihr Warenkorb',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'page',
			),
			// Bestellungen
			'customer_orders'  => array(
				'post_title'   => wp_strip_all_tags( 'Meine Bestellungen' ),
				'post_content' => 'Das sind Ihre vergangenen Bestellungen und Downloads',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'page',
			),
		);

		foreach ( $pages as $page ) {
			// Insert the post into the database.
			$post_id = wp_insert_post( $page );
			if ( ! is_wp_error( $post_id ) ) {
				$shop_pages_added[] = $post_id;
				add_filter( 'page_template', 'custom_page_template' );} else {
				// there was an error in the post insertion.
				echo esc_html( $post_id->get_error_message() );
				}
		} // end foreach
} // end function

register_activation_hook( __FILE__, 'add_shop_pages' );
/**
 * Registriert CSS und JS für das Plugin.
 */
function shop_styles_scripts() {
	global $template;

	wp_enqueue_style(
		'ww-shop',
		plugin_dir_url( __DIR__ ) . 'webwerk-bitshop/css/shop.min.css',
		'',
		WEBWERK_BITSHOP_VERSION
	);
	wp_enqueue_script( 'ww-shop-js', plugin_dir_url( __DIR__ ) . 'webwerk-bitshop/js/app.min.js', array( 'jquery' ), WEBWERK_BITSHOP_VERSION, true );
	if ( basename( $template ) === 'single-warenkorb.php' || basename( $template ) === 'cart-checkout-page.php' ) {
		wp_enqueue_script( 'ww-cart-js', plugin_dir_url( __DIR__ ) . 'webwerk-bitshop/js/cart.min.js', array( 'jquery' ), WEBWERK_BITSHOP_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'shop_styles_scripts', 90 );
/**
 * ACF Definitionen & Funktionen.
 */
require_once plugin_dir_path( __FILE__ ) . 'assets/acf-definitions.php';
// require_once plugin_dir_path( __FILE__ ) . 'assets/acf-functions.php';.

//Menu in Dashboard für CPTs.
require_once plugin_dir_path( __FILE__ ) . 'post-types/shop.php';
/**
 * Register post type.
 */
//Taxonomien.
require_once plugin_dir_path( __FILE__ ) . 'taxonomies/publication-form.php';
//Bestellungen.
require_once plugin_dir_path( __FILE__ ) . 'post-types/bestellung.php';
//Produkte.
require_once plugin_dir_path( __FILE__ ) . 'post-types/produkt.php';
//Warenkörbe.
require_once plugin_dir_path( __FILE__ ) . 'post-types/warenkorb.php';
//Zeitschriften.
require_once plugin_dir_path( __FILE__ ) . 'post-types/zeitschrifft.php';
//Templates
require_once plugin_dir_path( __FILE__ ) . 'includes/templates_cpt.php';




/**
 * Register ajax cart-actions.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/cart-actions.php';


// Search for Products.

/**
 * Register custom query vars
 *
 * @param array $vars Query-Variablen.
 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
 */
function ww_shop_register_query_vars( $vars ) {
	$vars[] = 'cat1';
	$vars[] = 'booktitle';
	$vars[] = 'bookauthor';
	$vars[] = 'pubform';
	return $vars;
}
add_filter( 'query_vars', 'ww_shop_register_query_vars' );

// Hook into cart_update ACFE-Form Action to update user_meta 'cart_item_count'.

add_filter( 'acfe/form/submit/post/form=cart-form', 'update_cart_item_count', 10, 5 );

// Search-Template für Produktarchiv.

add_filter( 'template_include', 'my_custom_search_template' );

/**
 * Suchtemplate.
 *
 * @param string $template Templatename.
 */
function my_custom_search_template( $template ) {
	global $wp_query;
	if ( ! $wp_query->is_search ) {
		return $template;
	}

	return plugin_dir_path( __FILE__ ) . 'templates/archive-product.php';

}

// Register REST API for Ajax.
add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'bit-shop/v1/items',
			'/cart-action/',
			array(
				'methods'             => 'POST',
				'callback'            => 'cart_action',
				'permission_callback' => '__return_true',
			)
		);
	}
);


add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'bit-shop/v1/items',
			'/update/',
			array(
				'methods'             => 'GET',
				'callback'            => 'update_cart_items',
				'permission_callback' => '__return_true',
			)
		);
	}
);

add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'bit-shop/v1/logout',
			'/modal/',
			array(
				'methods'             => 'POST',
				'callback'            => 'cart_modal',
				'permission_callback' => '__return_true',
			)
		);
	}
);

add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'bit-shop/v1/logout',
			'/delete-cart/',
			array(
				'methods'             => 'POST',
				'callback'            => 'delete_cart',
				'permission_callback' => '__return_true',
			)
		);
	}
);

/**
 * Rewrite-Regeln beim Aktivieren des Plugins leeren.
 *
 * Diese Funktion wird ausgeführt, wenn das Plugin aktiviert wird.
 * Sie sorgt dafür, dass die Rewrite-Regeln von WordPress aktualisiert werden,
 * um die neuen benutzerdefinierten Beitragstypen und deren Rewrite-Regeln zu berücksichtigen.
 */
function webwerk_bitshop_flush_rewrite_rules() {
    // Rewrite-Regeln leeren
    flush_rewrite_rules();
}

// Aktivierungshook registrieren
register_activation_hook(__FILE__, 'webwerk_bitshop_flush_rewrite_rules');

/**
 * Rewrite-Regeln beim Deaktivieren des Plugins leeren.
 *
 * Diese Funktion wird ausgeführt, wenn das Plugin deaktiviert wird.
 * Sie sorgt dafür, dass die Rewrite-Regeln von WordPress aktualisiert werden,
 * um die Änderungen rückgängig zu machen.
 */
function webwerk_bitshop_deactivate() {
    // Rewrite-Regeln leeren
    flush_rewrite_rules();
}

// Deaktivierungshook registrieren
register_deactivation_hook(__FILE__, 'webwerk_bitshop_deactivate');
