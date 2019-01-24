const { BasePage } = require('kakunin');

class EditRecordPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/edit/:id';

    this.title_field = $('#field-title');
    this.status_select = $('#metadata > form > div:nth-child(2) > div > div:nth-child(1) > div > div > div.multiselect__select');
    this.status_published = $('#metadata > form > div:nth-child(2) > div > div:nth-child(1) > div > div > div.multiselect__content-wrapper > ul > li:nth-child(1) > span');
    this.save_button = $('button[type="submit"]');
  }
}

module.exports = EditRecordPage;