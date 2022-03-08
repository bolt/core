import $ from 'jquery';
import { patience_virtue, renable } from './patience-is-a-virtue';

let i = 0;

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
        data: $(form).serialize(),
        beforeSend: patience_virtue,
        complete: renable,
    });
});
