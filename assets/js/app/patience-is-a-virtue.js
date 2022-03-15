import $ from 'jquery';

export function patience_virtue(thisElement) {
    const thisIcon = thisElement.find('i');

    // Bootstrap padding / margin like `mx-2` or `pt-3`
    const addedPadding = thisIcon.attr('class').match(/[mp][tblrxy]-[0-5]/i);
    const newClass = 'fas fa-w fa-cog fa-spin ' + (addedPadding ? addedPadding : '');

    thisElement.attr('data-original-class', thisIcon.attr('class'));
    thisIcon.attr('class', newClass);

    window.setTimeout(function() {
        thisElement.attr('disabled', true);
    }, 50);
}

export function renable() {
    $('*[data-original-class]').each(function() {
        const thisIcon = $(this).find('i');

        $(this).removeAttr('disabled');
        thisIcon.attr('class', $(this).attr('data-original-class'));
    });
}
