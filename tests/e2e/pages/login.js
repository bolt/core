const { BasePage } = require('kakunin');

class LoginPage extends BasePage {
  constructor() {
    super();

    this.url = '/bolt/login';

    this.login = element(by.css('#login-form'));
    this.username = element(by.css('#login-form [name="username"]'));
    this.password = element(by.css('#login-form [name="password"]'));
    this.login_button = element(by.css('#login-form [type="submit"]'));
  }
}

module.exports = LoginPage;