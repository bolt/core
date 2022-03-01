import $ from 'jquery';
let form = $('#editcontent');

/**
 * Start of Ajaxy solution for saving
 */
$('button[name="save"]').click(function() {
    $.post({
        link: '/edit/' + form.data('record'),
        data: {
            content: $(form).serialize(),
            ajax: 'true',
        },
        success: alert("SUCCESS"),
    });
});
