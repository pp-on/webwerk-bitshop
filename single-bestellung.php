<?php
/**
 * Template Name: Bestellung
 *
 * @package Webwerk
 */

 get_header();

 global $post;
 the_breadcrumb();
 $bestellung_id = $post->ID;
 ?>
 <main class="main" id="content" content="true">
   <div class="shop-section">



<!-- Warenkorb -->

<?php
global $post;
$user = wp_get_current_user();
$cart_id = $post->ID;
$checkout_url = home_url() . '/warenkorb-checkout/';
    the_post();
    echo '<h1>Warenkorb</h1>';
    // echo '<h2>Sie sind angemeldet als '. $user->display_name . '</h2>';

/**************************************************
*
* Ausgabe Warenkorb.
*
/**************************************************/
// Cookie checken

    echo '<h2>Das ist die Bestellung '. $post->post_title . '</h2>' .
    '<p>vom ' . $post->post_date ;

    // echo '<p>Warenkorb-Nr: '. $cart_id . '</p>';

    ?>
<p class="info">Hier können Sie Ihre Bestellung überprüfen und darin enthaltene digitale Produkte direkt Herunterladen.</p>


    <?php


      // Anzeige Warnkorb-Formular
      acfe_form('cart-form');
    // Checken auf digitale Produkte.
    // Check rows exists.
if( have_rows('field_616558167898c', $bestellung_id) ):

    // Loop through rows.
    while( have_rows('field_616558167898c', $bestellung_id) ) : the_row();

        // Load sub field value.
      $cart_author = get_sub_field('cart_author');
      $cart_titel = get_sub_field('cart_titel');
     echo 'Autor: ' . $cart_author . 'Titel: ' . $cart_titel . '<br>';
        // Do something...

    // End loop.
    endwhile;
  endif;
    ?>
    <div class="btn-container">
    <div class="cart-shop-more-btn">
     <a class="btn btn-standard" href="<?php echo home_url( 'shop' ); ?>">Weiter einkaufen</a>
   </div>
   <div class="logout-btn">
      <button id="signout-button" class="btn btn-standard" role="button" name="button" aria-disabled="true" disabled>Abmelden</button>
   </div>
   <div class="checkout-back-to-cart">
    <a class="btn btn-primary" href="<?php echo home_url() . '/warenkorb/' . $user->user_nicename; ?>">Zum Warenkorb</a>
   </div>
      </div>
   </div><!-- Ende Section -->
</main>
<?php
/**************************************************
*  Ende: Ausgabe Warenkorb.
/**************************************************/
 get_footer();

// echo home_url('/warenkorb-checkout/' . add_query_arg( array(), $wp->request ) );
