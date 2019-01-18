const { BasePage } = require('kakunin');

class SingleRecordPage extends BasePage {
  constructor() {
    super();

    this.url = '/page/:id';

    this.title = element(by.css('h1'));
    this.edit_button = element(by.css('.meta .edit-link a'));
  }
}

module.exports = SingleRecordPage;