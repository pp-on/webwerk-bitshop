<?php
/**
 * Template Name: Bestellungen
 *
 * @package Webwerk
 **/


 get_header();

?>
<?php
  the_post();
	the_breadcrumb();
  // the_title('<h1>', '</h1>');

if ( 0 == $current_user->ID ) {
	$logged_in_msg = 'Sie sind noch nicht angemeldet';
	$items_in_cart = null;
} else {
	$logged_in_msg = 'Angemeldet als ' . $current_user->display_name;
	// var_dump( $current_user->user_nicename);
	$items_in_cart = 0;
	// get_user_meta( $current_user->ID, 'cart_item_count', true );
}

?>
 <main class="main" id="content" content="true">
   <div class="shop-section">
	<?php
	$current_cart_id = null;
	global $_POST;
	global $current_user;
	?>
<h1>Meine Bestellungen</h1>

<div class="box-login" role="region" aria-live="polite">
  <h2>Nur für blinde und seheingeschränkte Nutzer</h2>
  <p id="login-status"><?php echo $logged_in_msg; ?></p>
  <a href="<?php echo wp_login_url( home_url() . '/meine-bestellungen/' ); ?>" id="signin-button" class="btn btn-primary customer-signin" role="button" name="button">Anmelden</a>
  <button id="signout-button" class="btn btn-primary" role="button" name="button" disabled="disabled">Abmelden</button>
  <div class="cart-shop-more-btn">
   <a class="btn btn-standard" href="<?php echo esc_html( home_url( 'shop' ) ); ?>">Weiter einkaufen</a>
	</div>
  <a href="<?php echo esc_html( home_url() . '/warenkorb/' . $current_user->user_nicename ); ?>" id="cart" class="cart btn btn-primary" role="button" name="button" aria-description="Warenkorb"><span id="items-count"><?php echo $items_in_cart ? $items_in_cart : ''; ?></span>
	<span class="items-name">Artikel</span>
	<span id="total-price">0 €</span>
  </a>
 </div>
 <p>Hier finden Sie Ihre vergangenen Bestellungen. Digitale Produkte aus diesen Bestellungen können hier heruntergeladen werden. Auch die aktuellen Ausgaben aus Ihren Zeitschriftenabonnements finden Sie auf dieser Seite.</p>


<!-- Neue Query für Zeitschriftenabos -->

  <div class="orders-section">
	<?php
	if ( 0 !== $current_user->ID ) :
		// Filter für Query nach ACF Unterfeld in Repeater.
		function my_posts_where( $where ) {

			$where = str_replace( "meta_key = 'cart_item_$", "meta_key LIKE 'cart_item_%", $where );

			return $where;
		}

		add_filter( 'posts_where', 'my_posts_where' );


		// Query Args: nur Bestellungen bei welchen das Textfeld Erscheinungsform den Inhalt "Zeitschrift" hat.
		$args = array(
			'suppress_filters' => false,
			'numberposts'      => -1,
			'post_type'        => 'customer_order',
			'post_status'      => array( 'draft', 'private' ),
			'author'           => $current_user->ID,
			'meta_query'       => array(
				array(
					'key'     => 'cart_item_$_cart_publicationform',
					'compare' => 'LIKE',
					'value'   => 'Zeitschrift',
				),
			),
		);


		// query
		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) :
			;
			?>
  <h2>Zeitschriften-Abos</h2>
	<div class="orders-list">
	<ul>
			<?php
			// Bestellungen.
			while ( $the_query->have_posts() ) :
				$the_query->the_post();
				$bestellung_id = get_post()->ID;
				$post_date     = get_post()->post_date;
				$post_datetime = strtotime( $post_date );
				?>
	  <li><h3>Zeitschriftenabo aus Bestellung <?php the_title(); ?> vom <?php echo esc_html( date( 'j.m.Y', $post_datetime ) . ' um ' . date( 'H:i', $post_datetime ) ); ?></h3>
			  <ol>
				<?php
				// Artikel.
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

					if ( strpos( $itemPubForms, 'Zeitschrift' ) !== false ) {

						?>

						<?php
						echo '<li>' . $itemAuthor . ' | ' . $itemTitle . ' | ' . $itemPubForms . ' | Best.Nr: ' . $itemID . ' | Preis: ' . $itemPrice . ' € | Menge: ' . $itemAmount;
						// Ausgabe
						if ( ! empty( $itemDownload ) ) {

							echo '<a href="' . $itemDownload['link'] . '" class="btn btn-standard download-btn" download>Download (' . size_format( $itemDownload['filesize'] ) . ')</a>';



						} elseif ( ! empty( $itemSubscriptionDownload ) ) {

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
					};
		endwhile;
				?>
	</ol>
</li>
				<?php
	endwhile;
			?>
</ul>
</div>
			<?php
endif;
		?>

		<?php wp_reset_query();  // Restore global post data stomped by the_post(). ?>
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
<?php else : ?>
  <p>Sie müssen sich anmelden damit Ihre Bestellungen angezeigt werden.</p>
<?php endif; ?>
   </div>
 </main>
 <?php
	get_footer();
