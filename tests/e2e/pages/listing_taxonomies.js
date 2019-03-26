const { BasePage } = require('kakunin');

class ListingTaxonomiesPage extends BasePage {
  constructor() {
    super();

    this.url = '/:type/:slug';

    this.article = $$('article');
  }
}

module.exports = ListingTaxonomiesPage;