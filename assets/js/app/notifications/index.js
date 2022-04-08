import $ from 'jquery';
import { Toast } from 'bootstrap';

$(document).ready(function() {
    var toastElList = [].slice.call(document.querySelectorAll('.toast'));
    var toastList = toastElList.map(function(toastEl) {
        return new Toast(toastEl, []);
    });

    toastList.forEach(function(toast) {
        toast.show();
    });
});
