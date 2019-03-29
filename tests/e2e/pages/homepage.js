const { BasePage } = require('kakunin');

class HomepagePage extends BasePage {
  constructor() {
    super();

    this.url = '/';

    this.title = $('header a');
    this.recent_pages_list = $('aside ul');

    this.search_input = $('#searchform-inline input[name="searchTerm"');
    this.search_button = $('#searchform-inline button');
  }
}

module.exports = HomepagePage;