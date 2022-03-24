import $ from 'jquery';
import { patience_virtue, renable } from './patience-is-a-virtue';

let form = $('#editcontent');
let record_id = form.data('record');
let element = $('button[name="save"]');

record_id = JSON.stringify(record_id);
/**
 * Start of Ajaxy solution for saving
 */
$(document).ready(function() {
    var unsaved = false;

    $(':input').change(function() {
        //triggers change in all input fields including text type
        unsaved = true;
    });

    function unloadPage() {
        if (unsaved) {
            return 'You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?';
        }
    }

    window.onbeforeunload = unloadPage;

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
                    $(document).ready(function() {
                        $('.toast').toast('show');
                    });
                }
            },
            error: function(jq, status, err) {
                console.log(status, err);
            },
        });
        unsaved = false;
    });
});
