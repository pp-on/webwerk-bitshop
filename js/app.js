// const homeURL = document.location.href;
const homeURL = wwThemeParams.homeURL;
jQuery(document).ready(function(){
// Logout-Popup Button Funktionnen.



// Button-Wechsel. Wenn eingeloggt, dann ....
if (document.body.classList.contains( 'logged-in')) {
  // Wenn angemeldet::
  // Signout disablen
    jQuery('#signout-button').removeAttr('disabled');
    jQuery('#signout-button').attr('aria-disabled', 'false');
  // Login Status: Angemeldet als ...
    // jQuery('#login-status').text('Sie sind angemeldet als ' ); // hier per Ajax noch display name user übergeben.
  // Signin-Button wird zum Kundenkonto
      // jQuery('#signin-button').text('Kundenkonto');
      // jQuery('#signin-button').attr("href", homeURL + "/Kundenkonto");
      // jQuery('.btn-cart').show();
      jQuery('.product-btn > #signin-button').hide();
    // Anzahl der Waren im Warenkorb anzeigen.
    // jQuery('#items-count').text();
}else{
  // Wenn nicht angemeldet::

  // alle "In den Warenkorb Knöpfe" ausblenden.
  jQuery('.btn-cart').hide();
  jQuery('#signout-button').prop('disabled', true);
  jQuery('#signout-button').attr('aria-disabled', 'true');
  // jQuery('#login-status').text('Sie sind nicht angemeldet.');
  jQuery('#signin-button').text('Anmelden');
}
// Ende: Button-Wechsel



// Close Cart modal
jQuery(".product-footer, .product-btn").on("click", ".c-btn__shop-on" ,function(){
jQuery('#cart-modal--webwerk').remove();
jQuery('#cart-modal--webwerk').remove();
});
jQuery(".logout-btn, .box-login, .btn-container").on("click", ".c-btn__stay", function(){
  jQuery('#cart-modal--webwerk').remove();
  jQuery('#cart-modal--webwerk').remove();
});

});

// Zu Tabellenfeldern disabled hinzufügen.

/****************************************************
*                                                   *
* Fokusfalle                                        *
*                                                   *
/****************************************************/


function trapFocusinModal(){
// Helperfunctions.
// select a list of matching elements, context is optional
function $0(selector, context) {
  return (context || document).querySelectorAll(selector);
}

// select the first match only, context is optional
function $1(selector, context) {
  return (context || document).querySelector(selector);
}
// add all the elements inside modal which you want to make focusable
// https://uxdesign.cc/how-to-trap-focus-inside-modal-to-make-it-ada-compliant-6a50f9a70700 .
const modal = document.querySelector('#cart-modal--webwerk'); // select the modal by it's id.


const  focusableElements =
    'button, input, a'; //  [tabindex]:not([tabindex="-1"])

const focusableContent = modal.querySelectorAll(focusableElements);

const firstFocusableElement = focusableContent[0]; // get first element to be focused inside modal

const lastFocusableElement = focusableContent[focusableContent.length - 1]; // get last element to be focused inside modal

document.addEventListener('keydown', function(e) {
  let isTabPressed = e.key === 'Tab' || e.keyCode === 9;

  if (!isTabPressed) {
    return;
  }
  if (e.shiftKey) { // if shift key pressed for shift + tab combination
    if (document.activeElement === firstFocusableElement) {
      lastFocusableElement.focus(); // add focus for the last focusable element
      e.preventDefault();
    }
  } else { // if tab key is pressed
    if (document.activeElement === lastFocusableElement) { // if focused has reached to last focusable element then focus first focusable element after pressing tab
      firstFocusableElement.focus(); // add focus for the first focusable element
      e.preventDefault();
    }
  }
});

firstFocusableElement.focus();
}
