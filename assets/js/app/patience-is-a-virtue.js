import $ from 'jquery';

export function patience_virtue() {
    const thisElement = $('a[data-patience], button[data-patience]');
    const thisIcon = thisElement.find('i');

    console.log(thisElement);

    // If we're handling a form, and the form's not valid, we can stop here
    if (thisElement.attr('form') && !$('#' + thisElement.attr('form'))[0].checkValidity()) {
        return;
    }

    // Bootstrap padding / margin like `mx-2` or `pt-3`
    const addedPadding = thisIcon.attr('class').match(/[mp][tblrxy]-[0-5]/i);
    const newClass = 'fas fa-w fa-cog fa-spin ' + (addedPadding ? addedPadding : '');

    console.log(thisElement);

    $(this).attr('data-original-class', thisIcon.attr('class'));
    thisIcon.attr('class', newClass);

    window.setTimeout(function() {
        thisElement.attr('disabled', true);
    }, 50);
}

export function renable() {
    $('*[data-original-class]').each(function() {
        const thisIcon = $(this).find('i');

        $(this).attr('disabled', false);
        thisIcon.attr('class', $(this).attr('data-original-class'));
    });
}
