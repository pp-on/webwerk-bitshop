<?php
/**
 * Template Name: Shop Artikelübersicht (Spalten)
 *
 * @package Webwerk
 **/

 get_header();
 global $wp_query;
 global $current_user;

?>

 <main class="main" id="content" content="true">
   <div class="shop-section">
<?php
  the_post();
	the_breadcrumb();
  // the_title('<h1>', '</h1>');
?>
<h1>Shop</h1>
<?php

	$posts_per_page = get_option( 'posts_per_page' );

  $page = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
if ( ! $page ) {
	$page = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
}
  $tax_query  = array(
	  array(
		  'taxonomy'         => 'publicationform',
		  'field'            => 'term_id',
		  'terms'            => get_query_var( 'pubform' ),
		  'include_children' => true,
		  'operator'         => 'IN',
	  ),
  );
		$args = array(
			's'              => $s,
			'post_type'      => array( 'product', 'magazine_cpt' ),
			'paged'          => $page,
			'posts_per_page' => $posts_per_page,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'cat'            => get_query_var( 'cat1' ),
		 // 'meta_query'      => $meta_query
		);
		if ( ! empty( get_query_var( 'pubform' ) ) ) {
			$args['tax_query'] = $tax_query;
		}
		// The query.
		$the_query = new WP_Query( $args );




		?>
<p class="page-current page-blog__current">
	<?php
	echo sprintf(
		/* translators: 1: aktuelle Seite, 2: Seiten insgesamt */
		esc_html__( 'Seite %1$s von %2$s', 'webwerk' ),
		$page,
		$the_query->max_num_pages
	);
	  // the_content();
	 $current_uri = esc_url( home_url( add_query_arg( null, null ) ) );
	$category     = null;
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

<div class="box-login" role="region" aria-live="polite">
  <h2>Nur für blinde und seheingeschränkte Nutzer</h2>
  <p id="login-status"><?php echo $logged_in_msg; ?></p>
  <a href="<?php echo wp_login_url(home_url() . '/shop/'); ?>" id="signin-button" class="btn btn-primary customer-signin" role="button" name="button">Anmelden</a>
  <button id="signout-button" class="btn btn-primary" role="button" name="button" disabled="disabled">Abmelden</button>
  <a href="<?php echo esc_html( home_url() . '/meine-bestellungen/' ); ?>" id="signin-button" class="btn btn-primary customer-signin" role="button" name="button">Bestellungen/Downloads </a>
  <a href="<?php echo esc_html( home_url() . '/warenkorb/' . $current_user->user_nicename ); ?>" id="cart" class="cart btn btn-primary" role="button" name="button" aria-description="Warenkorb"><span id="items-count"><?php echo $items_in_cart ? $items_in_cart : ''; ?></span>
	<span class="items-name">Artikel</span>
	<span id="total-price">0 €</span>
  </a>
 </div>

<div class="search-area">
  <h2>Suche</h2>
  <form class="product-search" action="<?php echo esc_html( home_url() ); ?>" method="get">
	  <label for="search-text">Suche in allen Feldern</label>
	  <input id="search-text" type="search" name="s" placeholder="Suchbegriff..." value="<?php echo get_search_query(); ?>" />
	  <!-- <label for="booktitle">Titel:</label>
	  <input id="s-title" role="search" type="text" name="booktitle" value="
	  <?php
		// echo get_query_var('booktitle');
		?>
	  ">
	<label for="bookauthor">Autor:</label>
	<input id="s-author" role="search" type="text" name="bookauthor" value="
	<?php
	// echo get_query_var('bookauthor');
	?>
	"> -->
	<label for="cat1">Kategorie:</label>
	<?php
	wp_dropdown_categories(
		array(
			'name'              => 'cat1',
			'option_none_value' => 'Keine',
			'show_option_all'   => 'Alle',
			'hierarchical'      => 1,
			'class'             => 'select refresh',
			'selected'          => isset( $_GET['cat1'] ) ? $_GET['cat1'] : '',
		)
	);
	?>
		<label for="pubform">Erscheinungsform:</label>
		<?php
		wp_dropdown_categories(
			array(
				'taxonomy'          => 'publicationform',
				'name'              => 'pubform',
				'option_none_value' => 'Keine',
				'show_option_all'   => 'Alle',
				'hierarchical'      => 1,
				'class'             => 'select refresh',
				'selected'          => isset( $_GET['pubform'] ) ? $_GET['pubform'] : '',
			)
		);
		?>
			<input type="hidden" name="post_type" value="['product', 'magazine_cpt']" />
	<button class="btn btn-primary" type="submit" value="Send" accesskey="s">Suchen</button>
	<!-- <button class="btn btn-primary" type="submit" value="" accesskey="del">Suche zurücksetzen</button> -->
  </form>
</div>

 <?php
	$searchTerm    = '';
	$searchCat     = '';
	$searchPubform = '';

	// Wenn Suche, dann diese Überschrift
	if ( isset( $_GET ) && $_GET ) {
		$searchTerm    = $_GET['s'];
		$searchCat     = $_GET['cat1'];
		$searchPubform = $_GET['pubform'];

		if ( $searchCat == 0 ) {
			$searchCat = ' alle Kategorien';
			if ( $searchTerm && $searchPubform ) {
				$searchCat = ' in allen Kategorien';
			} else {
				$searchCat = '';
			}
		} else {
			if ( $searchTerm ) {
				$searchCat = ' in Kategorie "' . get_cat_name( $searchCat ) . '"';
			} else {
				$searchCat = ' Kategorie "' . get_cat_name( $searchCat ) . '"';
			}
		}

		if ( $searchTerm ) {
			$searchTerm = ' "' . $searchTerm . '"';

		}

		if ( $searchPubform ) {
			$searchPubform = get_term( $searchPubform, 'publicationform' );
			$searchPubform = ' als ' . $searchPubform->name;
		} else {
			$searchPubform = '';
		}
		echo '<h2>Suchergebnis für' . $searchTerm . $searchCat . $searchPubform . '</h2>';
	} else {
		// sonst diese:
		echo '<h2>Alle Produkte</h2>';
	}


	?>
  <div class="product-list <?php echo esc_html( sanitize_title( $category ) ); ?>">
  <?php


	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) :
			$the_query->the_post();
			$post_id      = get_the_ID();
			$categories   = null;
			$category     = '';
			$categoryname = '';
			$categories   = get_the_category();
			if ( $category ) {
				$category = $categories[0]->category_nicename;
			}


			if ( count( $categories ) > 1 ) {
				foreach ( $categories as $category ) {
					$categoryname .= $category->name . ' | ';
				}
				$categoryname = rtrim( $categoryname, ' | ' );

			} else {
				$categoryname = $categories[0]->name;
			}


			?>
	 <div class="product-box">
	  <div class="product-header">
			<?php
			// product details.
			$title        = get_field( 'title' ) ? get_field( 'title' ) : '';
			$author       = get_field( 'author' ) ? get_field( 'author' ) : '';
			$imprints     = get_field( 'imprints' ) ? get_field( 'imprints' ) : '';
			$year_edition = get_field( 'year_edition' ) ? get_field( 'year_edition' ) : '';
			// add dash btw place and year & combine to $imprints
			$imprints     = $imprints && $year_edition ? $imprints . ' – ' . $year_edition : $imprints;
			$imprints     = $imprints ? $imprints : $year_edition;
			$duration     = get_field( 'field_60255163fc965' ) ? get_field( 'field_60255163fc965' ) : '';
			$id           = get_field( 'id' ) ? get_field( 'id' ) : '';
			$price        = get_field( 'price' );
			$price_comma  = number_format( $price, 2, ',', '.' ) ?: '';
			$audio_sample = get_field( 'sample' ) ? get_field( 'sample' ) : '';
			// DL-URL wurde durch File Upload ersetzt!
			// if ( get_field( 'download_url' ) ) {
			// $download_url = 'data-item-download-url="' . get_field( 'download_url' ) . '" ';
			// } else {
			// $download_url = '';
			// }
			// $description   = get_field( 'description' ) ?: '';
			// $product_image = get_field( 'product_image' );
			// if ($product_image) {
			// $img_path      = get_picture_data( $product_image, 'ww-small', $title );
			// $img_path      = $product_image['url'];
			// $size = 'ww-small';
			// $thumb = $product_image['sizes'][ $size ];
			// $alt = $product_image['alt'];
			// $caption = $product_image['caption'];
			// $img_tag           =  '<img src="' . esc_url($thumb) . '" alt="' . $alt . '" />';
			// }else{
			// $img_path          = esc_url(plugin_dir_url( __FILE__ ) . 'assets/img/img.png');
			// $img_tag           =  '<img src="' . $img_path . '" alt="" />';
			// }
			$pubforms         = '';
			$publicationforms = get_the_terms( get_the_ID(), 'publicationform' );
			if ( $publicationforms ) {
				foreach ( $publicationforms as $publicationform ) {
					$pubforms .= $publicationform->name . ', ';
				}
				$pubforms = rtrim( $pubforms, ', ' );
			}

			echo '<h3><span class="product-title">' . $title . ' </span>' . ' <span class="product-author">' . $author . '</span></h3>';
			echo '<p class="product-category">' . $categoryname . '</p>';
			echo '<p class="product-publicationform">' . $pubforms . '</p>';
			if ( $audio_sample ) {
				echo '<div class="product-player"><h2>' . 'Hörprobe' . '</h2>';
				echo do_shortcode( '[audio src="' . $audio_sample . '"]' );
				echo '</div>';
			}
			?>
	  </div>
	<div class="product-footer">
			<?php
			echo '<p class="product-price">' . $price_comma . ' €</p>';
			echo '<a href="' . get_permalink() . '" class="btn btn-primary">Details</a>';
			echo '
        <button class="btn btn-cart add-item" aria-label="In den Warenkorb" type="button"
          data-item-id="' . $id . '"
          data-item-price="' . $price . '"
          data-item-url="' . $current_uri . '"
          data-item-post ="' . $post_id . '"

          data-item-author="' . $author . '"
          data-item-title="' . $title . '"
          data-item-pubforms="' . $pubforms . '"
          data-item-max-quantity="1">
          <span class="icon-container"><svg role="img" class="symbol" aria-hidden="true" focusable="false"><use xlink:href="' . get_template_directory_uri() . '/img/icons.svg#shopping-cart"></use></svg></span>
          <span class="title">In den Warenkorb</span>
        </button>';
			?>

	</div>
  </div>
			<?php
	endwhile;

		?>
  </div>
 <!-- end of the loop -->



 <!-- pagination here -->

 <div class="box-previousandnext">
		<?php
		$big = 999999999; // need an unlikely integer
		echo paginate_links(
			array(
				'base'          => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'        => '?paged=%#%',
				'current'       => max( 1, get_query_var( 'paged' ) ),
				'total'         => $the_query->max_num_pages,
				'before_number' => '<span class="screen-reader-text">Seite</span>',
				'prev_text'     => __( '&laquo; Previous' ),
				'next_text'     => __( 'Next &raquo;' ),
				'type'          => 'list',
			)
		);
		?>
 </div>
		<?php
 else :
	 echo '<p>Für diese Anfrage wurden keine übereinstimmenden Produkte gefunden</p>';
endif;
wp_reset_postdata();
	?>
	</div>
 </main>
	<?php
	get_footer(); ?>
