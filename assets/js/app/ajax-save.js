import $ from 'jquery';
import { patience_virtue, renable } from './patience-is-a-virtue';

let form = $('#editcontent');
let record_id = form.data('record');
let element = $('button[name="save"]');

record_id = JSON.stringify(record_id);
/**
 * Start of Ajaxy solution for saving
 */
$(document).ready(function () {
    let dirty = false;
    form.on('change', function () {
        dirty = true;
    });

    if (dirty === true) {
        // The confirmation message is as fallback. Modern browser show their own messages.
        const confirmationMessage =
            'It looks like you have been editing something. ' +
            'If you leave before saving, your changes will be lost.';

        (event || window.event).returnValue = confirmationMessage; //Gecko + IE
        return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    }

    element.on('click', function () {
        $.ajax({
            type: 'POST',
            link: '/edit/' + record_id,
            data: $(form).serialize(),
            beforeSend: function () {
                patience_virtue(element);
            },
            complete: function () {
                renable();
            },
            error: function (jq, status, err) {
                console.log(status, err);
            },
        });
        console.log(dirty)
        if (dirty === true) {
            dirty = false;
        }
    });
});
