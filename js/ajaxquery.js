
jQuery(document).ready(
  function($) {

    jQuery('button.btn-cart.add-item').click(function(){
      cartItem = jQuery(this).data();
      cartItemID = (cartItem ['itemId']);

    // This does the ajax request
    $.ajax({
        type: 'POST',
        url: wp_ajax_cart_obj.restURL + 'bit-shop/v1/items/cart-action',
        beforeSend: function (xhr) {
          xhr.setRequestHeader( 'X-WP-Nonce', wp_ajax_cart_obj.restNonce);
        },
        data: {
            'action': 'cart_action',
            'contentType': 'application/json',
            'item' : JSON.stringify(cartItem),
            'dataType': 'json',
            // 'nonce' : wp_ajax_cart_obj.nonce
        },
        success:function(data) {
          // console.log(data);
            // This outputs the result of the ajax request
            $("button.add-item[data-item-id='" + cartItemID + "']").parent().append(data['markup']);
            $(".c-btn__shop-on").focus();
            trapFocusinModal();
            // Hier wird der Zähler im Warenkorbknopf aktualisiert.
            if (data['cart_items']) {
              jQuery('#items-count').text(data['cart_items']['cart_item_count']);
              jQuery('#total-price').text(parseFloat(data['cart_items']['cart_items_price']).toFixed(2) + ' €');
            }
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
  });

  jQuery('#signout-button').click(function(event){

  // This does the ajax request
  $.ajax({
      type: 'POST',
      url: wp_ajax_cart_obj.restURL + 'bit-shop/v1/logout/modal',
      beforeSend: function (xhr) {
        xhr.setRequestHeader( 'X-WP-Nonce', wp_ajax_cart_obj.restNonce);
      },
      data: {
          'action': 'cart_logout',
          'contentType': 'application/json',
          // 'item' : JSON.stringify(cartItem),
          'dataType': 'json',
          // 'nonce' : wp_ajax_cart_obj.nonce
      },
      success:function(data) {
        // console.log(data);
        if (data['cart_item_count'] > 0) {
          // event.preventDefault();
          // This outputs the result of the ajax request
          $("#signout-button").parent().append(data['markup_full']);
          trapFocusinModal();
          $(".c-btn__delete-cart").focus();

        } else {
          $("#signout-button").parent().append(data['markup_empty']);
          trapFocusinModal();
          $(".c-btn__logout").focus();
        }

          // Hier wird der Zähler im Warenkorbknopf aktualisiert.
          // jQuery('#items-count').text(data['cart_item_count']);
      },
      error: function(errorThrown){
          console.log(errorThrown);
      }
  });
});
});
