jQuery(document).ready(
function($) {
  $.ajax({
      type: 'GET',
      url: wp_ajax_cart_obj.restURL + 'bit-shop/v1/items/update',
      beforeSend: function (xhr) {
        xhr.setRequestHeader( 'X-WP-Nonce', wp_ajax_update_cart_obj.restNonce);
      },
      data: {
          'action': 'update_cart_items',
          'contentType': 'application/json',
          'dataType': 'json',
          // 'nonce' : wp_ajax_cart_obj.nonce
      },
      success:function(data) { // data ist der Rückgabewert von update_cart_items().
          // Hier wird der Zähler im Warenkorbknopf aktualisiert.
          if (data) {
            // console.log(data);
            if (data['cart_item_count']) {
              jQuery('#items-count').text(data['cart_item_count']);
            }
            if (data['cart_items_price']) {
              jQuery('#total-price').text(parseFloat(data['cart_items_price']).toFixed(2) + ' €');
            }
          } else {
            jQuery('#cart').text('Zum Warenkorb');

          }

      },
      error: function(errorThrown){
          console.log(errorThrown);
      }
  });



  // function($) {
  //   'use strict';     //wrapper
  //       var data = {
  //         itemCount : 4,
  //         // jQuery('#items-count'),
  //       }
  //       // console.log('bis da');
  //       $.getJSON(wp_ajax_cart_obj.ajax_url,
  //                 data,
  //                 function(json) {
  //                   if (json.success) {
  //                      alert( 'yes!' );
  //                     console.log('data');
  //                         // itemCount.text(data);
  //                   } else {
  //                     alert(json.data.message);
  //                   }
  //                              //callback
  //              //insert server response
  //       });
});
