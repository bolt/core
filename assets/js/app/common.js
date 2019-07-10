import $ from 'jquery';

$(document).ready(function() { 

  // add a js class to indicate we have JS enabled. Might need a change to either modernizr or somethng comparable
  $('html').addClass('js');

  /*
  ** Sidebar collapse functionality
  ** Collapsible Sidebar on load
  ** The 'admin__sidebar--is-collapsed' class has effect up to screen sizes where the sidebar menu cannot be totally 
  ** offscreen anymore
  */
  $('.admin__sidebar').addClass('admin__sidebar--is-collapsed');

  $('.admin-sidebar-toggler').on('click', function() {
    if ($('.admin__sidebar').hasClass('admin__sidebar--is-collapsed')) {
      $('.admin__sidebar').addClass('admin__sidebar--is-expanded').removeClass('admin__sidebar--is-collapsed');
      $(this).toggleClass('is-active');
    } else {
      $('.admin__sidebar').addClass('admin__sidebar--is-collapsed').removeClass('admin__sidebar--is-expanded');
      $(this).toggleClass('is-active');
    }
  });

});