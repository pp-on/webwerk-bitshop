<?php


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
?>
