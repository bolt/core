const { BasePage } = require('kakunin');

class DashboardPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/';

    this.profile_text = $('#toolbar .toolbar-item__profile');
    this.header = $('.admin__header--title');
    this.records = $$('.listing__row');
    this.first_record = $('.listing__row');

    // Test for widget
    this.widget_title = $('#widget-news-widget .card-header');
  }
}

module.exports = DashboardPage;