jQuery(document).ready(function(){
  // Für Warenkorbseite - vielleicht getrennt einhängen?

  // Wiederholer Label entfernen.
  jQuery("label[for='acf-field_616558167898c']").remove();

function appendSum(){
  // Reihe für Summe dranhängen.
  jQuery('tbody').append('<tr><td colspan="5"></td>' +
  '<td class="acf-field acf-field-number acf-field-617bb334332ea" data-name="cart-total-price" data-type="number" data-key="field_617bb334332ea">' +
  '<div class="acf-label">' +
  '<label for="acf-field_617bb334332ea">Gesamtpreis in €</label></div>' +
  '<div class="acf-input">' +
  '<div class="acf-input-wrap"><input type="number" id="total-price-table" name="acf[field_617bb334332ea]" min="0" max="5000" readonly="readonly"></div></div>' +
  '</div></td><td colspan="3"></td></tr>'
);
}

var rowToDeleteCheckbox = jQuery('.acf-field[data-name="cart_item-delete"]');

function changeAttributes(){
// Die Reihe der Tabelle.

const dataName = ['cart_author','cart_titel', 'cart_publicationform', 'cart_order-number', 'cart_price'];
dataName.forEach(disableFields);
function disableFields(dataName){
  var disabledFields = jQuery('.acf-field[data-name="'+ dataName + '"]').find('input');
  disabledFields.attr('readonly','readonly');
  // remove attribute autocomplete from checkbox input.
  rowToDeleteCheckbox.find('input').removeAttr('autocomplete');
}
}

function changeRoleAttr(){
// Barrierefreiheit für Tabellen.
const editableFields = ['cart_amount','cart_item-delete'];
editableFields.forEach(roleStatus);
function roleStatus(editableFields){
  var roleStatusFields = jQuery('.acf-field[data-name="'+ editableFields + '"]').find('input');
  roleStatusFields.attr('role', 'status');
};
}
// Spalte mit Post-ID verschwinden lassen.
function hideColumn(hideColumn){
    let inputFields = jQuery('.acf-field[data-name="item_post"]').find('input');
    inputFields.attr('type', 'hidden');
    jQuery('.acf-th[data-name="item_post"]').find('label').remove();
  };
  appendSum();
  changeAttributes();
  changeRoleAttr();
  hideColumn();
  makeTableResponsive();

// Disable Fields in Checkout.
// disableFields('cart_amount');
// deleteFields = jQuery('.acf-field[data-name="cart_item-delete"]');
// console.log(deleteFields);
// deleteFields.each( function(){
//   $(this).remove();
// });
// acfDeleteUi = jQuery('.acf-js-tooltip');
// acfDeleteUi.each( function(){
//   $(this).remove();
// });

// Reihen Löschen. -- hier mit Promises arbeiten.
// var checkboxDeleteRow = .find('input');

rowToDeleteCheckbox.on("click", "input" , function(){
  currentCheckBox = $(this);
      shureToDeleteModal = '<div class="add-to-cart-modal--webwerk" id="cart-modal--webwerk" role="dialog" aria-modal="true" aria-labelledby="item-delete-modal-head" aria-hidden="false">' +
      '<div class="cart-modal__settings">' +
      '<section>' +
      '<h2 id="item-delete-modal-head">Wollen Sie den Artikel wirklich aus dem Warenkorb löschen?</h2>' +
      '</section>' +
      '<div class="c-btns">' +
      '<button id="c-btn__delete" class="btn btn-standard c-btn">Ja</button>' +
      '<button id ="c-btn__cancel" class="btn btn-standard c-btn">Nein (Abbrechen)</button>' +
      '</div>' +
      '</div>' +
      '</div>';

  const rowToDelete = $(this).parentsUntil("tbody").last();
  console.log(rowToDelete);
  rowToDelete.append(shureToDeleteModal).append();
  rowToDeleteCheckbox.promise().done(function(){
    trapFocusinModal();
    // console.log(jQuery('#c-btn__delete'));
    deleteButton = jQuery('#c-btn__delete');
    deleteButton.focus();
  });
  // Funktion zum Entfernen des Modals.
  function removeModal(){
    modalOuter = jQuery('#cart-modal--webwerk');
    modalOuter.remove();
  }
  // Cancel Item Delete
  rowToDelete.on('click', '#c-btn__cancel', function(e){
    removeModal();
    rowToDelete.find('input').removeAttr('checked');

  });

  // Item Delete
  rowToDelete.on('click', '#c-btn__delete', function(e){
    	// removeModal();
      // console.log(rowToDelete);
      rowToDelete.remove();
      rowToDelete.promise().done(function() {
      calculateTotal();
    });
  });
});


// Ende: Document ready.


// Auf Mengenänderung schauen.
var itemCount = jQuery('.acf-field[data-name="cart_amount"]').find('input');
itemCount.on('input', function()
{
  calculateTotal();
    // console.log('input changed to: ', itemCount.val());
});

// Summe berechnen
function calculateTotal(){
  var totalPrice = 0;
const priceCells = jQuery('.acf-field[data-name="cart_price"]').find('input');
const amountCells = jQuery('.acf-field[data-name="cart_amount"]').find('input');
for (let i = 0; i < priceCells.length; i++) {
  if (priceCells[i].value) {
    var singlePrice = priceCells[i].value.replace(/[^0-9.]/g, '');
    var cartAmount = parseInt(amountCells[i].value);
    var multiplePrice = parseFloat(singlePrice) * cartAmount;
       totalPrice = totalPrice + multiplePrice;
       // console.log( typeof(priceCells[i].value) + ' ' + priceCells[i].value );
  }
}
totalPriceTable = document.getElementById("total-price-table");
totalPriceCell = document.getElementById("acf-field_617bb334332ea");
totalPriceCell.innerHTML = totalPrice;
totalPriceCell.value = totalPrice;
totalPriceTable.innerHTML = totalPrice;
totalPriceTable.value = totalPrice;
}
calculateTotal();




});
// End: Document ready.

function makeTableResponsive(){
  // Tabelle Mobil (responsive)
  // var n;
  // $('tbody tr').children().each(function(n) {
  //   n = 0;
  //   var tableTextCon = $(this).text();
  //   //		console.log(tableTextCon);
  //   $('tbody td:nth-of-type(' + (n + 1) + ')').html('<p class="res-tbody">' + tableTextCon + '</p>');
  // });
  let index2 = 1;
  $('tr:first-of-type').children().each(function(index2) {
    var tableText = $(this).text();
    $('tbody td:nth-of-type(' + (index2 + 1) + ')').prepend('<p class="res-thead">' + tableText + '</p>');
  });
}
