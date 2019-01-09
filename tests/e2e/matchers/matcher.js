'use strict';

const { regexBuilder, matchers } = require('kakunin');

const ExampleMatcher = {
  isSatisfiedBy: function (prefix, name) {
    return prefix === 'e';
  },

  match: function (element, regexName) {
    const regex = regexBuilder.buildRegex(`r:${regexName}`);

    return regex.test(element);
  }
};

matchers.addMatcher(ExampleMatcher);