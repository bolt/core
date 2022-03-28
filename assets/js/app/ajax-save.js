import $ from 'jquery';
import { patience_virtue, renable } from './patience-is-a-virtue';

let form = $('#editcontent');
let record_id = form.data('record');
let element = $('button[name="save"]');

let dom_element =
    '<div class="admin__notifications"><div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="6000"><div id="toastTitle" class="toast-header alert-success" ><strong id="toastNotification" class="mr-auto"></strong><small id="toastType"></small><button class="ml-2 mb-1 close" aria-label="Close" data-dismiss="toast" type="button"><span aria-hidden="true">&times;</span></button></div><div id="toastBody" class="toast-body"></div></div></div>';

record_id = JSON.stringify(record_id);
/**
 * Start of Ajaxy solution for saving
 */
$(document).ready(function() {
    let unsaved = false;

    $(form).change(function() {
        //triggers change in all input fields including text type
        unsaved = true;
    });

    function unloadPage() {
        if (unsaved) {
            return 'You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?';
        }
    }

    window.onbeforeunload = unloadPage;

    function showToast(toastType, toastMessage, toastStatus, notification, dom_element) {
        // Refactor this so it looks cleaner.
        $('.admin__notifications').replaceWith(dom_element);
        $('#toastNotification').append(notification);
        $('#toastType').append(toastType);
        $('#toastBody').append(toastMessage);

        $(document).ready(function() {
            $('.toast').toast('show');
        });

        setTimeout($('.toast').toast('hide'), 5000);
    }

    element.on('click', function() {
        $.ajax({
            type: 'POST',
            link: '/edit/' + record_id,
            data: $(form).serialize(),
            beforeSend: function() {
                patience_virtue(element);
            },
            complete: function() {
                renable();
            },
            success: function(data) {
                if (!record_id) {
                    window.location.replace(data.url);
                } else {
                    showToast(data.type, data.message, data.status, data.notification, dom_element);
                }
            },
            error: function(jq, status, err) {
                // eslint-disable-next-line no-console
                console.log(status, err);
                showToast('error', 'Failed saving', 'Failed', 'Error', dom_element);
            },
        });
        unsaved = false;
    });
});
