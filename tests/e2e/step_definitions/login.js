const { defineSupportCode } = require('kakunin');

defineSupportCode(function ({ Given }) {
  Given(/^I am logged in as "([^"]*)"$/, async function (user) {

    this.currentUser = this.userProvider.getUser(user);
    this.currentPage = browser.page.login;

    await this.currentPage.visit();
    await this.currentPage.waitForVisibilityOf('login_form');
    await this.currentPage.fillField('username', this.currentUser.username);
    await this.currentPage.fillField('password', this.currentUser.password);
    await this.currentPage.click('login_button');
  });
});