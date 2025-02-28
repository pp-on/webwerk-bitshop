<?php
/**
 * Template Name: Kundenkonto
 *
 * @package Webwerk
 **/

 get_header();

	   global $current_user;
	   global $post;
	   $cart_id      = $post->ID;
	   $checkout_url = home_url() . '/warenkorb-checkout/';
?>
 <main class="main" id="content" content="true">
   <div class="shop-section">
	<?php
		the_breadcrumb();
		the_title( '<h1>', '</h1>' );
		the_post();
		the_content();


	acfe_form( 'delivery-address' );
	?>
  <div class="btn-container">
  <div class="checkout-back-to-cart">
   <a class="btn btn-standard" href="<?php echo esc_url( home_url() . '/warenkorb/' . $current_user->user_nicename ); ?>">Zurück zum Warenkorb</a>
  </div>
  <div class="cart-to-checkout-btn">
	<?php
	echo '<a href="' . esc_url( add_query_arg( 'cart', $cart_id, $checkout_url ) ) . '" class="btn btn-primary">Zur Kasse</a>';
	?>
 </div>
 <div class="logout-btn">
	 <button id="signout-button" class="btn btn-standard" role="button" name="button" aria-disabled="true" disabled>Abmelden</button>
 </div>
  <div class="cart-shop-more-btn">
   <a class="btn btn-standard" href="<?php echo esc_url( home_url( 'shop' ) ); ?>">Weiter einkaufen</a>
  </div>
  </div>
  </div><!-- Ende Section -->
 </main>
 <?php
	get_footer();
