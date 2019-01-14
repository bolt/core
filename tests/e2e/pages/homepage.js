const { BasePage } = require('kakunin');

class HomepagePage extends BasePage {
  constructor() {
    super();

    this.url = '/';

    this.title = element(by.css('header a'));
    this.recent_pages_list = element(by.css('aside ul'));
  }
}

module.exports = HomepagePage;