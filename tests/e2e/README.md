Bolt end-to-end tests
===

Bolt uses [Kakunin](https://thesoftwarehouse.github.io/Kakunin/docs/index.html) for E2E testing.

Install
---

Under `./tests/e2e/` folder, run:
```
npm install
node ./node_modules/protractor/bin/webdriver-manager update --gecko=false
```

Usage
---

Put your tests inside `features/` folder.

Run tests with:
```
npm run kakunin
```

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
