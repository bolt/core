const { BasePage } = require('kakunin');

class EditRecordPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/edit/:id';

    this.title_field = element(by.id('field-title'));
    this.save_button = element(by.css('button[type="submit"]'));
  }
}

module.exports = EditRecordPage;