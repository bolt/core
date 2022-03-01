import $ from 'jquery';
let form = $('#editcontent');

/**
 * Start of Ajaxy solution for saving
 */
$('input[name="save"]').click(function() {
    $.ajax({
        type: 'POST',
        link: '/edit/' + form.data('record'),
        data: $(form).serialize(),
    }).then(console.log('AJAX SUCCESS'));
});
