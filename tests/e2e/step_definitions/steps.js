'use strict';

const { matchers, variableStore, defineSupportCode } = require('kakunin');

defineSupportCode(({ When, Then }) => {
  When(/^I wait for "([^"]*)" element to appear$/, function (elementName) {
    return this.currentPage.waitForVisibilityOf(elementName);
  });

  Then(/^there is element "([^"]*)" with text "([^"]*)"$/, function (elementName, value) {
    const pageElement = this.currentPage[elementName];

    return this.currentPage.waitForVisibilityOf(elementName).then(() => {

      return matchers.match(pageElement, variableStore.replaceTextVariables('t:' + value)).then(function (matcherResult) {
        return expect(matcherResult).to.be.true;
      });
    });
  });
});