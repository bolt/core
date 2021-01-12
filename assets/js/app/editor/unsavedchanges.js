import $ from 'jquery';

export default {
    warnFor(element) {
        const changeHandler = function() {
            if (!window.modified) {
                $(window).on('beforeunload', function(e) {
                    // The confirmation message is as fallback. Modern browser show their own messages.
                    const confirmationMessage =
                        'It looks like you have been editing something. ' +
                        'If you leave before saving, your changes will be lost.';

                    (e || window.event).returnValue = confirmationMessage; //Gecko + IE
                    return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
                });
            }

            window.modified = true;
        };

        $(element).on('DOMSubtreeModified', changeHandler);
        $(element).on('change', changeHandler);

        // Warning off if we are saving the content
        $(element).on('submit', function() {
            $(window).off('beforeunload');
        });
    },
};
