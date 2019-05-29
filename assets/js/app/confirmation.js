import bootbox from 'bootbox';

$("*[data-confirmation]").on("click", function (e) {
    let thisHref = $(this).attr('href');
    let confirmation = $(this).data('confirmation');
    bootbox.confirm(confirmation, function(result) {
        if (result) {
            window.location = thisHref;
        }
    });

    return false;
});

