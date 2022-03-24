import $ from 'jquery';
import { patience_virtue, renable } from './patience-is-a-virtue';

let form = $('#editcontent');
let record_id = form.data('record');
let element = $('button[name="save"]');

const toastSuccess =
    '            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="6000">\n' +
    '                <div class="alert-success toast-header">\n' +
    '                    <strong class="mr-auto"></strong>\n' +
    '                    <small>Success</small>\n' +
    '                    <button class="ml-2 mb-1 close" aria-label="Close" data-dismiss="toast" type="button">\n' +
    '                        <span aria-hidden="true">&times;</span>\n' +
    '                    </button>\n' +
    '                </div>\n' +
    '                <div class="toast-body">Content updated succesfully</div>\n' +
    '            </div>';

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

    function showToast(toastType) {
        $(toastType).append('.admin__notifications');
    }

    function hideToast(toastType) {
        $(toastType).detach('.admin__notifications');
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
            success: function(data, textStatus) {
                if (!record_id) {
                    window.location.replace(data);
                } else {
                    showToast(toastSuccess);
                    setTimeout(hideToast(toastSuccess), 5000);
                }
            },
            error: function(jq, status, err) {
                console.log(status, err);
            },
        });
        unsaved = false;
    });
});
