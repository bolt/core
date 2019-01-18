const { BasePage } = require('kakunin');

class DashboardPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/';

    this.profile_text = $('#toolbar .is-profile');
    this.header = $('.admin__header--title strong');
  }
}

module.exports = DashboardPage;