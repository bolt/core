import $ from 'jquery';

$(document).ready(function() {
  $('.toast').toast('show');
  // add a js class to indicate we have JS enabled. Needs a change to either modernizr or something custom @bobdenotter
  $('html').addClass('js');

  $('.admin__sidebar').addClass('admin__sidebar--is-collapsed');

  $('.admin-sidebar-toggler').click(function() {
    if ($('.admin__sidebar').hasClass('admin__sidebar--is-collapsed')) {
      $('.admin__sidebar').addClass('admin__sidebar--is-expanded').removeClass('admin__sidebar--is-collapsed');
      $(this).toggleClass('is-active');
    } else {
      $('.admin__sidebar').addClass('admin__sidebar--is-collapsed').removeClass('admin__sidebar--is-expanded');
      $(this).toggleClass('is-active');
    }
  });

});
