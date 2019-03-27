const { BasePage } = require('kakunin');

class SearchPage extends BasePage {
  constructor() {
    super();

    this.url = '/search';

    this.search_input = $('#searchform-inline input[name="search"');
    this.search_button = $('#searchform-inline button');

    this.search_results_title = $('h1.search-results');
    this.search_results_description = $('p.search-results-description');
    this.article = $$('article');
  }
}

module.exports = SearchPage;