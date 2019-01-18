const { BasePage } = require('kakunin');

class PagesOverviewPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/content/pages';

<<<<<<< Updated upstream
    this.edit_button = element(by.css('#listing .listing__row .listing--actions .link'));
    this.record_title = element(by.css('#listing .listing__row .is-details a'));

    // edit record
    this.title_field = element(by.id('field-title'));
    this.save_button = element(by.css('button[type="submit"]'));
=======
    this.edit_button = $('#listing .listing__row .listing--actions .link');
    this.pager_next = $('nav.listing__filter nav a[rel="next"]');
    this.record_title = $('#listing .listing__row .is-details a');
>>>>>>> Stashed changes
  }
}

module.exports = PagesOverviewPage;