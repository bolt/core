const { BasePage } = require('kakunin');

class SingleRecordPage extends BasePage {
  constructor() {
    super();

    this.url = '/page/:id';

    this.title = $('h1.title');
    this.heading = $('h1.heading');
    this.edit_button = $('.meta .edit-link a');

    this.taxonomy_tags = $$('.taxonomy-tags');
    this.taxonomy_categories = $$('.taxonomy-categories');
    this.first_category = $('.taxonomy-categories');
    this.article = $$('article');
  }
}

module.exports = SingleRecordPage;