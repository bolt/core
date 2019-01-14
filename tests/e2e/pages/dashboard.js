const { BasePage } = require('kakunin');

class DashboardPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/';

    this.profile_text = element(by.css('#toolbar .is-profile'));
    this.header = element(by.css('.admin__header--title strong'));
  }
}

module.exports = DashboardPage;