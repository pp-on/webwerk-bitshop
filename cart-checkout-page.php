<?php
/**
 * Template Name: Warenkorb/Checkout
 *
 * @package Webwerk
 **/


 get_header();

?>
 <main class="main" id="content" content="true">
   <div class="shop-section">
	<?php
	$current_cart_id = null;
	global $_POST;
	global $current_user;
	// JS das nur bei Kasse eingehängt wird.



	$prepare_checkout = 'function disableFields(dataName){
    var disabledFields = jQuery(\'.acf-field[data-name="\'+ dataName + \'"]\').find(\'input\');
    disabledFields.attr(\'readonly\',\'readonly\');}
  disableFields("cart_amount");' .
	'deleteFields = jQuery(\'.acf-field[data-name="cart_item-delete"]\');' .
	'console.log(deleteFields);' .
	'deleteFields.each( function(){ $(this).remove(); });' .
	'acfDeleteUi = jQuery(".acf-js-tooltip");' .
	'acfDeleteUi.each( function(){ $(this).remove(); });';
	wp_add_inline_script( 'ww-cart-js', $prepare_checkout );

	if ( 0 == $current_user->ID ) {
		echo '<p>Sie sind nicht angemeldet!</p>';
		echo '<p>Bitte melden Sie sich an um Artikel zu kaufen.</p>';
		echo '<a href="' . esc_url( home_url() . '/wp-login.php?action=login' ) . '" id="signin-button" class="btn btn-primary customer-signin" type="button" name="button">Anmelden</a>';
		exit;
	}
	echo '<h1>Kasse</h1>';
	echo '<div role="region" aria-live="polite">';
	echo '<p>Sie sind angemeldet als: ' . esc_html( $current_user->display_name ) . '</p>';


	?>

<?php

 // Check for cart.
 $args = array(
	 'post_type'   => 'customer_cart',
	 'post_status' => 'private',
	 'author'      => $current_user->ID,
 );

 $wp_posts = get_posts( $args );

 if ( isset( $wp_posts[0] ) ) {
	 if ( ! ( $wp_posts[0]->post_name == $current_user->user_nicename ) ) {
		  echo "<p class='ww-error-msg'> Etwas ist schief gegangen! Bitte löschen Sie Ihren Warenkorb, melden Sie sich ab und anschließend nochmals an um den Einkauf fortzusetzen.</p>";
		  write_log( 'Fehler: Cart-Slug und Username stimmen nicht überein. [cart-checkout-page.php Z. ' . __LINE__ . ']' );
	 }
	 $current_cart_id = $wp_posts[0]->ID;
 }
 // Abfrage ob Übermittelte Cart-ID übereinstimmt.
 $submitted_id = $_GET['cart'];
 if ( isset( $submitted_id ) && ! ( $submitted_id == $current_cart_id ) ) {
	 echo "<p class='ww-error-msg'> Etwas ist schief gegangen! Bitte löschen Sie Ihren Warenkorb, melden Sie sich ab und anschließend nochmals an um den Einkauf fortzusetzen.</p>";
	 echo '<div class="cart-destoy-btn">';
	 write_log( 'Fehler: Übermittelte Cart-ID ' . $submitted_id . ' und aktuelle ' . $current_cart_id . ' stimmen nicht überein. [cart-checkout-page.php Z. ' . __LINE__ . ']' );
 }



 // Button Warenkorb löschen.
 if ( isset( $_POST['btn-delete-cart'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'btn-delete-cart_' . $current_cart_id ) ) {
	 echo '<p class="success">Der Warenkorb wurde erfolgreich geleert.</p>';
	 write_log( 'Button: Warenkorb leeren gedrückt. Warenkorb: ' . sanitize_title( wp_unslash( $_POST['btn-delete-cart'] ) ) . ' Current: ' . $current_cart_id . ' [cart-checkout-page.php Z. ' . __LINE__ . ']' );
	 if ( $current_cart_id == $_POST['btn-delete-cart'] ) {
		  // $deleted = wp_delete_post($current_cart_id); // so würde der Post/Warenkorb ganz gelöscht.
		  do_action( 'clear_cart', $current_cart_id );
	 }
 } elseif ( isset( $_POST['btn-delete-cart'] ) ) {
	 $_POST['btn-delete-cart'] = null;
	 echo '<p class="warning">Ein Fehler ist aufgetreten. Der Warenkorb konnte nicht geleert werden. Bitte gehen Sie auf "Abmelden" oder "Zurück zum Warenkorb" um die Artikel zu entfernen.</p>';
	 write_log( 'Button: Warenkorb leeren gedrückt. ! konnte nicht verarbeitet werden !' );
 }

 // Wenn Warenkorb vorhanden.
 if ( count( $wp_posts ) ) {

	 $cart_item_count = get_user_meta( $current_user->ID, 'cart_item_count', true );
	 if ( $cart_item_count == 0 && ! ( array_key_exists( '_acf_validation', $_POST ) && $_POST['_acf_validation'] ) ) {
		  echo '<p>Sie haben keinen Artikel im Warenkorb. Drücken Sie "Weiter einkaufen" um Artikel auzuwählen.</p>
      <p>Oder laden Sie weitern unten Artikel aus vergangenen Bestellungen herunter.</p>';
	 } else { // Show ACF on customer_cart post.
		 get_post( $current_cart_id );

		   $formsettings = array(
			   'name'    => 'cart-checkout',
			   'post_id' => $current_cart_id,
		   );
		   acfe_form( $formsettings );}
 } else {
	 echo '<p>Etwas ist schief gegangen. Kein Warenkorb für ' . esc_html( $current_user->display_name ) . ' vorhanden. <br>Bitte melden Sie sich ab. Melden Sie sich dann erneut an um mit dem Einkauf fortzufahren.</p>';
	 echo '<button id="signout-button" class="btn btn-standard" type="button" name="button">Abmelden</button>';
	 // Setzt Metafield für Wagen zurück auf 0.
	 update_user_meta( $current_user->ID, 'shopping_cart_exists', 0, 1 );
 }
  echo '</div>'; // close aria-live region.
	?>
<div class="btn-container">
  <div class="btns">
	<div class="checkout-back-to-cart">
	 <a class="btn btn-standard" href="<?php echo esc_url( home_url() . '/warenkorb/' ) . esc_html( $current_user->user_nicename ); ?>">Zurück zum Warenkorb</a>
	</div>
	<div class="cart-shop-more-btn">
   <a class="btn btn-standard" href="<?php echo esc_html( home_url( 'shop' ) ); ?>">Weiter einkaufen</a>
	</div>
  </div>
   <button id="signout-button" class="btn btn-primary" role="button" name="button">Abmelden</button>
   <form class="" method="post">
	 <button class ="btn btn-standard" type="submit" name="btn-delete-cart" value="<?php echo esc_html( $current_cart_id ); ?>">Warenkorb leeren</button>
   <?php wp_nonce_field( 'btn-delete-cart_' . $current_cart_id ); ?>
   </form>
 </div>
<?php
// Hier alle vergangenen Bestellungen.
$args = array(
	'post_type'   => 'customer_order',
	'post_status' => array( 'private', 'draft' ),
	'author'      => $current_user->ID,
);


$order_query = new WP_Query( $args );

if ( $order_query->have_posts() ) :
	?>
  <div class="orders-section">
	<h2>Alle Bestellungen</h2>
  <div class="orders-list">
  <ul>
	<?php
	while ( $order_query->have_posts() ) :
		$order_query->the_post();
		$bestellung_id = get_post()->ID;
		$post_date     = get_post()->post_date;
		$post_datetime = strtotime( $post_date );
		?>
 <li><h3>Bestellung <?php the_title(); ?> vom <?php echo esc_html( date( 'j.m.Y', $post_datetime ) . ' um ' . date( 'H:i', $post_datetime ) ); ?></h3>
		<?php
		  // the_content();
		  // Checken auf digitale Produkte.
		  // Check rows exists.
		if ( have_rows( 'field_616558167898c', $bestellung_id ) ) :
			?>
	<ol class="item-list-order">

			<?php
			// Loop through rows.
			while ( have_rows( 'field_616558167898c', $bestellung_id ) ) :
				the_row();

				// Load sub field value.
				$itemAuthor               = get_sub_field( 'cart_author' );
				$itemTitle                = get_sub_field( 'cart_titel' );
				$itemPubForms             = get_sub_field( 'cart_publicationform' );
				$itemID                   = get_sub_field( 'cart_order-number' );
				$itemPrice                = get_sub_field( 'cart_price' );
				$itemAmount               = get_sub_field( 'cart_amount' );
				$itemPost                 = get_sub_field( 'item_post' );
				$itemDownload             = get_field( 'field_602541c73159d', $itemPost );
				$itemSubscriptionDownload = get_field( 'field_620f76868bbf2', $itemPost ); // subscription-repeater.
				// Do something...
				echo '<li>' . $itemAuthor . ' | ' . $itemTitle . ' | ' . $itemPubForms . ' | Best.Nr: ' . $itemID . ' | Preis: ' . $itemPrice . ' € | Menge: ' . $itemAmount;
				// Ausgabe - funktioniert noch nicht!
				if ( ! empty( $itemDownload ) ) {

					echo '<a href="' . $itemDownload['link'] . '" class="btn btn-standard download-btn" download>Download (' . size_format( $itemDownload['filesize'] ) . ')</a>';

					// $file_id = $itemDownload["ID"];
					// $mime_type = sanitize_title($itemDownload['mime_type']);
					// echo'<a href="'. plugin_dir_url('__FILE__') .'webwerk-shop/download.php?file='. $file_id .'&mime_type='. $mime_type .'" target="_new">Download File</a>';

				} elseif ( ! empty( $itemSubscriptionDownload ) ) {
					  // echo '<pre>',  var_dump($itemSubscriptionDownload), '</pre>';
					// Check rows exists.
					if ( have_rows( 'field_620f76868bbf2', $itemPost ) ) {
						  echo '<ul class="magazine-issues">';
						  // Loop through rows.
						  $repeat = 0;
						  // Ermitteln wie viele Reihen.
						  $row_count = intval( get_post_meta( $itemPost, 'subscription-repeater', true ) );

						while ( have_rows( 'field_620f76868bbf2', $itemPost ) ) {
							$repeat ++;

							the_row();
							// die letzten drei Ausgaben anzeigen.
							if ( $repeat < ( $row_count - 2 ) ) {
								  continue;
							}

							// Load sub field value.
							$itemDownload  = get_sub_field( 'subscription-download' );
							$magazineIssue = get_sub_field( 'magazine-issue' );
							// if ($repeat > 1) {
							// break;
							// }
							// Do something...
							if ( $itemDownload ) {
								echo '<li><span class="magazine-issue">' . $magazineIssue . '</span>';
								echo '<a href="' . $itemDownload['link'] . '" class="btn btn-standard download-btn" download>Download (' . size_format( $itemDownload['filesize'] ) . ')</a></li>';
							}

							// End loop.
						}
						echo '</ul>';
					}
				} //not empty
				echo '</li>';
				// End loop.
		  endwhile;

			?>
	</ol>
			<?php
		endif;
		?>
</li>
	<?php endwhile; ?>
  </ul>
	<?php
	$totalPrice = get_field( 'field_617bb334332ea', $bestellung_id );
	echo '<p>Gesamtpreis: ' . sprintf( '%01.2f', $totalPrice ) . ' €</p>';
	?>
</div>
</div>
<!-- Ende: orders-section -->
<?php else : ?>
  <div class="orders-section">
	<p>Bisher noch keine Bestellung abgeschlossen.</p>
  </div>
	<?php
endif;
wp_reset_postdata();
?>
   </div>


   </div>
 </main>
 <?php
	get_footer();
