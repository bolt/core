const { BasePage } = require('kakunin');

class PagesOvewviewPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/content/pages';

    this.edit_button = element(by.css('#listing .listing__row .listing--actions .link'));
    this.record_title = element(by.css('#listing .listing__row .is-details a'));

    // edit record
    this.title_field = element(by.id('field-title'));
    this.save_button = element(by.css('button[type="submit"]'));
  }
}

module.exports = PagesOvewviewPage;