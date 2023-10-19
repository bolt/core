import $ from 'jquery';
import { DateTime } from 'luxon';
import { Popover } from 'bootstrap';
import { Tab } from 'bootstrap';
import { resetModalContent } from './modal';
import ClipboardJS from 'clipboard';

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
                .removeClass('admin__sidebar--is-collapsed')
                .removeClass('d-none');
            $(this).toggleClass('is-active');
        } else {
            $('.admin__sidebar')
                .addClass('admin__sidebar--is-collapsed')
                .removeClass('admin__sidebar--is-expanded')
                .removeClass('d-none');
            $(this).toggleClass('is-active');
        }
    });

    /*
     ** Hash on tabs functionality
     ** When there is a Bootstrap data-toggle="tab" element, append the hash to the link
     */
    let url = location.href.replace(/\/$/, '');
    if (location.hash) {
        const hash = url.split('#');
        let triggerEl = document.querySelector('a[href="#' + hash[1] + '"]');
        Tab.getOrCreateInstance(triggerEl).show(); // Select tab by name
        url = location.href.replace(/\/#/, '#');
        history.replaceState(null, null, url);
        setTimeout(() => {
            $(window).scrollTop(0);
        }, 50);
    }

    $('a[data-bs-toggle="tab"]').on('click', function() {
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
    let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new Popover(popoverTriggerEl);
    });

    /*
     ** When a field from another group is invalid, show it.
     */
    $('#editor button[type="submit"]').click(function() {
        $('#editor input:invalid').each(function() {
            // Find the tab-pane that this element is inside, and get the id
            let $closest = $(this).closest('.tab-pane');
            let id = $closest.attr('id');
            if (id != null) {
                // Find the link that corresponds to the pane and have it show
                let triggerEl = document.querySelector('.nav a[href="#' + id + '"]');
                Tab.getOrCreateInstance(triggerEl).show(); // Select tab by name
            }

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
    new ClipboardJS('*[data-clipboard-text]');
    /* End of copy text to clipboard */

    /*
     ** Modals content
     */

    // Reset the content of a modal to it's default

    window.$(document).on('click', '[data-bs-toggle="modal"]', function(event) {
        let resourcesModal = document.getElementById('resourcesModal');

        let saveButton = document.getElementById('modalButtonAccept');

        let title = event.currentTarget.getAttribute('data-modal-title');
        let modalTitle = resourcesModal.querySelector('.modal-title');

        let modalBody = resourcesModal.querySelector('.modal-body');
        let body = event.currentTarget.getAttribute('data-modal-body');
        let targetURL = event.currentTarget.getAttribute('href');

        modalTitle.innerHTML = title;
        modalBody.innerHTML = body;

        if (targetURL != null && modalBody != null) {
            modalBody.remove();
        }

        saveButton.addEventListener(
            'click',
            () => {
                // When the modal is accepted navigate to the URL of the target element
                if (targetURL) {
                    window.location.href = targetURL;
                }
            },
            { once: true },
        );

        resourcesModal.addEventListener(
            'hidden.bs.modal',
            () => {
                // Reset modal body content when the modal is closed
                resetModalContent('');
            },
            { once: true },
        );
    });
    /* End of Modals content */
});
