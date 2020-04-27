import bootbox from 'bootbox';
import $ from 'jquery';

$('*[data-confirmation]').on('click', function () {
  let thisHref = $(this).attr('href');
  let confirmation = $(this).data('confirmation');
  bootbox.confirm(confirmation, function (result) {
    if (result) {
      window.location = thisHref;
    }
  });

  return false;
});
