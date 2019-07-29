const { BasePage } = require('kakunin');

class FilemanagerPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/filemanager/:area';

    this.header = $('.admin__header--title');
    this.path = $('p.path');

  }
}

module.exports = FilemanagerPage;