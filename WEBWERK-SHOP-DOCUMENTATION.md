# Webwerk Shop Plugin - Comprehensive Documentation

## Table of Contents
1. [Overview](#overview)
2. [Architecture](#architecture) 
3. [Custom Post Types](#custom-post-types)
4. [Template System](#template-system)
5. [Cart Functionality](#cart-functionality)
6. [Order Process](#order-process)
7. [AJAX & REST API](#ajax--rest-api)
8. [Frontend Logic Flow](#frontend-logic-flow)
9. [Backend Logic Flow](#backend-logic-flow)
10. [User Journey](#user-journey)
11. [File Structure](#file-structure)

## Overview

The Webwerk Shop is a custom WordPress e-commerce plugin designed specifically for digital products like audiobooks and magazines. It features:

- Custom shopping cart system tied to WordPress users
- Digital product delivery with downloads
- Magazine subscription management
- AJAX-powered cart interactions
- ACF (Advanced Custom Fields) integration for data management

## Architecture

### Core Components
- **Main Plugin File**: `plugin.php` - Entry point, hooks, and core functionality
- **Post Type Registration**: `includes/shop-post-type.php` - Defines custom post types
- **Cart Actions**: `includes/cart-actions.php` - AJAX handlers and cart logic
- **Templates**: `templates/` - Custom page templates
- **Assets**: `js/` and `css/` - Frontend JavaScript and styles

### Key Technologies
- WordPress custom post types and taxonomies
- Advanced Custom Fields (ACF) for data management
- ACFE (ACF Extended) for frontend forms
- REST API for AJAX communication
- Gulp build system for asset compilation

## Custom Post Types

### 1. Product (`product`)
**Purpose**: Main products (books, audiobooks, digital content)

**URL Structure**: `/shop/{product-slug}/`

**ACF Fields**:
- `title` - Product title
- `author` - Author name  
- `narrator` - Speaker/narrator (for audiobooks)
- `imprints` - Publisher/location
- `year_edition` - Publication year
- `size_duration` - Content size/duration
- `id` - Unique order number (required)
- `price` - Product price (required)
- `sample` - Audio sample file
- `description` - Product description
- `download_file` - Digital download file
- `subscription` - Whether it's a subscription product

**Templates**:
- Single: `templates/single-product.php`
- Archive: `templates/archive-product.php`

### 2. Customer Cart (`customer_cart`)
**Purpose**: Stores shopping cart data for each user

**URL Structure**: `/warenkorb/{username}/`

**Status**: Private posts (one per user)

**ACF Fields**:
- `cart_item` (Repeater field `field_616558167898c`):
  - `cart_author` - Product author
  - `cart_titel` - Product title  
  - `cart_publicationform` - Publication format
  - `cart_order-number` - Product order number
  - `cart_price` - Item price
  - `cart_amount` - Quantity
  - `cart_item-delete` - Delete checkbox
  - `item_post` - Product post ID
- `cart-total-price` - Total cart price

**Templates**:
- Single: `templates/single-warenkorb.php`

### 3. Customer Order (`customer_order`)
**Purpose**: Completed orders/receipts for download access

**URL Structure**: `/bestellung/{order-id}/`

**Status**: Private/draft posts

**ACF Fields**: Same structure as cart_item repeater

**Templates**:
- Single: `templates/single-bestellung.php`

### 4. Magazine CPT (`magazine_cpt`)
**Purpose**: Magazine products with subscription support

**ACF Fields**: Same as products, plus:
- `subscription-repeater` - Multiple magazine issues
  - `magazine-issue` - Issue name/number
  - `subscription-download` - Download file for issue

**Templates**:
- Single: `templates/single-magazine_cpt.php`

### 5. Publication Form Taxonomy (`publicationform`)
**Purpose**: Categorizes products by format (CD, Download, Print, etc.)

**Applied to**: Products and Magazine CPT

## Template System

### Template Hierarchy

The plugin uses WordPress's template system with custom overrides:

```
plugin.php:312 - ww_shop_templates() filter
```

**Template Mapping**:
1. **Cart/Checkout Page** (`"Warenkorb/Checkout"`)
   - Template: `templates/cart-checkout-page.php`
   - Triggered: `is_page('Warenkorb/Checkout')`

2. **Orders Page** (`"Meine Bestellungen"`)
   - Template: `templates/orders-page.php` 
   - Triggered: `is_page('Meine Bestellungen')`

3. **Individual Cart** (`customer_cart` post)
   - Template: `templates/single-warenkorb.php`
   - Triggered: `is_singular('customer_cart')`

4. **Individual Order** (`customer_order` post)
   - Template: `templates/single-bestellung.php`
   - Triggered: `is_singular('customer_order')`

5. **Product Archive/Search**
   - Template: `templates/archive-product.php`
   - Triggered: Search queries or product archives

### Template Loading Logic

**Function**: `ww_shop_templates()` in `plugin.php:320`

```php
// Priority order:
1. Check for specific page templates by page title
2. Check for single post type templates  
3. Fall back to theme templates
```

### Page Creation

**On Plugin Activation** (`plugin.php:350`):
- Creates "Kundenkonto" (Customer Account) page
- Creates "Warenkorb/Checkout" (Cart/Checkout) page  
- Creates "Meine Bestellungen" (My Orders) page

## Cart Functionality

### Cart Lifecycle

#### 1. Cart Creation (`plugin.php:216`)
**Triggered**: User login (`wp_login` hook)

**Process**:
1. Check if user is administrator (skip if true)
2. Look for existing cart posts for user
3. If multiple carts exist, delete extras
4. If no cart exists, create new `customer_cart` post
5. Update user meta `shopping_cart_exists` with cart ID

**Cart Post Structure**:
```php
$new_cart = array(
    'post_name'   => $user->user_nicename,  // Username as slug
    'post_title'  => $user->display_name,   // Display name as title
    'post_author' => $user->ID,             // User ID as author
    'post_status' => 'private',             // Private visibility
    'post_type'   => 'customer_cart'        // Custom post type
);
```

#### 2. Adding Items to Cart
**Trigger**: AJAX call via "In den Warenkorb" button

**Flow**:
1. User clicks "Add to Cart" button on product
2. JavaScript (`js/ajaxquery.js:5`) captures click
3. AJAX POST to `/bit-shop/v1/items/cart-action/`
4. Backend handler `cart_action()` in `cart-actions.php:79`

**Backend Process**:
1. Get current user's cart
2. Check if item already in cart (`update_item_count()`)
3. If existing: increment quantity
4. If new: add new row to cart_item repeater field
5. Update user meta counters (`cart_item_count`, `cart_items_price`)
6. Return success modal markup

#### 3. Cart Display
**Templates**:
- Cart page: `templates/single-warenkorb.php`
- Uses ACFE form: `acfe_form('cart-form')`

**Cart Form Features**:
- Edit item quantities
- Remove items (checkbox deletion)
- Calculate totals
- "Update Cart" functionality

#### 4. Cart Updates
**Via ACFE Form Submission**:
- Form name: `cart-form`
- Hook: `acfe/form/submit/post/form=cart-form`
- Handler: `update_cart_item_count()` in `plugin.php:466`

**Update Process**:
1. Count all items in cart repeater
2. Calculate total price  
3. Update user meta fields
4. Log changes

#### 5. Cart Deletion
**Triggers**:
- User logout (optional via modal)
- Manual cart clear
- Order completion

**Logout Flow**:
1. User clicks "Abmelden" (logout)
2. AJAX call to `/bit-shop/v1/logout/modal/`
3. Show modal asking about cart
4. If "Delete": AJAX to `/bit-shop/v1/logout/delete-cart/`
5. Clear cart and user meta

## Order Process

### Order Placement Flow

#### 1. Checkout Initiation
**Template**: `templates/cart-checkout-page.php`

**Prerequisites**:
- User must be logged in
- Must have items in cart
- Cart ID must match user's cart

**Validation** (`cart-checkout-page.php:48-70`):
```php
// Verify cart ownership
if (!($wp_posts[0]->post_name == $current_user->user_nicename)) {
    // Error: cart slug doesn't match username
}

// Verify cart ID
if (isset($submitted_id) && !($submitted_id == $current_cart_id)) {
    // Error: submitted cart ID doesn't match current
}
```

#### 2. Checkout Form Display
**Form System**: ACFE (ACF Extended)
**Form Configuration**:
- Form name: `cart-checkout`
- Post ID: Current cart ID
- Displays cart items (read-only)
- Includes delivery address fields

**JavaScript Modifications**:
```javascript
// Disable quantity fields in checkout
disableFields("cart_amount");
// Remove delete checkboxes
deleteFields = jQuery('.acf-field[data-name="cart_item-delete"]');
```

#### 3. Order Completion
**Form Submission Hook**: `acfe/form/submit/form=cart-checkout`
**Handler**: `checkout_actions()` in `plugin.php:496`

**Process**:
1. Log order submission
2. Get cart ID from form
3. Call `do_action('clear_cart', $cart_id)`
4. Clear cart contents via `clear_cart_callback()`

**Cart Clearing** (`plugin.php:55`):
1. Delete all rows from cart repeater field
2. Reset user meta counters to 0
3. Log successful clearing

### Order Storage & Access

#### Order Records
- Orders are stored as `customer_order` posts
- Contain same item structure as carts
- Status: `private` or `draft`
- Author: customer user ID

#### Download Access
**Location**: Checkout page shows past orders
**Query** (`cart-checkout-page.php:128`):
```php
$args = array(
    'post_type'   => 'customer_order', 
    'post_status' => array('private', 'draft'),
    'author'      => $current_user->ID,
);
```

**Download Links**:
- Digital products: Direct file downloads
- Subscriptions: Show recent 3 issues
- File access via ACF file fields

## AJAX & REST API

### REST API Endpoints

All endpoints use namespace `bit-shop/v1/`

#### 1. Add to Cart
- **Endpoint**: `POST /bit-shop/v1/items/cart-action/`
- **Handler**: `cart_action()` in `cart-actions.php:79`
- **Permission**: `__return_true` (logged-in users)

**Request Data**:
```javascript
{
    action: 'cart_action',
    contentType: 'application/json', 
    item: JSON.stringify({
        itemId: 'product-id',
        itemPrice: 'price',
        itemTitle: 'title',
        itemAuthor: 'author', 
        itemPubforms: 'formats',
        itemUrl: 'product-url',
        itemPost: 'post-id'
    })
}
```

**Response**:
```json
{
    "markup": "<div>Success modal HTML</div>",
    "cart_items": {
        "cart_item_count": 3,
        "cart_items_price": 29.97
    }
}
```

#### 2. Update Cart Items
- **Endpoint**: `GET /bit-shop/v1/items/update/`  
- **Handler**: `update_cart_items()` in `cart-actions.php:213`

**Response**:
```json
{
    "cart_item_count": 3,
    "cart_items_price": 29.97
}
```

#### 3. Logout Modal
- **Endpoint**: `POST /bit-shop/v1/logout/modal/`
- **Handler**: `cart_modal()` in `plugin.php:81`

**Response**:
```json
{
    "markup_full": "<div>Cart exists modal</div>",
    "markup_empty": "<div>Empty cart modal</div>", 
    "cart_item_count": 2
}
```

#### 4. Delete Cart on Logout
- **Endpoint**: `POST /bit-shop/v1/logout/delete-cart/`
- **Handler**: `delete_cart()` in `plugin.php:155`

**Process**: Deletes all user carts and resets user meta

### AJAX Implementation

#### Frontend JavaScript Files
1. **ajaxquery.js** - Main cart AJAX functionality
2. **update-cart-items.js** - Cart updates  
3. **cart.js** - Cart page interactions

#### Script Enqueuing
**Location**: `cart-actions.php:11`
**Localization**: 
```php
wp_localize_script('shop-ajax-script', 'wp_ajax_cart_obj', array(
    'restURL' => rest_url(),
    'restNonce' => wp_create_nonce('wp_rest'),
));
```

#### Add to Cart Flow
```javascript
// 1. Button click captured
jQuery('button.btn-cart.add-item').click(function(){
    cartItem = jQuery(this).data(); // Get product data
    
    // 2. AJAX request
    $.ajax({
        type: 'POST',
        url: wp_ajax_cart_obj.restURL + 'bit-shop/v1/items/cart-action',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', wp_ajax_cart_obj.restNonce);
        },
        data: { item: JSON.stringify(cartItem) },
        success: function(data) {
            // 3. Show success modal
            $("button.add-item[data-item-id='" + cartItemID + "']").parent().append(data['markup']);
            
            // 4. Update cart counters
            jQuery('#items-count').text(data['cart_items']['cart_item_count']);
            jQuery('#total-price').text(data['cart_items']['cart_items_price'] + ' €');
        }
    });
});
```

## Frontend Logic Flow

### Product Browsing

#### 1. Shop Archive Page
**Template**: `templates/archive-product.php`
**URL**: `/shop/` or search results

**Features**:
- Search functionality (title, category, publication form)
- Product grid display with "Add to Cart" buttons
- Pagination
- Login status display
- Cart counter

#### 2. Single Product Page  
**Template**: `templates/single-product.php`
**URL**: `/shop/{product-slug}/`

**Elements**:
- Product details (title, author, description, price)
- Audio sample player (if available)
- Publication format display
- Add to Cart button with product data attributes
- Login prompt for non-authenticated users

#### 3. Add to Cart Interaction
**Trigger**: Click "In den Warenkorb" button
**Process**:
1. Extract product data from button attributes
2. AJAX call to add item
3. Display success modal
4. Update cart counter in header
5. Provide options: "Continue Shopping" or "Go to Cart"

### Cart Management

#### 1. Cart View
**Template**: `templates/single-warenkorb.php`
**URL**: `/warenkorb/{username}/`

**Elements**:
- Cart items list (ACFE form)
- Quantity adjustment
- Item removal checkboxes  
- Update cart button
- Proceed to checkout button

#### 2. Cart Updates
**Form**: ACFE `cart-form`
**Process**:
1. User modifies quantities or checks delete boxes
2. Clicks "Warenkorb aktualisieren" (Update Cart)
3. Form submission triggers backend update
4. Cart totals recalculated
5. Page refreshes with updated cart

### Checkout Process

#### 1. Checkout Page
**Template**: `templates/cart-checkout-page.php`
**URL**: `/warenkorb-checkout/?cart={cart-id}`

**Security Checks**:
- User must be logged in
- Cart ID must match user's cart
- Cart must contain items

#### 2. Order Review
**Form**: ACFE `cart-checkout`
**Elements**:
- Read-only cart items display
- Delivery address form (if configured)
- Order total
- Submit order button

#### 3. Order Completion
**Process**:
1. Form validation
2. Order data processing  
3. Cart clearing
4. Redirect to confirmation/orders page

## Backend Logic Flow

### User Session Management

#### Login Process
**Hook**: `wp_login` in `plugin.php:207`
**Handler**: `ww_shop_prepare_cart()`

**Flow**:
1. User logs in to WordPress
2. Check if user is administrator (skip cart creation)
3. Query for existing customer_cart posts by user
4. If multiple carts exist, clean up extras
5. If no cart exists, create new one
6. Update user meta with cart ID

#### Logout Process  
**Hook**: `logout_redirect` in `plugin.php:201`
**Handler**: `log_logout()`

**Flow**:
1. User initiates logout
2. Optional cart deletion modal (AJAX)
3. If deleting cart: clear cart contents and user meta
4. Redirect to shop page
5. Log logout action

### Data Management

#### ACF Field Structure
**Key Field**: `field_616558167898c` (cart_item repeater)

**Subfields**:
- `cart_author` - Product author
- `cart_titel` - Product title
- `cart_publicationform` - Publication format  
- `cart_order-number` - Unique product ID
- `cart_price` - Item price
- `cart_amount` - Quantity
- `cart_item-delete` - Delete flag
- `item_post` - Reference to product post

#### User Meta Fields
- `shopping_cart_exists` - Cart post ID
- `cart_item_count` - Total items in cart
- `cart_items_price` - Total cart value

### Database Operations

#### Cart Item Addition
**Function**: `cart_action()` in `cart-actions.php:79`

**Process**:
1. Get user's cart post
2. Check if item exists (`update_item_count()`)
3. If exists: increment quantity in repeater row
4. If new: add new repeater row with `add_row()`
5. Update user meta counters
6. Return JSON response

#### Cart Item Updates
**Hook**: `acfe/form/submit/post/form=cart-form`
**Handler**: `update_cart_item_count()` in `plugin.php:466`

**Process**:
1. Loop through cart_item repeater
2. Sum quantities and calculate total price
3. Update user meta fields
4. Log changes

#### Order Creation
Orders appear to be created through the ACFE form system, copying cart data to a new `customer_order` post.

## User Journey

### Complete User Flow

#### 1. First Visit (Not Logged In)
1. Browse shop archive at `/shop/`
2. View product details
3. See "Anmelden" (Login) button instead of "Add to Cart"
4. Click login, authenticate via WordPress

#### 2. Post-Login (Cart Creation)
1. Login triggers `ww_shop_prepare_cart()`
2. System creates private `customer_cart` post
3. User meta updated with cart reference
4. Redirected back to shop

#### 3. Shopping
1. Browse products, view details
2. Click "In den Warenkorb" on desired items
3. AJAX adds items to cart
4. Success modal appears with options
5. Cart counter updates in header

#### 4. Cart Review
1. Navigate to cart via `/warenkorb/{username}/`
2. Review items, adjust quantities
3. Remove unwanted items
4. Click "Warenkorb aktualisieren" to save changes
5. Click "Zur Kasse" to proceed

#### 5. Checkout  
1. Redirected to `/warenkorb-checkout/?cart={id}`
2. Review order details (read-only)
3. Fill delivery address if required
4. Submit order
5. Cart automatically cleared
6. Access downloads from orders page

#### 6. Post-Purchase
1. Visit "Meine Bestellungen" for download access
2. Download digital products
3. For subscriptions: access recent issues
4. Re-order or continue shopping

#### 7. Logout (Optional)
1. Click "Abmelden" 
2. Modal asks about cart preservation
3. Choose to keep or delete cart
4. Logout completes, redirected to shop

## File Structure

```
webwerk-shop/
├── plugin.php                           # Main plugin file, hooks, core functions
├── CLAUDE.md                            # Project instructions
├── gulpfile.js                          # Build system configuration
├── package.json                         # Node.js dependencies
│
├── includes/                            # PHP includes
│   ├── shop-post-type.php              # Custom post type definitions
│   ├── cart-actions.php                # AJAX handlers and cart logic
│   └── download.php                    # Download handling (if exists)
│
├── templates/                           # Custom page templates
│   ├── single-product.php              # Individual product pages
│   ├── archive-product.php             # Product listing/search results  
│   ├── single-warenkorb.php           # Individual cart view
│   ├── cart-checkout-page.php         # Checkout process page
│   ├── single-bestellung.php          # Individual order view
│   ├── orders-page.php                # Order history page
│   ├── customer-account-page.php      # Customer account page
│   └── single-magazine_cpt.php        # Magazine product pages
│
├── assets/                             # ACF configuration
│   ├── acf-definitions.php            # ACF field group definitions
│   ├── acf-export-2021-11-08.json    # ACF field export
│   └── acfe-export-form-*.json       # ACFE form configurations
│
├── js/                                 # JavaScript files
│   ├── ajaxquery.js                   # Main cart AJAX functionality
│   ├── ajaxquery.min.js               # Minified version
│   ├── cart.js                        # Cart page interactions
│   ├── cart.min.js                    # Minified version  
│   ├── update-cart-items.js           # Cart update functionality
│   ├── update-cart-items.min.js       # Minified version
│   ├── app.js                         # General application JS
│   └── app.min.js                     # Minified version
│
├── css/                               # Compiled CSS
│   └── shop.min.css                  # Minified stylesheet
│
└── scss/                              # Source SCSS files
    └── style.scss                     # Main stylesheet source
```

### Key File Purposes

**plugin.php**: Central coordination, hooks, user management, REST API registration
**shop-post-type.php**: Data structure definitions (post types, taxonomies)
**cart-actions.php**: AJAX request handling, cart manipulation logic  
**templates/**: User interface presentation layer
**js/**: Client-side interaction and AJAX communication
**assets/**: ACF field and form configurations for data management

This comprehensive documentation covers the complete architecture and functionality of the Webwerk Shop plugin, from high-level concepts down to specific implementation details.