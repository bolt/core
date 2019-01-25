const { BasePage } = require('kakunin');

class FilemanagerPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/filemanager/:area';

    this.header = $('.admin__header--title');
    this.title = $('h2');

  }
}

module.exports = FilemanagerPage;