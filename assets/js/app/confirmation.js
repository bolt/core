import bootbox from 'bootbox';
import $ from 'jquery';

let bootboxDefaults = {
  locale: 'en'
}

bootbox.setDefaults(bootboxDefaults);

let locale = $('html').attr('lang');

if(locale){
  bootbox.setLocale(locale);
}

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
