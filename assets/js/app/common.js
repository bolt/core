import $ from 'jquery';
import { DateTime } from 'luxon';

import { version } from '../version';
window.assetsVersion = version;

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

    /*
     ** Initialise all popover elements
     */
    $('[data-toggle="popover"]').popover();

    /*
     ** When a field from another group is invalid, show it.
     */
    $('#editor button[type="submit"]').click(function() {
        $('input:invalid').each(function() {
            // Find the tab-pane that this element is inside, and get the id
            var $closest = $(this).closest('.tab-pane');
            var id = $closest.attr('id');

            // Find the link that corresponds to the pane and have it show
            $('.nav a[href="#' + id + '"]').tab('show');

            // Only want to do it once
            return false;
        });
    });

    /*
     ** Simulates disabled behavior for elements with data-readonly attribute.
     * This is needed, because a disabled input cannot be required.
     */
    $('[data-readonly]').on('keydown paste', function(e) {
        e.preventDefault();
    });
    /* Part of the code above, however make sure flatpickr is not readonly
     * and that its validation works.
     */
    $('.editor--date')
        .siblings()
        .prop('readonly', false)
        .attr('data-readonly', 'readonly');
    $('.editor--date').on('change', e => {
        const target = $(e.target)
            .parent()
            .find('input[data-readonly="readonly"]');
        if (target.val()) {
            target[0].setCustomValidity('');
        } else {
            target[0].setCustomValidity('Please fill out this field.');
        }
    });

    /*
     ** Display the custom error message, if set.
     */
    function handleInvalid(event) {
        const errormessage = $(event.target).attr('data-errormessage');
        event.target.setCustomValidity(errormessage);
    }

    function handleInput(event) {
        event.target.setCustomValidity('');
    }

    $('[data-errormessage]').on('invalid', handleInvalid);

    /* Remove custom validity every time input is changed. This is done because setCustomValidity does not reset */
    $('[data-errormessage]').on('input', handleInput);

    /* Set the errormessage on the correct editor--date field */
    $('.editor--date').each(function() {
        let siblings = $(this).siblings();
        const errormessage = $(this).attr('data-errormessage');

        siblings.each(function() {
            $(this)
                .attr('data-errormessage', errormessage)
                .on('invalid', handleInvalid)
                .on('input', handleInput);
        });
    });
    /* End of custom error message */

    /*
     ** Copy text to clipboard. Used in filemanager actions.
     */
    $('[data-copy-to-clipboard]').on('click', function(e) {
        const target = $(e.target);

        var input = document.createElement('input');
        input.setAttribute('id', 'copy');

        target.parent().append(input);
        input.value = target.attr('data-copy-to-clipboard');
        input.focus();
        input.select();
        document.execCommand('copy');
        target
            .parent()
            .find('#copy')
            .remove();
    });
    /* End of copy text to clipboard */
});
