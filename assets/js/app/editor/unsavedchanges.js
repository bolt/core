import $ from 'jquery';

export default {
    warnFor(element) {
        const warn = function (event) {
            // The confirmation message is as fallback. Modern browser show their own messages.
            const confirmationMessage =
                'It looks like you have been editing something. ' +
                'If you leave before saving, your changes will be lost.';

            (event || window.event).returnValue = confirmationMessage; //Gecko + IE
            return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
        };

        const getFormData = function (element) {
            return $(element).serialize();
        };

        const originalData = getFormData(element);

        $(window).on('beforeunload', function (event) {
            const currentData = getFormData(element);

            if (originalData === currentData) {
                return;
            }

            return warn(event);
        });

        // Warning off if we are saving the content
        $(element).on('submit', function () {
            $(window).off('beforeunload');
        });
    },
};
