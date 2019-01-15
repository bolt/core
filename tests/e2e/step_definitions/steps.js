'use strict';

const { matchers, variableStore, defineSupportCode } = require('kakunin');

defineSupportCode(({ When, Then }) => {
  When(/^I wait for "([^"]*)" element to appear$/, function (elementName) {
    return this.currentPage.waitForVisibilityOf(elementName);
  });

  Then(/^there is element "([^"]*)" with text "([^"]*)"$/, async function (elementName, value) {
    const pageElement = this.currentPage[elementName];

    await this.currentPage.waitForVisibilityOf(elementName);
    return matchers.match(pageElement, variableStore.replaceTextVariables('t:' + value)).then(function (matcherResult) {
      return expect(matcherResult).to.be.true;
    });
  });

  When(/^I fill the "([^"]*)" field with "([^"]*)"$/, async function (elementName, value) {
    await this.currentPage.waitForVisibilityOf(elementName);
    await this.currentPage.fillField(elementName, value);
  });
});