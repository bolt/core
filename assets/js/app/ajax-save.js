import $ from 'jquery';
import { patience_virtue, renable } from './patience-is-a-virtue';

let form = $('#editcontent');
let record_id = form.data('record');
let element = $('button[name="save"]');

let dom_element =
    '<div class="admin__notifications"><div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="6000"><div id="toastTitle" ><strong id="toastNotification" class="mr-auto"></strong><small id="toastType"></small><button class="ml-2 mb-1 close" aria-label="Close" data-dismiss="toast" type="button"><span aria-hidden="true">&times;</span></button></div><div id="toastBody" class="toast-body"></div></div></div>';

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

    function showToast(
        toastType = 'Error',
        toastMessage = 'Failed trying to save!',
        toastStatus = 'warning',
        notification = 'Notification',
        dom_element = dom_element,
    ) {
        let typeClass = 'alert-' + toastStatus;

        $('.admin__notifications').replaceWith(dom_element);
        $('#toastTitle').addClass(['toast-header', typeClass]);
        $('#toastNotification').append(notification);
        $('#toastType').append(toastType);
        $('#toastBody').append(toastMessage);

        $(document).ready(function() {
            $('.toast').toast('show');
        });

        setTimeout($('.toast').toast('hide'), 5000);
    }
    this.href = window.location.pathname;

    let duplicate_id = this.href.substring(this.href.lastIndexOf('/') + 1);

    let url = window.location.pathname;

    element.on('click', function() {
        $.ajax({
            type: 'POST',
            link: url,
            data: $(form).serialize(),
            beforeSend: function() {
                patience_virtue(element);
            },
            complete: function() {
                renable();
            },
            success: function(data, textStatus) {
                if (!record_id) {
                    window.location.replace(data.url);
                } else if (window.location.pathname === '/bolt/duplicate/' + duplicate_id) {
                    window.location.replace(data.url);
                } else if (textStatus === 'success') {
                    showToast(data.type, data.message, data.status, data.notification, dom_element);
                    $('div[class="admin__header--title-inner"]').html(data.title);
                } else if (data.status !== 'success') {
                    showToast();
                }
            },
            error: function(jq, status, err) {
                // eslint-disable-next-line no-console
                console.log(status, err);
                showToast();
            },
        });
        unsaved = false;
    });
});
