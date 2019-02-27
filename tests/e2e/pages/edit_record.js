const { BasePage } = require('kakunin');

class EditRecordPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/edit/:id';

    this.title_field = $('#field-title');

    this.status_select = $('#metadata > form > div:nth-child(2) > div > div:nth-child(1) > div > div > div.multiselect__select');
    this.status_published = $('#metadata > form > div:nth-child(2) > div > div:nth-child(1) > div > div > div.multiselect__content-wrapper > ul > li:nth-child(1) > span');

    this.lang_select = $('#metadata > form > div:nth-child(2) > div > div > div > div.multiselect__select');
    this.lang_nl = $('#metadata > form > div:nth-child(2) > div > div > div > div.multiselect__content-wrapper > ul > li:nth-child(2) > span');

    this.tab_media = $('#media-tab');
    this.embed_field = $('[name="fields[embed][url]"]');
    this.embed_width = $('[name="fields[embed][width]"]');
    this.embed_height = $('[name="fields[embed][height]"]');
    this.embed_title = $('[name="fields[embed][title]"]');
    this.embed_author = $('[name="fields[embed][authorname]"]');

    this.save_button = $('button[type="submit"]');
  }
}

module.exports = EditRecordPage;
