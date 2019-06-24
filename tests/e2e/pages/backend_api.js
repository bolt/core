const { BasePage } = require('kakunin');

class BackendApiPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/api';

    this.header = $('.admin__header--title strong');
  }
}

module.exports = BackendApiPage;