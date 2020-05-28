import $ from 'jquery';

$('*[data-patience]').on('click', function() {
  const thisIcon = $(this).find('i');

  // Bootstrap padding / margin like `mx-2` or `pt-3`
  const addedPadding = thisIcon.attr('class').match(/[mp][tblrxy]-[0-5]/i);
  const newClass =
    'fas fa-w fa-cog fa-spin ' + (addedPadding ? addedPadding : '');

  $(this).attr('data-original-class', thisIcon.attr('class'));
  $(this).attr('disabled', true);
  thisIcon.attr('class', newClass);
});

window.reEnablePatientButtons = function() {
  $('*[data-patience]').each(function() {
    const thisIcon = $(this).find('i');

    $(this).attr('disabled', false);
    thisIcon.attr('class', $(this).attr('data-original-class'));
  });
};
