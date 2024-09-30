<?php
/*****************************************

 *  Enqueue Ajax for Cart Buttons         +
https://developer.wordpress.org/plugins/javascript/enqueuing/
 ******************************************/

/**
 * Enqueue Ajax scripts and assets for Frontend.
 */
function shop_ajax_enqueue() {
	// Enqueue javascript on the frontend. For Cart actions.
	wp_enqueue_script(
		'shop-ajax-script',
		plugins_url( '/js/ajaxquery.js', __FILE__ ),
		array( 'jquery' ),
		WEBWERK_BITSHOP_VERSION,
		false
	);

	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'shop-ajax-script',
		'wp_ajax_cart_obj',
		array(
			'restURL'   => rest_url(),
			'restNonce' => wp_create_nonce( 'wp_rest' ),
		)
	);

	// Zweite Route für Update Cart Items.
	wp_enqueue_script(
		'ajax-update-cart-items',
		plugins_url( '/js/update-cart-items.js', __FILE__ ),
		array( 'jquery' ),
		WEBWERK_BITSHOP_VERSION,
		false
	);

	wp_localize_script(
		'ajax-update-cart-items',
		'wp_ajax_update_cart_obj',
		array(
			'restURL'   => rest_url(),
			'restNonce' => wp_create_nonce( 'wp_rest' ),
		)
	);

}
add_action( 'wp_enqueue_scripts', 'shop_ajax_enqueue' );


// Function to check if article already in cart.
function update_item_count( $item, $cart_id ) {
	$article_in_cart = false; // Ausgangswert.
	if ( have_rows( 'field_616558167898c', $cart_id ) ) {
		while ( have_rows( 'field_616558167898c', $cart_id ) ) {
			the_row();
			if ( $item == get_sub_field( 'cart_order-number' ) ) {
				$value = get_row();
				$row   = get_row_index();
				++$value['field_616553902ca50']; // Wert des Feldes cart_amount erhöhen.
				update_row( 'field_616558167898c', $row, $value, $cart_id );
				write_log( 'Artikel: ' . $item . ' Anzahl: ' . $value['field_616553902ca50'] );
				$article_in_cart = true;
			}
		} // end while have rows
	} // end if have rows
	return $article_in_cart;
	// gibt false zurück wenn nicht im Wagen.
}


/**
 * Artikel zum Warenkorb.
 *
 * @param object $RESTRequestObj Ajaxobjekt.
 */
function cart_action( $RESTRequestObj ) {
		global $post;
		global $current_user; // User Object.
		// Get current user.
		$current_customer = get_current_user_id();
		// Get cart of customer.
		$args = array(
			'post_type'   => 'customer_cart',
			'post_status' => 'private',
			'author'      => $current_customer,
		);

		// get his cart.
		$current_user_carts = get_posts( $args );
		if ( isset( $current_user_carts[0] ) ) {

			if ( is_array( $current_user_carts ) && count( $current_user_carts ) > 1 ) {
				// delete all other carts.
				write_log( 'surplus carts exist! | [cart-actions.php' . __LINE__ . ']' );
			}
			// Das ist die aktuelle Cart ID.
			$current_user_cart_id = $current_user_carts[0]->ID;

			write_log( 'das ist der aktuelle Warenkorb: ' . $current_user_cart_id );

		} else {
			$current_user_cart_id = 0;
		}

		// AJAX Fields.
		$item         = $RESTRequestObj->get_body_params();
		$item         = json_decode( $item['item'] );
		$itemAuthor   = $item->itemAuthor;
		$itemTitle    = $item->itemTitle;
		$itemPrice    = $item->itemPrice;
		$itemID       = $item->itemId;
		$itemPubForms = $item->itemPubforms;
		$itemDLurl    = $item->itemUrl;
		$itemPost     = $item->itemPost;

		/**********************************************
		*
		* Zeige Popup nach Button "In den Warenkorb"
		*/
		$cart_popup_markup = '<div class="add-to-cart-modal--webwerk" id="cart-modal--webwerk" role="dialog" aria-modal="true" aria-labelledby="cart-modal-head" aria-hidden="false">
        <div class="cart-modal__settings">
          <div class="cart-modal__title">
            <h2 id="cart-modal-head">Artikel wurde in den Warenkorb gelegt</h2>
            <h3 class="add-to-cart-modal__item-title">' . $itemTitle . '</h3><p>als ' . $itemPubForms . '<br> mit Bestellnummer: ' . $itemID . '</p>
          </div>
          <div class="c-btns">
              <button class="btn btn-standard c-btn__shop-on">Weiter einkaufen</button>
              <a href="' . esc_html( home_url() . '/warenkorb/' . $current_user->user_nicename ) . '" class="btn btn-primary c-btn__to-cart">Zum Warenkorb</a>
            </div>
      </div>
      </div>';

		$item_in_cart = true;
		// Artikelanzahl checken.
		if ( $current_user_cart_id ) {
			$item_in_cart = update_item_count( $itemID, $current_user_cart_id );
		}

		// Wenn Artikel noch nicht im Korb.

		// write_log('Im Wagen ' . $item_in_cart);
		// Wenn noch nicht im Wagen, hinzufügen:
		if ( ! $item_in_cart ) {
			// Build Array for ACF.
			$row = array(
				'cart_author'          => $itemAuthor,
				'cart_titel'           => $itemTitle,
				'cart_publicationform' => $itemPubForms,
				'cart_order-number'    => $itemID,
				'cart_price'           => $itemPrice,
				'cart_amount'          => 1,
				'cart_url'             => $itemDLurl,
				'item_post'            => $itemPost,
			);

			// Zum Warenkorb hinzufügen.

			add_row( 'field_616558167898c', $row, $current_user_cart_id );
			// $item = $itemParams['item'];
			write_log( 'Artikel in Warenkorb gelegt von User-ID: ' . $current_customer );
			write_log( 'in Warenkorb-ID: ' . $current_user_cart_id );
			write_log( '################## Artikel: ###################### | [cart-actions.php Z.' . __LINE__ . ']' );
				write_log( $item );
				write_log( '---------------- Artikel Ende -------------' );
		}
		// Hier lese ich Anzahl in user-meta aus DB
		$cart_item_count = get_user_meta( $current_customer, 'cart_item_count', true );

		// füge eins hinzu
		++$cart_item_count;

		// Hier lese ich Gesamtpreis in user-meta aus DB
		$cart_items_price = get_user_meta( $current_customer, 'cart_items_price', true );
		// füge Preis hinzu.
		$cart_items_price = (float) $cart_items_price + (float) $itemPrice;

		// Schreibe zurück in DB und gebe Wert zurück.
		$upd_success = update_user_meta( $current_customer, 'cart_item_count', $cart_item_count );
		write_log( 'user_meta update: ' . $upd_success . ' (1=success) | User: ' . $current_customer . ' | Artikel-Count: ' . $cart_item_count . ' | [cart-actions.php Z. ' . __LINE__ . ']' );
		$upd_success = update_user_meta( $current_customer, 'cart_items_price', $cart_items_price );
		write_log( 'user_meta update: ' . $upd_success . ' (1=success) | User: ' . $current_customer . ' | Gesamtpreis: ' . $cart_items_price . ' | [cart-actions.php Z. ' . __LINE__ . ']' );
		$cart_items = array(
			'cart_item_count'  => $cart_item_count,
			'cart_items_price' => $cart_items_price,
		);
		  // Now we'll return it to the javascript function.
		  // Anything outputted will be returned in the response.
		echo json_encode(
			array(
				'markup'     => $cart_popup_markup,
				'cart_items' => $cart_items,
			)
		);
		// Always die in functions echoing ajax content.
		die();
}

add_action( 'wp_ajax_cart_action', 'cart_action' );
/**********************************************
*
* Ende: von Cart Action Handler.
*----------------------------------------------
* Beginn Update Cart Handler.
*/
add_action( 'wp_ajax_update_cart_item_count', 'update_cart_items' );

/**
 * Korb aktualisieren.
 */
function update_cart_items() {
	$cart_item_count  = get_user_meta( get_current_user_id(), 'cart_item_count', true ); // 3rd: retrieve single meta field.
	$cart_items_price = get_user_meta( get_current_user_id(), 'cart_items_price', true );
	$cart_items       = array(
		'cart_item_count'  => $cart_item_count,
		'cart_items_price' => $cart_items_price,
	);
	return $cart_items;
	die(); // all ajax handlers should die when finished.
}



// Warenkorb aktualisieren -> Anzahl und Löschen.
// Löschen muss mit js erfolgen. Dann post update action.
// add_filter('acfe/form/prepare/post/form=cart-form', 'update_fields', 10, 4);.
/**
 * Felder aktualisieren.
 *
 * @param bool    $prepare Wahr/Falsch.
 * @param array   $form formsettings.
 * @param integer $post_id ID.
 * @param string  $action Aktionsname.
 */
function update_fields( $prepare, $form, $post_id, $action ) {
	$item_to_delete     = null;
	$row_delete_success = false;
	$prepare            = true;
	// Löschen von Einträgen im Warenkorb.
	/**
	 * Get the form input value of checkbox from each row.
	 */
	  _log( '------------------------- Artikel aus Warenkorb löschen: ----------------------- | [cart-actions.php Z. ' . __LINE__ . ']' );
	 // Schauen welche Reihen gelöscht werden sollen.
	if ( have_rows( 'cart_item' ) ) :
		while ( have_rows( 'cart_item' ) ) :
			the_row();
			if ( get_sub_field( 'cart_item-delete' ) ) {
				$items_to_delete[] = get_row_index();
				_log( get_row_index() . ' wird gelöscht' );
			}
		  endwhile;
		// Ende: Schauen welche Reihen gelöscht werden sollen.

		// Löschen.
		for ( $i = 0; $i < count( $items_to_delete ); $i++ ) {
			// Reihe löschen.
			$row_delete_success = delete_row( 'field_616558167898c', $items_to_delete[ $i ], $post_id );
				// Reihenindex anpassen.
			for ( $n = $i; $n < count( $items_to_delete ); $n++ ) {
				--$items_to_delete[ $n ];
			}
			if ( $row_delete_success ) {
				write_log( 'Artikel ' . $item_to_delete . ' gelöscht! | [cart-actions.php' . __LINE__ . ']' );
			} else {
				write_log( 'Fehler beim Löschen eines Artikels aus dem Warenkorb! | [cart-actions.php Z. ' . __LINE__ . ']' );
				$prepare = false;
				// Ausgabe in Popup??
				echo 'Artikel konnte nicht aus Warenkorb gelöscht werden.';
			}
		}

	  endif;
	  return $prepare;
}
