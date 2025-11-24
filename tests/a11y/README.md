
Bolt accessibility tests
===

Bolt uses [Pa11y CI](https://github.com/pa11y/pa11y-ci) for accessibility testing.

Installation
---
`pa11y-ci` is installed alongside all npm requirements in `package.json`.

Usage
---

Run all tests with:
```
npm run a11y:test:all
```

Run a test for a specific page with:
```
npm run a11y:test localhost:8088/bolt/
```

| :warning: Note - by default, tests will not fail on errors                                                                    |
|:------------------------------------------------------------------------------------------------------------------------------|
| By default, the tests will pass even if there are accessibility errors and warnings, based on the configured threshold value. |
| If you want to see what is failing, edit `pa11yci.json`, by removing the `"threshold": {number}` line for the url.            |

