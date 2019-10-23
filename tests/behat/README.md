
Bolt Behat end-to-end tests
===

Bolt uses [Behat](http://docs.behat.org/en/v2.5/guides/1.gherkin.html) for E2E testing.

Installation
---
Be sure that you have Java installed.

Run under `core` folder:
```
./run_behat_tests.sh
```

Usage
---

Run all tests with:
```
make behat-js
```

Running only failed tests is not yet implemented. However you can use tags to run only a few of them!
Just add a tag before scenario, like:
```
Feature: Display record
    @example
    Scenario: As a user I want to display a single record
```
And then run:
```
vendor/bin/behat --tags=example
```

Writing tests
---

Put your tests inside `./tests/behat/` folder.

For describing test scenarios Behat uses [Gherkin syntax](http://docs.behat.org/en/v2.5/guides/1.gherkin.html).

For writing custom steps please refer to [Behat docs](https://docs.behat.org/en/v2.5/guides/2.definitions.html).