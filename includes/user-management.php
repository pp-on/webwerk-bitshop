<?php
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

// Logout hook: Ask customer if he wants to clear shopping-cart.
add_action( 'logout_redirect', 'log_logout', 10, 3 );

?>
