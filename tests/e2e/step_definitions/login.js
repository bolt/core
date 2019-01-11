const { defineSupportCode } = require('kakunin');

defineSupportCode(function ({ Given }) {
  Given(/^I am logged in as "([^"]*)"$/, async function (userName) {
    this.currentUser = this.userProvider.getUser(userName);
    this.currentPage = browser.page['login'];
    await this.currentPage.waitForVisibilityOf('username');
    await this.currentPage.fillField('username', this.currentUser.username);
    await this.currentPage.fillField('password', this.currentUser.password);
    await this.currentPage.click('login_button');
  });
});