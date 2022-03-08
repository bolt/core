import $ from 'jquery';
import { patience_virtue, renable } from './patience-is-a-virtue';
let form = $('#editcontent');
let record_id = form.data('record');

record_id = JSON.stringify(record_id);
/**
 * Start of Ajaxy solution for saving
 */
$('button[name="save"]').click(function() {
    $.ajax({
        type: 'POST',
        link: '/edit/' + record_id,
        data: {
            content: $(form).serialize(),
            ajax: 'true',
        },
        beforeSend: patience_virtue,
        success: renable,
    });
});
