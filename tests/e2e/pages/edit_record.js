const { BasePage } = require('kakunin');

class EditRecordPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/edit/:id';

    this.title_field = $('#field-title');

    this.status_select = $('#multiselect-field-status div.multiselect__select');
    this.status_published = $('#multiselect-field-status div.multiselect__content-wrapper > ul > li:nth-child(1) > span');

    this.lang_select = $('#multiselect-localeswitcher div.multiselect__select');
    this.lang_nl = $('#multiselect-localeswitcher div.multiselect__content-wrapper > ul > li:nth-child(2) > span');

    this.tab_media = $('#media-tab');
    this.embed_field = $('[name="fields[embed][url]"]');
    this.embed_width = $('[name="fields[embed][width]"]');
    this.embed_height = $('[name="fields[embed][height]"]');
    this.embed_title = $('[name="fields[embed][title]"]');
    this.embed_author = $('[name="fields[embed][authorname]"]');

    this.save_button = $('#metadata [type="submit"]:not([formaction])');
    this.preview_button = $('#metadata [type="submit"][formaction*="/bolt/preview/"]');
    this.dropdown_button = $('#metadata button.dropdown-toggle');
    this.viewsaved_button = $('#metadata button[type="submit"][formaction*="/bolt/viewsaved/"]');

    this.frontend_title = $('h1.title');
  }
}

module.exports = EditRecordPage;
