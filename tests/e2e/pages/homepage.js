const { BasePage } = require('kakunin');

class HomepagePage extends BasePage {
  constructor() {
    super();

    this.url = '/';

    this.title = $('header a');
    this.recent_pages_list = $('aside ul');

    this.search_input = $('#searchform-inline input[name="searchTerm"');
    this.search_button = $('#searchform-inline button');

    // Attributes for testing the menu
    this.menu_first = $('ul.menu li.first');
    this.menu_sub = $$('ul.menu li.has-submenu li');
    this.menu_last = $('ul.menu li.last a.bolt-site');
  }
}

module.exports = HomepagePage;