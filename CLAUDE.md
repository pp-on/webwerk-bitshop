# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress plugin called "Webwerk Shop" that implements a custom e-commerce solution with shopping cart functionality. The plugin creates custom post types for products, customer carts, orders, and magazines, with custom checkout and user account management.

## Development Commands

### Build System (Gulp)
- `npm start` or `gulp` - Runs the default build task (SCSS compilation, CSS/JS minification, and file watching)
- `npm install` - Install dependencies (Foundation Sites, jQuery, Gulp build tools)

The build system:
- Compiles SCSS to CSS with autoprefixer
- Minifies JavaScript files 
- Creates source maps
- Watches for file changes during development
- Combines CSS files into `css/shop.min.css`

### File Structure for Development
- `scss/style.scss` - Main SCSS entry point
- `js/*.js` - JavaScript files (auto-minified to `*.min.js`)
- `css/` - Generated CSS output
- `gulpfile.js` - Build configuration

## Architecture Overview

### WordPress Plugin Structure
- **Main Plugin File**: `plugin.php` - Plugin registration, hooks, and core functionality
- **Post Types**: `shop-post-type.php` - Defines custom post types (product, customer_cart, customer_order, magazine_cpt)
- **Cart System**: `cart-actions.php` - AJAX handlers for cart operations
- **Templates**: Various PHP templates for single pages and archives

### Custom Post Types
1. **product** - Products with ACF fields (title, author, price, publication forms)
2. **customer_cart** - User shopping carts (private posts, one per user)
3. **customer_order** - Customer orders/receipts
4. **magazine_cpt** - Magazine subscriptions
5. **publicationform** - Custom taxonomy for product formats

### Key Features
- **User Cart Management**: Automatic cart creation on login, cart persistence, logout cart deletion modal
- **AJAX Shopping Cart**: Add/remove items without page refresh via REST API endpoints
- **ACF Integration**: Heavy use of Advanced Custom Fields for product data and cart items
- **Custom Templates**: Plugin provides its own templates for shop pages

### REST API Endpoints
- `/bit-shop/v1/items/cart-action/` - Add items to cart
- `/bit-shop/v1/items/update/` - Update cart item counts
- `/bit-shop/v1/logout/modal/` - Cart logout confirmation modal
- `/bit-shop/v1/logout/delete-cart/` - Delete cart on logout

### Page Templates
- `cart-checkout-page.php` - Checkout page ("Warenkorb/Checkout")
- `orders-page.php` - Order history ("Meine Bestellungen") 
- `customer-account-page.php` - Customer account page
- `single-product.php` - Individual product pages
- `archive-product.php` - Product catalog/search results

### JavaScript/AJAX
- `js/ajaxquery.js` - Cart AJAX functionality
- `js/cart.js` - Cart page interactions
- `js/update-cart-items.js` - Cart item updates
- Uses wp_localize_script for REST API URLs and nonces

## Development Notes

- Plugin creates shop pages automatically on activation
- Uses ACF field keys (e.g., `field_616558167898c`) for cart items repeater field
- German language interface ("Warenkorb", "Bestellung", etc.)
- Integrates with WordPress user system (cart tied to user ID)
- Custom rewrite rules for shop URLs (products use `/shop/` slug)
- Cart data stored as ACF repeater fields in customer_cart posts
- User meta tracks `cart_item_count` and `cart_items_price`