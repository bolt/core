const { BasePage } = require('kakunin');

class SingleTestPage extends BasePage {
  constructor() {
    super();

    this.url = '/page/:slug';

    this.title = $('h1.title');
    this.heading = $('h1.heading');

    this.text_markup_a = $('.text_markup_a');
    this.text_markup_b = $('.text_markup_b');
    this.text_markup_c = $('.text_markup_c');

    this.text_plain_a = $('.text_plain_a');
    this.text_plain_b = $('.text_plain_b');
    this.text_plain_c = $('.text_plain_c');

    this.textarea_field = $('.text_textarea');
    this.html_field = $('.text_html');
    this.markdown_field = $('.text_markdown');
  }
}

module.exports = SingleTestPage;