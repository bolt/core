const { BasePage } = require('kakunin');

class SingleRecordPage extends BasePage {
  constructor() {
    super();

    this.url = '/page/:id';

    this.title = $('h1');
    this.edit_button = $('.meta .edit-link a');
  }
}

module.exports = SingleRecordPage;