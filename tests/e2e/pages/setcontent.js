const { BasePage } = require('kakunin');

class SetcontentPage extends BasePage {
  constructor() {
    super();

    this.url = '/page/setcontent-test-page';

    this.results_one = $('#results-one');
    this.results_two = $('#results-two');
    this.results_four = $('#results-four');
    this.results_five = $('#results-five');
    this.results_six = $('#results-six');

    this.three_s1 = $('#three .s1');
    this.three_s2 = $('#three .s2');

    this.body = $('body');

  }
}

module.exports = SetcontentPage;