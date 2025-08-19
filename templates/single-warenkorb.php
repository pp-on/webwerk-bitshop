<?php
/**
 * Template Name: Warenkorb
 *
 * @package Webwerk
 */

 get_header();

 global $post;
 the_breadcrumb();
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

    echo '<h2>Das ist der Warenkorb für '. $post->post_title . '</h2>';
    echo '<p>Warenkorb-Nr: '. $cart_id . '</p>';
    $cart_item_count = get_user_meta( $current_user->ID, 'cart_item_count', true );
    if ($cart_item_count == 0):?>
      <p>Sie haben keinen Artikel im Warenkorb. Drücken Sie "Weiter einkaufen" um Artikel auzuwählen.</p>
<?php else:
    ?>
      <p class="info">Hier können Sie Ihren Warenkorb überprüfen, Artikel entfernen oder die gewünschte Anzahl anpassen.</p>
      <p>Wenn Sie keine Anpassungen vornehmen wollen, können Sie gleich auf den Knopf "Zur Kasse" drücken.</p>
      <p> Wenn Sie <span style="font-weight: bold;">Änderungen am Warenkorb</span> vorgenommen haben, drücken Sie bitte zuerst <span style="font-weight: bold;">"Warenkorb aktualisieren"</span> um die Änderungen zu speichern.</p>
<?php endif;
// wenn man noch nicht 'Zur Kasse gedrückt hat'

      // Anzeige Warnkorb-Formular
      acfe_form('cart-form');



    // Buttons.
    ?>
      <!--</div>  das Div wird in den Form Settings geöffnet class=btns -->
<div class="btn-container">
      <div class="cart-shop-more-btn">
       <a class="btn btn-standard" href="<?php echo home_url( 'shop' ); ?>">Weiter einkaufen</a>
      </div>
      <div class="logout-btn">
        <button id="signout-button" class="btn btn-primary" role="button" name="button" aria-disabled="true" disabled>Abmelden</button>
      </div>
      <div class="cart-to-checkout-btn">
       <?php
       echo '<a href="' . esc_url(  add_query_arg( 'cart', $cart_id, $checkout_url ) ). '" class="btn btn-primary">Zur Kasse</a>'; ?>
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
