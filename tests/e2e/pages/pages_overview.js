const { BasePage } = require('kakunin');

class PagesOverviewPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/content/pages';

    this.edit_button = $('#listing .listing__row .listing--actions .link');
    this.pager_next = $('.pagination a[rel="next"]');
    this.record_title = $('#listing .listing__row .is-details a');
  }
}

module.exports = PagesOverviewPage;
