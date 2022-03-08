import $ from 'jquery';
import { patience_virtue, renable } from './patience-is-a-virtue';

let form = $('#editcontent');
let record_id = form.data('record');
let element = $('button[name="save"]');

record_id = JSON.stringify(record_id);
/**
 * Start of Ajaxy solution for saving
 */
element.click(function() {
    $.ajax({
        type: 'POST',
        link: '/edit/' + record_id,
        data: $(form).serialize(),
        beforeSend: patience_virtue(element),
        complete: { renable },
    });
});
