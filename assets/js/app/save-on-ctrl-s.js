import $ from 'jquery';
import hotkeys from 'hotkeys-js';

hotkeys('ctrl+s, command+s', function () {
    if (!$('form#editcontent')) return true;

    $('form#editcontent button[name=save]').click();
    return false;
});
