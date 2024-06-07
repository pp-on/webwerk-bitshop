<?php
/**
 * ACF Funktionen.
 *
 * @package Webwerk Shop
 */

/**
 * Registriert einen ACF-Block.
 */
function cregister_acf_block_types() {
	// register a mailform block.
	acf_register_block_type(
		array(
			'name'            => 'product',
			'title'           => 'Shop Artikel',
			'description'     => 'Selektor für Shop Artikel',
			'render_callback' => 'procuct_acf_block_render_cb',
			'category'        => 'webwerk',
			'icon'            => 'book',
			'keywords'        => array( 'Produkt', 'Shop', 'Artikel', 'Webwerk' ),
		)
	);
	function procuct_acf_block_render_cb( $block ) {
		if ( file_exists( plugin_dir_path( __DIR__ ) . 'blocks/product/product.php' ) ) {
			include plugin_dir_path( __DIR__ ) . 'blocks/product/product.php';
		}
	}
}

// Check if function exists and hook into setup.
if ( function_exists( 'acf_register_block_type' ) ) {
	add_action( 'acf/init', 'cregister_acf_block_types' );
}

$title = get_field('title') ? get_field('title') : '';
$author = get_field('author') ? get_field('author') : '';
$narrator = get_field('narrator') ? get_field('narrator') : '';
$imprints = get_field('imprints') ? get_field('imprints'): '';
$year_edition = get_field('year_edition') ? get_field('year_edition') : '';
$duration = get_field('duration') ? get_field('duration') : '';
$id = get_field('id') ? get_field('id') : '';
$price = get_field('price') ? get_field('price') : '';
$download_url = get_field('download_url') ? get_field('download_url'): '';
$audio_sample = get_field('sample') ? get_field('sample') : '';
$description = get_field('description') ? get_field('description') : '';
$product_image = get_field('product_image') ? get_field('product_image') : '';

echo '<p>' . $title . '</p>';
echo '<p>' . $author . '</p>';
echo '<p>' . $narrator . '</p>';
echo '<p>' . $product_image . '</p>';
echo '<p>' . $imprints . '</p>';
echo '<p>' . $duration . '</p>';
echo '<p>' . $id . '</p>';
echo '<p>' . $price . '</p>';
echo '<p>' . $audio_sample . '</p>';
echo '<p>' . $download_url . '</p>';
echo '<p>' . $description . '</p>';
