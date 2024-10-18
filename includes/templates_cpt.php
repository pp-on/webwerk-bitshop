<?php
/**
 * Filtert das Single-Template für benutzerdefinierte Beitragstypen (Custom Post Types - CPT).
 *
 * Diese Funktion prüft, ob es sich um die Einzelansicht eines Beitrags vom Typ 'product' oder 'magazine_cpt' handelt.
 * Wenn ja, wird ein benutzerdefiniertes Template verwendet, sofern es existiert.
 *
 * @param string $single Das aktuelle Single-Template.
 * @return string Das angepasste oder das ursprüngliche Single-Template.
 */
function ww_shop_single_template( $single ) {
    // Überprüfen, ob eine Einzelansicht (single) für den Beitragstyp 'product' oder 'magazine_cpt' angezeigt wird
    if ( is_single() && ( get_post_type() === 'product' || get_post_type() === 'magazine_cpt' ) ) {
        // Pfad zum benutzerdefinierten Single-Template
        $template_path =  plugin_dir_path( __FILE__ )  . 'templates/single-cpt.php';

        // Überprüfen, ob das benutzerdefinierte Template existiert
        if ( file_exists( $template_path ) ) {
            return $template_path; // Benutzerdefiniertes Template verwenden
        }
    }

    return $single; // Standard-Template zurückgeben, falls kein benutzerdefiniertes Template vorhanden ist
}
// Filter hinzufügen, um die Single-Template-Logik anzupassen
add_filter( 'single_template', 'ww_shop_single_template' );

/**
 * Filtert das Archiv-Template für benutzerdefinierte Beitragstypen.
 *
 * Diese Funktion prüft, ob es sich um das Archiv des Beitragstyps 'product' handelt.
 * Wenn ja, wird ein benutzerdefiniertes Archiv-Template verwendet, sofern es existiert.
 *
 * @param string $archive_template Der Pfad zum aktuellen Archiv-Template.
 * @return string Das angepasste oder das ursprüngliche Archiv-Template.
 */
function ww_shop_archive_template( $archive_template ) {
    // Überprüfen, ob es sich um ein Archiv handelt und ob der Beitragstyp 'product' ist
    if ( is_archive() && get_post_type() === 'product' ) {
        // Pfad zum benutzerdefinierten Archiv-Template
        $custom_archive_template = plugin_dir_path(__FILE__) . 'templates/archive-product.php';

        // Überprüfen, ob das benutzerdefinierte Archiv-Template existiert
        if ( file_exists( $custom_archive_template ) ) {
            return $custom_archive_template; // Benutzerdefiniertes Archiv-Template verwenden
        }
    }

    return $archive_template; // Standard-Template zurückgeben, falls kein benutzerdefiniertes Archiv-Template vorhanden ist
}
// Filter hinzufügen, um die Archiv-Template-Logik anzupassen
add_filter( 'archive_template', 'ww_shop_archive_template' );

