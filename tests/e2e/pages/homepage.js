const { BasePage } = require('kakunin');

class HomepagePage extends BasePage {
  constructor() {
    super();

    this.url = '/';

    this.title = element(by.css('header h2 a'));
    this.record_list = element(by.css('ul.record_list'));
  }
}

module.exports = HomepagePage;