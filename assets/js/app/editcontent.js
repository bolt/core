import $ from 'jquery';

let form = $('#editcontent');

console.log(form.serialize());

$.ajax({
    type: 'POST',
    url: '/edit/{id}',
    data: ,
});
