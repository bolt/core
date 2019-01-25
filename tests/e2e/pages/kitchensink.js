const { BasePage } = require('kakunin');

class KitchensinkPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/kitchensink';

    this.header = $('.admin__header--title');
    this.title = $('h2');
    this.subtitle = $('h3');
    this.buttons = $$('section.buttons button.btn');
    this.field = $('input[name="foo"]');

    this.title_field = $('input#field-title');
    this.title_label = $('label[for="field-title"]');
    this.title_postfix = $('span#field-title_postfix');

  }
}

module.exports = KitchensinkPage;