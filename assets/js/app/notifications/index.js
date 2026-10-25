import $ from 'jquery';
import { Toast } from 'bootstrap';

$(document).ready(function () {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    const toastList = toastElList.map(function (toastEl) {
        return new Toast(toastEl, []);
    });

    toastList.forEach(function (toast) {
        toast.show();
    });
});
