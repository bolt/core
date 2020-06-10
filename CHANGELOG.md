Changelog
=========

## 4.0.0-rc.24

Released: 2020-06-10

### 📦 Additions and new features

- Add `babdev/pagerfanta-bundle`,  `squirrelphp/twig-php-syntax` (bobdenotter, [#1466](https://github.com/bolt/core/pull/1466))

### 🐛 Bug fixes

- Fix setting of `is_writable` when submitted Yaml syntax is incorrect (bobdenotter, [#1465](https://github.com/bolt/core/pull/1465))
- Intersect child fields with parent definition (I-Valchev, [#1464](https://github.com/bolt/core/pull/1464))
- Correct `type: checkbox` Field fixtures (I-Valchev, [#1463](https://github.com/bolt/core/pull/1463))
- (Real) fix for `htmllang()` default locale (I-Valchev, [#1462](https://github.com/bolt/core/pull/1462))
- Make sure list field `|length` filter works as expected (I-Valchev, [#1455](https://github.com/bolt/core/pull/1455))
- Better check for `isSpecialPage` if `homepage:` is set to a contenttype (listing) (bobdenotter, [#1451](https://github.com/bolt/core/pull/1451))

### ⚙️ Code Quality / Developer Experience

- Fix `ObjectManager` namespace for fixtures (phpstan complaining) (I-Valchev, [#1456](https://github.com/bolt/core/pull/1456))
- Bump `websocket-extensions` from `0.1.3` to `0.1.4` (dependabot[bot], [#1450](https://github.com/bolt/core/pull/1450))

---

## 4.0.0-rc.23

Released: 2020-06-06

### 🐛 Bug fixes

- Make sure editing text fields triggers slugify (bobdenotter, [#1440](https://github.com/bolt/core/pull/1440))
- Fix `htmllang()` when no locales defined (I-Valchev, [#1439](https://github.com/bolt/core/pull/1439))
- Fix for windows not handling `%k` in timestamp strings properly (bobdenotter, [#1448](https://github.com/bolt/core/pull/1448))

### ⚙️ Code Quality / Developer Experience

- Remove locale setting from bolt config (legacy) (I-Valchev, [#1441](https://github.com/bolt/core/pull/1441))
- Remove old field types (I-Valchev, [#1433](https://github.com/bolt/core/pull/1433))

---

## 4.0.0-rc.22

Released: 2020-06-02

### 🐛 Bug fixes

- Fix collections visibility (I-Valchev, [#1429](https://github.com/bolt/core/pull/1429))
- Localize collections with easier contenttype config (I-Valchev, [#1434](https://github.com/bolt/core/pull/1434))
- Allow comparison for scalar fields (bobdenotter, [#1428](https://github.com/bolt/core/pull/1428))

---

## 4.0.0-rc.21

Released: 2020-05-31

### 🐛 Bug fixes

- Don't show Fields that were removed from the ContentType definition (bobdenotter, [#1426](https://github.com/bolt/core/pull/1426))
- Unescape Vue input fields (bobdenotter, [#1425](https://github.com/bolt/core/pull/1425))
- Save non-localizable in correct default locale (I-Valchev, [#1424](https://github.com/bolt/core/pull/1424))
- Forbid templateselect inside collection. Fix forbidden fields check. (I-Valchev, [#1423](https://github.com/bolt/core/pull/1423))
- Pretty `record|link` when localization is enabled (I-Valchev, [#1421](https://github.com/bolt/core/pull/1421))
- Allow for parsed dates like "Today" or "in 3 weeks" (bobdenotter, [#1418](https://github.com/bolt/core/pull/1418))
- Update file listing screen: Lazy loading, paginator, other improvements (bobdenotter, [#1413](https://github.com/bolt/core/pull/1413))
- Make sure you can iterate over collection with one field (I-Valchev, [#1412](https://github.com/bolt/core/pull/1412))
- Allow `x` as well as `×` in thumbail URLs (bobdenotter, [#1411](https://github.com/bolt/core/pull/1411))

### 📦 Additions and new features

- Image and File fields support twig `is empty` test (I-Valchev, [#1419](https://github.com/bolt/core/pull/1419))

### 🛠️ Miscellaneous

- Adding spinners and disabling buttons to indicate the user might have to wait a few seconds (bobdenotter, [#1414](https://github.com/bolt/core/pull/1414))
- Add `ListServices()` method to ServiceTrait (bobdenotter, [#1406](https://github.com/bolt/core/pull/1406))

### ⚙️ Code Quality / Developer Experience

- Add `composer/package-versions-deprecated` (bobdenotter, [#1416](https://github.com/bolt/core/pull/1416))

---

## 4.0.0-rc.20

Released: 2020-05-24

### 🐛 Bug fixes

- View saved version in current edit locale (I-Valchev, [#1388](https://github.com/bolt/core/pull/1388))
- Break slightly less when adding new contenttypes (bobdenotter, [#1386](https://github.com/bolt/core/pull/1386))
- Format title works without breaking on new content. Sorting content by title uses format_title. (I-Valchev, [#1385](https://github.com/bolt/core/pull/1385))
- Persist non-translatable field with correct default locale (I-Valchev, [#1384](https://github.com/bolt/core/pull/1384))

### 📦 Additions and new features

- Add Configuration Notices extension (bobdenotter, [#1391](https://github.com/bolt/core/pull/1391))

### 🛠️ Miscellaneous

- Display upload limits and better feedback on failure (bobdenotter, [#1404](https://github.com/bolt/core/pull/1404))
- View content type button on hover (I-Valchev, [#1400](https://github.com/bolt/core/pull/1400))
- Get taxonomy by slug (I-Valchev, [#1397](https://github.com/bolt/core/pull/1397))
- Wrap list fields with 1 element in array (I-Valchev, [#1396](https://github.com/bolt/core/pull/1396))
- Minor CSS changes, update Symfony Encore (bobdenotter, [#1392](https://github.com/bolt/core/pull/1392))
- Users page uses abbr relative date (I-Valchev, [#1382](https://github.com/bolt/core/pull/1382))

### ⚙️ Code Quality / Developer Experience

- Add todo to stop selenium server process (I-Valchev, [#1393](https://github.com/bolt/core/pull/1393))
- Use selenium and chromedriver as composer dev dependencies (I-Valchev, [#1387](https://github.com/bolt/core/pull/1387))

### 🎨 Design / User Interface

- Updates for skeleton theme (bobdenotter, [#1403](https://github.com/bolt/core/pull/1403))
- Working on Skeleton Theme (New.css instead of Sakura) (bobdenotter, [#1401](https://github.com/bolt/core/pull/1401))


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
