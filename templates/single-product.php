<?php
/**
 * Produkte
 *
 * @package Webwerk Shop
 */

 get_header();
?>
 <main class="main" id="content" content="true">
   <div class="shop-section">
	<?php
	global $post;
	  the_breadcrumb();
			the_post();
	  $post_id = get_the_ID();
			// the_hero_content();
			// acf values.
			$title        = get_field( 'title' ) ?: '';
			$author       = get_field( 'author' ) ?: '';
		  $narrator       = get_field( 'narrator' ) ? : '';
			$imprints     = get_field( 'imprints' ) ?: '';
			$year_edition = get_field( 'year_edition' ) ?: '';
			// add dash btw place and year & combine to $imprints
			$imprints    = $imprints && $year_edition ? $imprints . ' – ' . $year_edition : $imprints;
			$imprints    = $imprints ? $imprints : $year_edition;
			$duration    = get_field( 'field_60255163fc965' ) ?: '';
			$id          = get_field( 'id' ) ?: '';
		  $price         = get_field( 'price' );
			$price_comma = number_format( $price, 2, ',', '.' ) ?: '';
	// DL URL ersetzt durch file upload.
	// if ( get_field( 'download_url' ) ) {
	// $download_url = 'data-item-download-url="' . get_field( 'download_url' ) . '" ';
	// } else {
	// $download_url = '';
	// }
			$description = get_field( 'description' ) ?: '';

			$audio_sample = get_field( 'sample' ) ?: '';

	  $categories       = get_the_category();
	  $publicationforms = get_the_terms( get_the_ID(), 'publicationform' );
	  $pubforms         = '';
	  // $tags = get_tags();
			$product_html = '';
	?>

	  <div class="product-box product-single product-grid">
		  <!-- <div class="product-header"> -->
		  <div class="product-header-content">
			  <?php
				echo '<h1><span class="product-title">' . $title . '</span>' . ' <span class="product-author">' . $author . '</span></h1>';
				?>
			<div class="audio-sample">
			  <?php
				if ( $audio_sample ) {
					echo '<div class="product-player"><h2>' . 'Hörprobe' . '</h2>';
					echo do_shortcode( '[audio src="' . $audio_sample . '"]' );
					echo '</div>';
				}
				?>
			</div>
		  </div>
		<div class="product-header-aside">
		  <?php
			echo '<h2>Preis</h2>';
			echo '<p class="product-price">' . $price_comma . ' €</p>';
			?>
		</div>
	  <!-- </div> -->
		<?php
		echo '<div class="product-main">';
		echo '<div class="product-content">';
		echo '<h2>Inhalt</h2>';
		echo '<p>' . $description . '</p>';
		echo '</div>';

		echo '<div class="product-info">';

		if ( $narrator ) {
			  echo '<h2 class="product-narrator-h">Sprecher</h2>';
			  echo '<p class="product-narrator">' . $narrator . '</p>';
		}
		echo '<h2 class="product-publisher-h">Verlag</h2>';
		echo '<p class="product-publisher">' . $imprints . '</p>';
		echo '<h2 class="product-data-h">Daten</h2>';
		echo '<p class="product-data"><span>Bestellnummer: ' . $id . '</span><br><span>Umfang/Dauer: ' . $duration . '</span>
    ';
		// echo '<p>Preis: <span class="product-details">' . $price . ' €</span></p>';
		// echo '<p>' . $download_url . '</p>';
		if ( count( $categories ) > 1 ) {
			echo '<h2 class="product-cat-h">Kategorien</h2>';
			echo '<p class="product-cat">';
			$categories = wp_list_pluck( $categories, 'name' );
			$categories = implode( '; ', $categories );
			echo $categories;
			// foreach ( $categories as $category ) {
			// var_dump($categories);
			// echo ( $category->name );
			// }
			echo '</p>';
		} else {
			echo '<h2 class="product-cat-h">Kategorie</h2>';
			echo '<p class="product-cat">' . ( $categories[0]->name ) . '</p>';
		}
		echo '<h2 class="product-nature-h">Erscheinungsform</h2>';
		if ( $publicationforms ) {
			foreach ( $publicationforms as $publicationform ) {
				$pubforms .= $publicationform->name . ', ';
			}
		}
		$pubforms = rtrim( $pubforms, ', ' );
		echo '<p class="product-nature">' . $pubforms . '</p>';
		echo '</div></div>';
		?>


	<div class="product-btn">

	<a href="<?php echo wp_login_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>" id="signin-button" class="btn btn-primary customer-signin" role="button" name="button">Anmelden</a>
	  <?php
		// show add-to-cart-button if user is logged in
		echo '
            <button class="btn btn-cart add-item" aria-label="In den Warenkorb" type="button"
              data-item-id="' . $id . '"
              data-item-price="' . $price . '"
              data-item-url="' . get_permalink() . '"
              data-item-post ="' . $post_id . '"

              data-item-author="' . $author . '"
              data-item-title="' . $title . '"
              data-item-pubforms="' . $pubforms . '">
              <span class="icon-container"><svg role="img" class="symbol" aria-hidden="true" focusable="false"><use xlink:href="' . get_template_directory_uri() . '/img/icons.svg#shopping-cart"></use></svg></span>
              <span class="title">In den Warenkorb</span>
            </button>';
		echo '</div>';
		echo '<a class="btn btn-standard" href="' . esc_html( home_url( 'shop' ) ) . '">Zur Artikelübersicht</a>';
		?>
  </div>

  </div><!-- Ende Section -->
 </main>
 <?php
	get_footer();
