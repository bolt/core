import $ from 'jquery';
import { Toast } from 'bootstrap';

var className = document.getElementsByClassName('.toast');

$(document).ready(function() {
    new Toast(className).show;
});
