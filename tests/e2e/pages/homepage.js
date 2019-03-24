const { BasePage } = require('kakunin');

class HomepagePage extends BasePage {
  constructor() {
    super();

    this.url = '/';

    this.title = $('header a');
    this.recent_pages_list = $('aside ul');

    // For testing "search"
    this.search_input = $('#searchform-inline input[name="search"');
    this.search_button = $('#searchform-inline button');

    this.search_results_title = $('h1.search-results');
    this.article = $$('article');
  }
}

module.exports = HomepagePage;