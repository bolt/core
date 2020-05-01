import bootbox from 'bootbox';
import $ from 'jquery';

$('*[data-confirmation]').on('click', function() {
  const thisElem = $(this);
  const thisHref = $(this).attr('href');
  const confirmation = $(this).data('confirmation');

  if (!thisElem.data('confirmed')) {
    bootbox.confirm(confirmation, function(result) {
      if (result && thisHref) {
        window.location = thisHref;
      }

      if (result) {
        thisElem.attr('data-confirmed', true);
        thisElem.click();
      }
    });

    return false;
  }
});
