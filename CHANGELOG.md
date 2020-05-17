
Changelog
---------

## 4.0.0-rc.19

Released: 2020-05-17

### 🐛 Bug fixes

- Feature/set default (I-Valchev, [#1362](https://github.com/bolt/core/pull/1362))
- Bugfix/collections default fix (I-Valchev, [#1353](https://github.com/bolt/core/pull/1353))

### 📦 Additions and new features

-  Improve `title_format` and add `excerpt_format` attribute in ContentTypes (bobdenotter, [#1352](https://github.com/bolt/core/pull/1352))
- Add length attribute for `|title` filter (bobdenotter, [#1349](https://github.com/bolt/core/pull/1349))

### 🛠️ Miscellaneous

- Refactor required attribute to be bool, not int (I-Valchev, [#1368](https://github.com/bolt/core/pull/1368))
- Return correct exit status for cache:thumbs, bolt:reset-secret and bolt:info (I-Valchev, [#1366](https://github.com/bolt/core/pull/1366))

### 🤖 Tests

- Files extras tests (I-Valchev, [#1367](https://github.com/bolt/core/pull/1367))

### 📖 Documentation

- Update README - Note about `var/data/bolt.sqlite` (bobdenotter, [#1364](https://github.com/bolt/core/pull/1364))

### ⚙️ Code Quality / Developer Experience

- Composer dep updates (bobdenotter, [#1380](https://github.com/bolt/core/pull/1380))
- Don't fail on 'Textarea' when it exists (I-Valchev, [#1377](https://github.com/bolt/core/pull/1377))
- NPM Updates, fixing tests (bobdenotter, [#1376](https://github.com/bolt/core/pull/1376))
- Updating some Easy Coding Standards settings (bobdenotter, [#1350](https://github.com/bolt/core/pull/1350))
- Adding the new Changelog!  (bobdenotter, [#1348](https://github.com/bolt/core/pull/1348))
- Update tests readme with up-to-date instructions (I-Valchev, [#1346](https://github.com/bolt/core/pull/1346))
- Release 4.0.0-RC.18 (bobdenotter, [#1344](https://github.com/bolt/core/pull/1344))

### 🎨 Design / User Interface

- Tweaking a bunch of small things: Margins, paddings, minor UX thingies (I-Valchev, [#1369](https://github.com/bolt/core/pull/1369))

## 4.0.0-rc.18

Released: 2020-05-01

### 🐛 Bug fixes

 - Fix flawed logic in Setcontent test page (bobdenotter) [#1342](https://github.com/bolt/core/pull/1342)
 - Sidebar priority fixed on mobile too (I-Valchev) [#1341](https://github.com/bolt/core/pull/1341)
 - API works if no viewless CTs exist (I-Valchev) [#1338](https://github.com/bolt/core/pull/1338)
 - Update content updated successfully message (I-Valchev) [#1331](https://github.com/bolt/core/pull/1331)
 - Invalidate localized menu cache (I-Valchev) [#1326](https://github.com/bolt/core/pull/1326)
 - Update `Field.php`: foreign key constraint issue affecting Collections and Sets (JTNMW) [#1325](https://github.com/bolt/core/pull/1325)
 - Record link filter persists current locale (I-Valchev) [#1316](https://github.com/bolt/core/pull/1316)
 - Record canonical URLs are unique for `record` and `record_locale` routes (I-Valchev) [#1315](https://github.com/bolt/core/pull/1315)
 - Backend menu caches localized (I-Valchev) [#1314](https://github.com/bolt/core/pull/1314)
 - Admin can duplicate a file (I-Valchev) [#1313](https://github.com/bolt/core/pull/1313)

### 📦 Additions and new features

 - Require confirmation to delete collection item in editor (I-Valchev) [#1343](https://github.com/bolt/core/pull/1343)
 - Better `isHomepage` detection for singletons (bobdenotter) [#1337](https://github.com/bolt/core/pull/1337)
 - Make it so `homepage:` accepts a singleton, or a contentType listing (bobdenotter) [#1336](https://github.com/bolt/core/pull/1336)

### ⚙️ Code Quality / Developer Experience

 - Fix phpstan failing on parser::create (I-Valchev) [#1339](https://github.com/bolt/core/pull/1339)
 - Working on NPM bitrot - attempt 2 (bobdenotter) [#1335](https://github.com/bolt/core/pull/1335)
 - Tidy up Twig RelatedExtension (I-Valchev) [#1328](https://github.com/bolt/core/pull/1328)
 - Remove unused Bolt\Entity\Field import (I-Valchev) [#1327](https://github.com/bolt/core/pull/1327)

## 4.0.0-rc.17

Released: 2020-04-19

### 🐛 Bug fixes

 - Fix flawed logic in Setcontent test page (bobdenotter) [#1342](https://github.com/bolt/core/pull/1342)
 - Sidebar priority fixed on mobile too (I-Valchev) [#1341](https://github.com/bolt/core/pull/1341)
 - API works if no viewless CTs exist (I-Valchev) [#1338](https://github.com/bolt/core/pull/1338)
 - Update content updated successfully message (I-Valchev) [#1331](https://github.com/bolt/core/pull/1331)
 - Invalidate localized menu cache (I-Valchev) [#1326](https://github.com/bolt/core/pull/1326)
 - Update `Field.php`: foreign key constraint issue affecting Collections and Sets (JTNMW) [#1325](https://github.com/bolt/core/pull/1325)
 - Record link filter persists current locale (I-Valchev) [#1316](https://github.com/bolt/core/pull/1316)
 - Record canonical URLs are unique for `record` and `record_locale` routes (I-Valchev) [#1315](https://github.com/bolt/core/pull/1315)
 - Backend menu caches localized (I-Valchev) [#1314](https://github.com/bolt/core/pull/1314)
 - Admin can duplicate a file (I-Valchev) [#1313](https://github.com/bolt/core/pull/1313)

### 📦 Additions and new features

 - Require confirmation to delete collection item in editor (I-Valchev) [#1343](https://github.com/bolt/core/pull/1343)
 - Better `isHomepage` detection for singletons (bobdenotter) [#1337](https://github.com/bolt/core/pull/1337)
 - Make it so `homepage:` accepts a singleton, or a contentType listing (bobdenotter) [#1336](https://github.com/bolt/core/pull/1336)

### ⚙️ Code Quality / Developer Experience

 - Fix phpstan failing on parser::create (I-Valchev) [#1339](https://github.com/bolt/core/pull/1339)
 - Working on NPM bitrot - attempt 2 (bobdenotter) [#1335](https://github.com/bolt/core/pull/1335)
 - Tidy up Twig RelatedExtension (I-Valchev) [#1328](https://github.com/bolt/core/pull/1328)
 - Remove unused Bolt\Entity\Field import (I-Valchev) [#1327](https://github.com/bolt/core/pull/1327)
 - Preparing release 4.0.0-rc.17 (bobdenotter) [#1312](https://github.com/bolt/core/pull/1312)


## 4.0.0-rc.16

Released: 2020-04-13

### 🛠️ Miscellaneous

 - Feature/badges for special pages (bobdenotter) [#1311](https://github.com/bolt/core/pull/1311)
 - Add Labels for statuses (bobdenotter) [#1310](https://github.com/bolt/core/pull/1310)
 - Api filter updates (technicallyerik) [#1309](https://github.com/bolt/core/pull/1309)
 - API shows published and viewless: false content only (I-Valchev) [#1305](https://github.com/bolt/core/pull/1305)
 - Mass delete on last page does not break. Content listing redirects to last page if requested page exceeds max (I-Valchev) [#1304](https://github.com/bolt/core/pull/1304)
 - New "Create new..." link has correct href (I-Valchev) [#1303](https://github.com/bolt/core/pull/1303)
 - Add missing labels to bulk actions (I-Valchev) [#1302](https://github.com/bolt/core/pull/1302)
 - Field::getValue() returns defaultLocale value (if current locale value is empty empty) for non-localizable fields (I-Valchev) [#1300](https://github.com/bolt/core/pull/1300)
 - Get correct field value depending on localization settings (I-Valchev) [#1299](https://github.com/bolt/core/pull/1299)
