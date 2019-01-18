const { BasePage } = require('kakunin');

class HomepagePage extends BasePage {
  constructor() {
    super();

    this.url = '/';

    this.title = $('header a');
    this.recent_pages_list = $('aside ul');
  }
}

module.exports = HomepagePage;