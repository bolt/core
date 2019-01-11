const { BasePage } = require('kakunin');

class SingleRecordPage extends BasePage {
  constructor() {
    super();

    this.url = '/record/1';

    this.title = element(by.css('#login-form'));
    this.edit_button = element(by.css('#login-form [name="username"]'));
  }
}

module.exports = SingleRecordPage;