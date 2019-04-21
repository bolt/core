const { BasePage } = require('kakunin');

class BackendTranslationsPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/_trans';

    this.header = $('.admin__header--title');
  }
}

module.exports = BackendTranslationsPage;