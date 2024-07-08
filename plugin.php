<?php
/**
 * Plugin Name: Webwerk Bitshop
 * Description: Ein Plugin zum Verwalten von Produkten, Bestellungen und Warenkörben.
 * Version: 1.0
 * Author: Webwerk
 * Author URI: https://webwerk-pfennigparade.de/
 *
 * @version WEBWERK_SHOP_VERSION
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

define( 'WEBWERK_SHOP_VERSION', '1.0' );
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

/**
 * Alle Einkaufswagen löschen.
 *
 * @param array $user_carts   Zu löschende Wagenobjekte.
 */
function delete_all_carts( $user_carts ) {
	foreach ( $user_carts as $user_cart ) {
		write_log( 'destroy: ' );
		write_log( $user_cart->ID );
		wp_delete_post( $user_cart->ID );
	}

}

/**
 * Routine zum Löschen des Einkaufswagens.
 *
 * @param integer $current_cart_id ID des Einkaufswagens.
 */
function clear_cart_callback( $current_cart_id ) {
	if ( have_rows( 'field_616558167898c', $current_cart_id ) ) :
				$row = 1;
		while ( have_rows( 'field_616558167898c', $current_cart_id ) ) :
			the_row();
			$row_delete_success = delete_row( 'field_616558167898c', 1, $current_cart_id );
			$row_delete_success ? write_log( $row . ' gelöscht' ) : write_log( 'nichts gelöscht.' );
			$row ++;
				endwhile;
		$cart_item_count   = 0; // Zähler auf 0 setzen.
		$upd_success_count = update_user_meta( get_current_user_id(), 'cart_item_count', $cart_item_count );
		write_log( 'user_meta update article count:  ' . $upd_success_count . ' (1=success) | Action: Warenkorb erfolgreich geleert' );
		$cart_items_price  = 0; // Zähler auf 0 setzen.
		$upd_success_price = update_user_meta( get_current_user_id(), 'cart_items_price', $cart_items_price );
		write_log( 'user_meta update total price:  ' . $upd_success_price . ' (1=success) | Action: Gesamtpreis auf 0 gesetzt' );
  endif;
}
add_action( 'clear_cart', 'clear_cart_callback' );

// New Ajax logout.

/**
 * Modal für Artikel hinzufügen.
 *
 * @param object $RESTRequestObj AjaxRequest.
 */
function cart_modal( $RESTRequestObj ) {
	$user = wp_get_current_user();
	// $current_customer = $user->ID;
	$item_in_cart = get_user_meta( $user->ID, 'cart_item_count', true );
	// Modal wenn Artikel im Korb.
	$cart_full_logout_popup_markup = '<div class="cart-full-logout-modal--webwerk" id="cart-modal--webwerk" role="dialog" aria-modal="true" aria-labelledby="cart-modal-head" aria-hidden="false">
		<div class="cart-modal__settings">
			<div class="cart-modal__title">
				<h2 id="cart-modal-head">Wollen Sie den Warenkorb löschen?</h2>
				<p>In Ihrem Warenkorb befinden sich noch Artikel.</p>
				<p>Wenn Sie diese bis zur nächsten Anmeldung speichern möchten, drücken Sie bitte "Behalten".</p>
			</div>
			<div class="c-btns">
					<a href="' . wp_logout_url() . '" class="btn btn-standard c-btn__keep-cart">Behalten</a>
					<a href="' . wp_logout_url() . '" class="btn btn-standard c-btn__delete-cart">Löschen</a>
			</div>
			<div class="cart-modal__abort">
			<p>Wenn Sie doch weiter einkaufen wollen, drücken Sie bitte "Abbrechen".</p>
			<button id="cancel-logout" class="btn btn-standard c-btn__stay">Abbrechen</button>
			</div>
	</div>
	<script>
	jQuery(".c-btn__delete-cart").click(function(){

	// This does the ajax request
	$.ajax({
			type: "POST",
			url: wp_ajax_cart_obj.restURL + "bit-shop/v1/logout/delete-cart",
			beforeSend: function (xhr) {
				xhr.setRequestHeader( "X-WP-Nonce", wp_ajax_cart_obj.restNonce);
			},
			data: {
					"action": "cart_logout",
					"contentType": "application/json",
					"dataType": "json",
			},
			success:function(data) {
				console.log("Löschen");
			},
			error: function(errorThrown){
					console.log(errorThrown);
			}
	});
});

	</script>
	</div>';
	// Modal wenn kein Artikel im Korb.
	$cart_empty_logout_popup_markup = '<div class="cart-empty-logout-modal--webwerk" id="cart-modal--webwerk" role="dialog" aria-modal="true" aria-labelledby="cart-modal-head" aria-hidden="false">
		<div class="cart-modal__settings">
			<div class="cart-modal__title">
				<h2 id="cart-modal-head">Wollen Sie sich wirklich abmelden?</h2>
			</div>
			<div class="c-btns">
					<button id="cancel-logout" class="btn btn-standard c-btn__stay">Nein (Abbrechen)</button>
					<a href="' . wp_logout_url() . '" class="btn btn-standard c-btn__logout">Ja</a>
				</div>
	</div>
	</div>';

	echo json_encode(
		array(
			'markup_full'     => $cart_full_logout_popup_markup,
			'markup_empty'    => $cart_empty_logout_popup_markup,
			'cart_item_count' => $item_in_cart,
		)
	);
};

// Logout hook: Ask customer if he wants to clear shopping-cart.

/**
 * Wagen löschen
 */
function delete_cart() {
					// Falls Warenkorb vorhanden: löschen.
					// Get current user.
					$user             = wp_get_current_user();
					$current_customer = $user->ID;
					write_log( 'Benutzer mit ID ' . $current_customer . ' will ausloggen.' );
					// Get cart of customer.
					$args = array(
						'post_type'   => 'customer_cart',
						'post_status' => 'private',
						'author'      => $current_customer,
					);

					// get his posts.
					$current_user_carts      = get_posts( $args );
					$carts_count_at_checkout = count( $current_user_carts );
					write_log( 'Anzahl Warenkörbe beim Checkout: ' . $carts_count_at_checkout );
					// sind Artikel im Warenkorb?
					// $current_user_cart_id = $current_user_carts[0]->ID;
					// Artikelanzahl checken.
					  $item_in_cart       = get_user_meta( $user->ID, 'cart_item_count', true );
						$cart_items_price = get_user_meta( $user->ID, 'cart_items_price', true );

						delete_all_carts( $current_user_carts );

					$success_reset_cart_count       = update_user_meta( $user->ID, 'shopping_cart_exists', 0 );
					$success_reset_article_count    = update_user_meta( $user->ID, 'cart_item_count', 0 );
					$success_reset_cart_items_price = update_user_meta( $user->ID, 'cart_items_price', 0 );
					write_log( 'Warenkorbzähler zurückgesetzt: ' . $success_reset_cart_count . ' | Artikelzähler zurückgesetzt: ' . $success_reset_article_count . ' | Gesamtpreis zurückgesetzt: ' . $success_reset_cart_items_price );
					write_log( 'Ausgeloggt (mit Löschen): ' . $user->user_nicename . '   [plugin.php Z: ' . __LINE__ . ']' );
}


/**
 * Abmeldung loggen mit Redirect zum Shop.
 *
 * @param string $redirect_to URL Ziel.
 * @param string $requested_redirect_to URL angefragter Redirect.
 * @param object $user Benutzer.
 */
function log_logout( $redirect_to, $requested_redirect_to, object $user ) {
				write_log( 'Ausgeloggt: ' . $user->user_nicename );
				write_log( ' $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ [plugin.php Z: ' . __LINE__ . ']' );
				return home_url() . '/shop';
}

add_action( 'logout_redirect', 'log_logout', 10, 3 );

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

}


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
		plugin_dir_url( __DIR__ ) . 'webwerk-shop/css/shop.min.css',
		'',
		WEBWERK_SHOP_VERSION
	);
	wp_enqueue_script( 'ww-shop-js', plugin_dir_url( __DIR__ ) . 'webwerk-shop/js/app.min.js', array( 'jquery' ), WEBWERK_SHOP_VERSION, true );
	if ( basename( $template ) === 'single-warenkorb.php' || basename( $template ) === 'cart-checkout-page.php' ) {
		wp_enqueue_script( 'ww-cart-js', plugin_dir_url( __DIR__ ) . 'webwerk-shop/js/cart.min.js', array( 'jquery' ), WEBWERK_SHOP_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'shop_styles_scripts', 90 );
/**
 * ACF Definitionen & Funktionen.
 */
require_once plugin_dir_path( __FILE__ ) . 'assets/acf-definitions.php';
// require_once plugin_dir_path( __FILE__ ) . 'assets/acf-functions.php';.


/**
 * Register post type.
 */
require_once dirname( __FILE__ ) . '/shop-post-type.php';




/**
 * Register ajax cart-actions.
 */
require_once dirname( __FILE__ ) . '/cart-actions.php';


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

/**
 * Artikelanzahl
 *
 * @param integer $post_id Created/Updated post ID.
 * @param string  $type     Action type: 'insert_post' or 'update_post'.
 * @param array   $args     Generated post arguments.
 * @param array   $form     Form settings.
 * @param string  $action   Action name.
 */
function update_cart_item_count( $post_id, $type, $args, $form, $action ) {
	// Hier werden die Artikel im Korb gezählt.
	$current_customer    = $args['post_author'];
	$current_cart        = $args['ID'];
	$article_count       = 0;
	$article_items_price = 0;
	if ( have_rows( 'field_616558167898c', $current_cart ) ) :
		 // Loop through rows.
		while ( have_rows( 'field_616558167898c', $current_cart ) ) :
			the_row();
			 $article_count      += get_sub_field( 'cart_amount' );
			$article_items_price += (int) get_sub_field( 'cart_amount' ) * (float) get_sub_field( 'cart_price' );
		   endwhile;
		update_user_meta( $current_customer, 'cart_item_count', $article_count );
			write_log( 'Warenkorb-Update | User: ' . $current_customer . ' | Artikel-Count: ' . $article_count . '| [plugin.php Z 409]' );
		update_user_meta( $current_customer, 'cart_items_price', $article_items_price );
			write_log( 'Warenkorb-Update | User: ' . $current_customer . ' | Gesamtpreis: ' . $article_items_price . '| [plugin.php Z 413]' );
	else :
		update_user_meta( $current_customer, 'cart_item_count', $article_count );
			write_log( 'Warenkorb-Update | User: ' . $current_customer . ' | Artikel-Count: ' . $article_count . '| [plugin.php Z 416]' );
		update_user_meta( $current_customer, 'cart_items_price', 0 );
			write_log( 'Warenkorb-Update | User: ' . $current_customer . ' | Gesamtpreis: 0 | [plugin.php Z 418]' );
	endif;
	// update_user_meta($current_customer, 'cart_item_count', $cart_item_count);.
}
/**
 * ACFE Kasse: Bestellung schicken loggen, Wagen löschen.
 *
 * @param array $form Form settings.
 */
function checkout_actions( $form ) {
	write_log( '########################## Bestellung abgeschickt ############################' );

	$current_cart_id = $form['post_id'];
		write_log( 'Warenkorb ' . $current_cart_id . ' löschen' );
	do_action( 'clear_cart', $current_cart_id );
}

add_action( 'acfe/form/submit/form=cart-checkout', 'checkout_actions', 10, 1 );

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

	return dirname( __FILE__ ) . '/archive-product.php';

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


// register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
// register_activation_hook( __FILE__, 'forms_flush_rewrites' );
