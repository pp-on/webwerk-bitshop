<?php
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

?>
