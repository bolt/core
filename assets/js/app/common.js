import $ from 'jquery';
import { DateTime } from 'luxon';

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
      $('.admin__sidebar')
        .addClass('admin__sidebar--is-expanded')
        .removeClass('admin__sidebar--is-collapsed');
      $(this).toggleClass('is-active');
    } else {
      $('.admin__sidebar')
        .addClass('admin__sidebar--is-collapsed')
        .removeClass('admin__sidebar--is-expanded');
      $(this).toggleClass('is-active');
    }
  });

  /*
   ** Hash on tabs functionality
   ** When there is a Bootstrap data-toggle="pill" element, append the hash to the link
   */
  let url = location.href.replace(/\/$/, '');
  if (location.hash) {
    const hash = url.split('#');
    $('a[href="#' + hash[1] + '"]').tab('show');
    url = location.href.replace(/\/#/, '#');
    history.replaceState(null, null, url);
    setTimeout(() => {
      $(window).scrollTop(0);
    }, 50);
  }

  $('a[data-toggle="pill"]').on('click', function() {
    let newUrl;
    const hash = $(this).attr('href');
    newUrl = url.split('#')[0] + hash;
    history.replaceState(null, null, newUrl);
  });

  /*
   ** Convert all ISO dates with class .datetime-relative to relative time
   */
  $('.datetime-relative').each(function() {
    $(this).text(DateTime.fromISO($(this).text()).toRelative());
  });
});
