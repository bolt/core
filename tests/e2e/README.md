Bolt end-to-end tests
===

Bolt uses [Kakunin](https://thesoftwarehouse.github.io/Kakunin/docs/index.html) for E2E testing.

Installation
---

Under `./tests/e2e/` folder, run:
```
npm install
node ./node_modules/protractor/bin/webdriver-manager update --gecko=false
```

Usage
---

Run tests with:
```
npm run kakunin
```

Writing tests
---

Put your tests inside `features/` folder.

For describing test scenarions Kakunin uses [Gherkin syntax](https://docs.cucumber.io/gherkin/reference/), same as in (or at least similar to) [Behat](http://docs.behat.org/en/v2.5/guides/1.gherkin.html).

For writing custom steps please refer to [Kakunin docs](https://thesoftwarehouse.github.io/Kakunin/docs/extending#adding-custom-code).

Fix for JetBrains IDE
---

To fix hinting steps in `cucumber.js` plugin, run:

For Linux/MacOS:

```
cd step_definitions
ln -s ../node_modules/kakunin/dist/step_definitions/elements.js kakunin-elements.js
ln -s ../node_modules/kakunin/dist/step_definitions/debug.js kakunin-debug.js
ln -s ../node_modules/kakunin/dist/step_definitions/file.js kakunin-file.js
ln -s ../node_modules/kakunin/dist/step_definitions/form.js kakunin-form.js
ln -s ../node_modules/kakunin/dist/step_definitions/email.js kakunin-email.js
ln -s ../node_modules/kakunin/dist/step_definitions/generators.js kakunin-generators.js
ln -s ../node_modules/kakunin/dist/step_definitions/navigation.js kakunin-navigation.js 
cd ..
```

For Windows 8+: (you have to do this as administrator)

```
cd step_definitions
mklink kakunin-elements.js ..\node_modules\kakunin\dist\step_definitions\elements.js"
mklink kakunin-debug.js ..\node_modules\kakunin\dist\step_definitions\debug.js"
mklink kakunin-file.js ..\node_modules\kakunin\dist\step_definitions\file.js"
mklink kakunin-form.js ..\node_modules\kakunin\dist\step_definitions\form.js"
mklink kakunin-email.js ..\node_modules\kakunin\dist\step_definitions\email.js"
mklink kakunin-generators.js ..\node_modules\kakunin\dist\step_definitions\generators.js"
mklink kakunin-navigation.js ..\node_modules\kakunin\dist\step_definitions\navigation.js"
cd ..
```
