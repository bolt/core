const { BasePage } = require('kakunin');

class EditRecordPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/edit/:id';

    this.title_field = $('#field-title');
    this.save_button = $('button[type="submit"]');
  }
}

module.exports = EditRecordPage;