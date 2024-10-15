<?php
// Create the Parent Menu "Shop"
function create_shop_parent_menu() {
    add_menu_page(
        __( 'Shop', 'webwerk-shop' ), // Page title
        __( 'Shop', 'webwerk-shop' ), // Menu title
        'manage_options',             // Capability
        'shop',                       // Menu slug
        '',                           // Callback function (if no content, leave it blank)
        'dashicons-store',            // Icon (Dashicon for a store)
        82                             // Menu position
    );
}
add_action( 'admin_menu', 'create_shop_parent_menu' );
?>
