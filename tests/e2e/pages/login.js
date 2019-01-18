const { BasePage } = require('kakunin');

class LoginPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/login';

    this.login_form = $('#login-form');
    this.username = $('#login-form [name="username"]');
    this.password = $('#login-form [name="password"]');
    this.login_button = $('#login-form [type="submit"]');
  }
}

module.exports = LoginPage;