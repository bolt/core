'use strict';

const { matchers, variableStore, defineSupportCode } = require('kakunin');

defineSupportCode(({ Then }) => {
  Then(/^my matcher "([^"]*)" matches "([^"]*)"$/, function (matcher, text) {
    return expect(matchers.match(variableStore.replaceTextVariables(text), matcher)).to.be.true;
  });
});