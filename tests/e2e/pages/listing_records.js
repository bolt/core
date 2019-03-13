const { BasePage } = require('kakunin');

class ListingRecordsPage extends BasePage {
  constructor() {
    super();

    this.url = '/entries';

    this.heading = $('article h2');
    this.pagination = $('.pagination');
    this.next = $('.pagination a[rel="next"]');
    this.current = $('.pagination span.current');
  }
}

module.exports = ListingRecordsPage;