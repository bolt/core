Changelog
=========

## 4.1.1

Released: 2020-10-18

### 🐛 Bug fixes

- Any field that has a localized field down the line is considered localized (I-Valchev, [#1987](https://github.com/bolt/core/pull/1987))
-  Fix url for 'bulk' operations on Listing screen, for sites with modified Backend URL (bobdenotter, [#1983](https://github.com/bolt/core/pull/1983))
- Use `->loadEnv()` instead of `->load()` to support the 'standard' Symfony… (simongroenewolt, [#1974](https://github.com/bolt/core/pull/1974))
- Redirect to record or new record on singleton listing pages in backend (I-Valchev, [#1968](https://github.com/bolt/core/pull/1968))
- Don't break if slug is `null` as opposed to "empty string" (bobdenotter, [#2003](https://github.com/bolt/core/pull/2003))
- Updating the system font stack for 2020 (bobdenotter, [#2004](https://github.com/bolt/core/pull/2004))

### 🛠️ Miscellaneous

- Deprecate `default_state` for collection in favour of `variant` (I-Valchev, [#1988](https://github.com/bolt/core/pull/1988))
- Use DatabasePlatform instead of Driver to check for MySQL/MariaDB (andysh-uk, [#1984](https://github.com/bolt/core/pull/1984))
- Force path separator to `/` in ImageFixtures relative paths (luistar15, [#1979](https://github.com/bolt/core/pull/1979))
- `Log.php` had an incorrect `repositoryClass` specified in the Entity annotation (simongroenewolt, [#1972](https://github.com/bolt/core/pull/1972))
- Simplified Chinese translation (ziselive, [#1970](https://github.com/bolt/core/pull/1970))
- Missing HU labels (rixbeck, [#2005](https://github.com/bolt/core/pull/2005))
- Updating the system font stack for 2020 (bobdenotter, [#2004](https://github.com/bolt/core/pull/2004))

### ⚙️ Code Quality / Developer Experience

- Feat: add `.gitattributes` file for release (toofff, [#1980](https://github.com/bolt/core/pull/1980))
- A little less padding on lists in Cards (bobdenotter, [#1989](https://github.com/bolt/core/pull/1989))
- [CI] Add stylelint 4 space indent (TomasVotruba, [#1967](https://github.com/bolt/core/pull/1967))
- [CI] Move npm jobs from Travis to Github Actions (TomasVotruba, [#1966](https://github.com/bolt/core/pull/1966))


## 4.1.0

Released: 2020-10-08

### 📦 Additions and new features

- Initial PostgreSQL support (Wieter, [#1933](https://github.com/bolt/core/pull/1933))
- Feature/translations russian (denis-gorin, [#1962](https://github.com/bolt/core/pull/1962))
- Backend in Bulgarian / Панел на български 🇧🇬 (I-Valchev, [#1940](https://github.com/bolt/core/pull/1940))
- Add new `bolt:reset-password` CLI command to reset password (simongroenewolt, [#1937](https://github.com/bolt/core/pull/1937))
- Allow default values for Fields in new Content (bobdenotter, [#1952](https://github.com/bolt/core/pull/1952))

### 🐛 Bug fixes

- API: `$this->viewlessContentTypes` should be a indexed array, not associative (simongroenewolt, [#1960](https://github.com/bolt/core/pull/1960))
- Ignore empty like `%%` parameters (I-Valchev, [#1955](https://github.com/bolt/core/pull/1955))
- Access extensions by name with full namespace (I-Valchev, [#1954](https://github.com/bolt/core/pull/1954))
- Ensure the "Titles" in collection blocks are plain text only (bobdenotter, [#1948](https://github.com/bolt/core/pull/1948))
- Better way to allow `x` in thumbnail URLs alongside `×` (bobdenotter, [#1943](https://github.com/bolt/core/pull/1943))
- Ensure we have an array of records in "select" block (bobdenotter, [#1942](https://github.com/bolt/core/pull/1942))
- Add missing `json_decode` Twig Filter (bobdenotter, [#1941](https://github.com/bolt/core/pull/1941))
- Fix Buggy thumbnail url generation #1928 (luistar15, [#1938](https://github.com/bolt/core/pull/1938))
- Dont create folders if the image can't be created (UtechtDustin, [#1936](https://github.com/bolt/core/pull/1936))
- fix HTML DOM problems (LordSimal, [#1930](https://github.com/bolt/core/pull/1930))
- Exclude relations from `Content::toArray()` (I-Valchev, [#1927](https://github.com/bolt/core/pull/1927))

### 🛠️ Miscellaneous

- 🧹 Cleanup for `Doctrine\Version` [#1963](https://github.com/bolt/core/pull/1963))

### ⚙️ Code Quality / Developer Experience

- [phpstan] remove invalid tmp dir + dependency on symfony container (TomasVotruba, [#1958](https://github.com/bolt/core/pull/1958))
- [composer] decouple `post-install/update-cmd` and skip run on CI (TomasVotruba, [#1961](https://github.com/bolt/core/pull/1961))
- [composer] remove copy-paste leftovers (TomasVotruba, [#1959](https://github.com/bolt/core/pull/1959))
- [ci] use common path for `.env` file (TomasVotruba, [#1957](https://github.com/bolt/core/pull/1957))
- Delete `Version20200219064805.php` (bobdenotter, [#1946](https://github.com/bolt/core/pull/1946))
- Better feedback when `|related` and their ilk fail (bobdenotter, [#1935](https://github.com/bolt/core/pull/1935))
- Change travis-ci build status in `README.md` to travis-ci.com (LordSimal, [#1932](https://github.com/bolt/core/pull/1932))

## 4.0.1

Released: 2020-09-30

### 🐛 Bug fixes

- Fix: Don't break when passing in params for "Search Query" (bobdenotter, [#1922](https://github.com/bolt/core/pull/1922))
- Fix displaying (singleton) page by slug (denis-gorin, [#1921](https://github.com/bolt/core/pull/1921))
- Fix edit file in File management subfolders (denis-gorin, [#1915](https://github.com/bolt/core/pull/1915))
- Fix: add config media types to files list in dropdown (denis-gorin, [#1913](https://github.com/bolt/core/pull/1913))
- Empty image Field should return `null` instead of `files/` (bobdenotter, [#1925](https://github.com/bolt/core/pull/1925))

### 🛠️ Miscellaneous

- Try and resolve Extension classes from the DI container (rossriley, [#1909](https://github.com/bolt/core/pull/1909))
- Collection updates (eduardomart, [#1904](https://github.com/bolt/core/pull/1904))
- Enhancement: Add French Translations (BoutValentin, [#1903](https://github.com/bolt/core/pull/1903))
- Bolt is stable! (I-Valchev, [#1902](https://github.com/bolt/core/pull/1902))

### ⚙️ Code Quality / Developer Experience

- [CI] Move console checks from Travis to Github Actions (TomasVotruba, [#1917](https://github.com/bolt/core/pull/1917))
- [CI] Move PHPStan and Composer Validate from Travis to Github Actions (TomasVotruba, [#1916](https://github.com/bolt/core/pull/1916))
- [CI] Move ECS from Travis to Github Actions (TomasVotruba, [#1912](https://github.com/bolt/core/pull/1912))
- Cleanup, this is handled in ENV / Doctrine (bobdenotter, [#1908](https://github.com/bolt/core/pull/1908))

## Major release 4.0.0 

Released: 2020-09-24

### 📦 Additions and new features

- Allow numeric slugs with `allow_numeric` attribute in ContentType (bobdenotter, [#1884](https://github.com/bolt/core/pull/1884))

### 🐛 Bug fixes

- Only show "Preview" button if ContentType is not Viewless (bobdenotter, [#1896](https://github.com/bolt/core/pull/1896))
- Fix "zero width spaces" and Twigified titles in backend. (bobdenotter, [#1893](https://github.com/bolt/core/pull/1893))
- Fix Z-index, attempt 2 (bobdenotter, [#1890](https://github.com/bolt/core/pull/1890))
- Minor fix: Check if `Content` is set in preview mode. (bobdenotter, [#1885](https://github.com/bolt/core/pull/1885))
- Getting a date uses the default timezone from config (I-valchev, [#1901](https://github.com/bolt/core/pull/1901))

### 🛠️ Miscellaneous

- Update `_multiselect.scss` (eduardomart, [#1899](https://github.com/bolt/core/pull/1899))
- Fix `messages.de.xlf`: "Standart" to "Standard" (binarious, [#1887](https://github.com/bolt/core/pull/1887))
- Add pedantic notice to installation on this page, reminding people to use https://docs.bolt.cm/installation instead (bobdenotter, [#1886](https://github.com/bolt/core/pull/1886))
- Fix iffy details/summary marker  (bobdenotter, [#1882](https://github.com/bolt/core/pull/1882))
- Update `hidden.html.twig` (eduardomart, [#1880](https://github.com/bolt/core/pull/1880))



## 4.0.0-rc.44

Released: 2020-09-20

### 🐛 Bug fixes

- Don't show `Media created successfully` if it's not new (I-Valchev, [#1872](https://github.com/bolt/core/pull/1872))
- Edit attributes button does not break the page. (I-Valchev, [#1870](https://github.com/bolt/core/pull/1870))

### 🛠️ Miscellaneous

- Styling Collections more nice-like (bobdenotter, [#1876](https://github.com/bolt/core/pull/1876))
- Make z-index of `<select>` a bit higher (bobdenotter, [#1875](https://github.com/bolt/core/pull/1875))
- Allow uploading of Avif and Webp images, for the hip kids. (bobdenotter, [#1874](https://github.com/bolt/core/pull/1874))
- Adding `base-2021` to `CopyThemesCommand` (bobdenotter, [#1879](https://github.com/bolt/core/pull/1879))


## 4.0.0-rc.43

Released: 2020-09-16

### 🐛 Bug fixes

- Multiple collections buttons don't break on the same page (I-Valchev, [#1863](https://github.com/bolt/core/pull/1863))
- Slugs in fixtures use the `uses:` attribute to make sensible slugs (I-Valchev, [#1861](https://github.com/bolt/core/pull/1861))
- Allow multiple roles to be selected for users (I-Valchev, [#1856](https://github.com/bolt/core/pull/1856))
- Ensure config for `not_found` etc. are arrays. (and some cleanup of old, deprecrated stuff) (bobdenotter, [#1867](https://github.com/bolt/core/pull/1867))

### 📦 Additions and new features

- Make possible to check if an extension is present, directly in Twig (bobdenotter, [#1844](https://github.com/bolt/core/pull/1844))
- Configure homepage without a record or listing (I-Valchev, [#1866](https://github.com/bolt/core/pull/1866))

### 🛠️ Miscellaneous

- Don't break if editor tries editing a record with a missing definition (I-Valchev, [#1859](https://github.com/bolt/core/pull/1859))
- Update `upload_location` in GeneralParser.php, followup to #1850 (bobdenotter, [#1858](https://github.com/bolt/core/pull/1858))
- Fix default locations for image uploads (bobdenotter, [#1857](https://github.com/bolt/core/pull/1857))
- No dropdown for adding collection items if there's just one. (I-Valchev, [#1853](https://github.com/bolt/core/pull/1853))
- Update `config.yaml` (eduardomart, [#1850](https://github.com/bolt/core/pull/1850))
- Check if extension exists as a twig test (I-Valchev, [#1849](https://github.com/bolt/core/pull/1849))
- Make Frontend output a bit more robust by catching common pitfalls (non-existing content, etc) (bobdenotter, [#1847](https://github.com/bolt/core/pull/1847))
- Updating Fixtures for impending release (bobdenotter, [#1846](https://github.com/bolt/core/pull/1846))
- Add optional parameter to `excerpt` to wrap output in `<p>` (bobdenotter, [#1845](https://github.com/bolt/core/pull/1845))
- Release/4.0.0 rc.42 (bobdenotter, [#1843](https://github.com/bolt/core/pull/1843))

### ⚙️ Code Quality / Developer Experience

- Update about page with Symfony 5 (I-Valchev, [#1864](https://github.com/bolt/core/pull/1864))
- Fix phpstan break (again)! (I-Valchev, [#1862](https://github.com/bolt/core/pull/1862))
- Re-run behat tests on clean plate in case they fail mid-test (I-Valchev, [#1855](https://github.com/bolt/core/pull/1855))
- Re-enable `composer validate --strict` in CI (I-Valchev, [#1854](https://github.com/bolt/core/pull/1854))
- Fix ECS by explicitly skipping `UnaryOperatorSpacesFixer` (bobdenotter, [#1851](https://github.com/bolt/core/pull/1851))
- Allow up to 120 characters on a line of JS code (I-Valchev, [#1865](https://github.com/bolt/core/pull/1865))


## 4.0.0-rc.42

Released: 2020-09-11

### 🐛 Bug fixes

- Support taxonomies in `title_format` and `excerpt_format` (bobdenotter, [#1838](https://github.com/bolt/core/pull/1838))
- Removed faulty definition to show timeSelection in Date Picker (develth, [#1837](https://github.com/bolt/core/pull/1837))
- Imagelists and Filelists work in collections (I-Valchev, [#1835](https://github.com/bolt/core/pull/1835))
- Listing search uses correct config. Add `--like` option to query params search (I-Valchev, [#1829](https://github.com/bolt/core/pull/1829))
- Add pattern `url` and `email` options as shown in the docs (I-Valchev, [#1828](https://github.com/bolt/core/pull/1828))
- Prevent recursion in `title_format` (bobdenotter, [#1827](https://github.com/bolt/core/pull/1827))
- Clear menu caches on content delete. Log delete event. (I-Valchev, [#1824](https://github.com/bolt/core/pull/1824))
- Search without author returns results (I-Valchev, [#1823](https://github.com/bolt/core/pull/1823))
- Don't break if we're trying to get content for "no ContentTypes" (bobdenotter, [#1822](https://github.com/bolt/core/pull/1822))
- Allow src in iframes for embeds to work. Add spinner feedback while embed is loading. (I-Valchev, [#1820](https://github.com/bolt/core/pull/1820))
- Pass in `record` with Set partial (bobdenotter, [#1842](https://github.com/bolt/core/pull/1842))

### 📦 Additions and new features

- Use configured `upload_location` for images and files in ContentTypes (bobdenotter, [#1834](https://github.com/bolt/core/pull/1834))
- Collapsible collections (I-Valchev, [#1830](https://github.com/bolt/core/pull/1830))

### 🛠️ Miscellaneous

- Add `fixture_format` option to fields (I-Valchev, [#1841](https://github.com/bolt/core/pull/1841))
- Make `placeholders()` accept `null` for robustness (bobdenotter, [#1840](https://github.com/bolt/core/pull/1840))
- Deprecate use of Symfony 4 (I-Valchev, [#1839](https://github.com/bolt/core/pull/1839))
- Update top toolbar. Kill your darlings! (bobdenotter, [#1836](https://github.com/bolt/core/pull/1836))
- Update `_taxonomylinks.html.twig`: fix link (bobdenotter, [#1832](https://github.com/bolt/core/pull/1832))
- Listing singleton uses `record_route` setting (I-Valchev, [#1826](https://github.com/bolt/core/pull/1826))

### 🤖 Tests

- Use `travis_retry` command on frequently failing behat tests (I-Valchev, [#1831](https://github.com/bolt/core/pull/1831))
- Run tests in prod (I-Valchev, [#1804](https://github.com/bolt/core/pull/1804))


## 4.0.0-rc.41

Released: 2020-09-02

### 🐛 Bug fixes

- Don't list Singleton ContentTypes in Aside (bobdenotter, [#1814](https://github.com/bolt/core/pull/1814))
- Fall back to `DetailController` when trying to "list" a Singleton ContentType (bobdenotter, [#1813](https://github.com/bolt/core/pull/1813))
- Fix `Notice: Array to string conversion` in `Field.php` by ensuring  the  array is one level deep. (bobdenotter, [#1811](https://github.com/bolt/core/pull/1811))
- Make order filter work with `ContentHelper::get()` options (I-Valchev, [#1809](https://github.com/bolt/core/pull/1809))
- Minor fix: Set database path correctly (bobdenotter, [#1801](https://github.com/bolt/core/pull/1801))
- Fix breakage in PROD for `type: select` fields (bobdenotter, [#1800](https://github.com/bolt/core/pull/1800))
- shouldreturnsingle handles id filtering based on operator (I-Valchev, [#1799](https://github.com/bolt/core/pull/1799))
- Include taxonomy types if no taxonomies set (I-Valchev, [#1795](https://github.com/bolt/core/pull/1795))

### 🛠️ Miscellaneous

- Allow for more flexibility with `class: foo` in Text fields (bobdenotter, [#1810](https://github.com/bolt/core/pull/1810))
- Show appropriate `displayTitle` for new "Singleton" Record (bobdenotter, [#1807](https://github.com/bolt/core/pull/1807))
- Allow for more control over formatting of prefix and postfix (bobdenotter, [#1806](https://github.com/bolt/core/pull/1806))
- WIP: Czech localization (VentyCZ, [#1803](https://github.com/bolt/core/pull/1803))
- Version bump in `doctrine/common` broke this (bobdenotter, [#1802](https://github.com/bolt/core/pull/1802))
- Allow `contenttype` in title formatting (For "Select" fields) (bobdenotter, [#1797](https://github.com/bolt/core/pull/1797))
- Remove `max-width` from prefix and postfix (eduardomart, [#1796](https://github.com/bolt/core/pull/1796))

### ⚙️ Code Quality / Developer Experience

- Update `UnderscoreNamingStrategy` in `doctrine.yaml` (bobdenotter, [#1812](https://github.com/bolt/core/pull/1812))
- Fix deprecation in RouteCollectionBuilder (bobdenotter, [#1815](https://github.com/bolt/core/pull/1815))


## 4.0.0-rc.40

Released: 2020-08-31

### 🐛 Bug fixes

- Fix widgets: Only setRendered if widget didn't return `null`  (bobdenotter, [#1791](https://github.com/bolt/core/pull/1791))
- Avoid caching slug uses definition for new records (I-Valchev, [#1786](https://github.com/bolt/core/pull/1786))

### ⚙️ Code Quality / Developer Experience

- Updating dependencies, Fix CS findings (bobdenotter, [#1790](https://github.com/bolt/core/pull/1790))


## 4.0.0-rc.39

Released: 2020-08-26

### 🐛 Bug fixes

- Get curl_options as an array instead of collection (bobdenotter, [#1781](https://github.com/bolt/core/pull/1781))
- Numeric order works on MySQL databases (I-Valchev, [#1778](https://github.com/bolt/core/pull/1778))
- Localehelper shouldn't break on CLI commands (bobdenotter, [#1777](https://github.com/bolt/core/pull/1777))
- Better check for `INSTR` and `CAST` (bobdenotter, [#1776](https://github.com/bolt/core/pull/1776))
- Fix orderby non-localizable field in localized contenttype (I-Valchev, [#1774](https://github.com/bolt/core/pull/1774))
- Unsplash images fixtures work even if ssl verification fails (I-Valchev, [#1771](https://github.com/bolt/core/pull/1771))
- For now, only use setcontent with standalone fields (I-Valchev, [#1770](https://github.com/bolt/core/pull/1770))
- Date field required updates validity when set (I-Valchev, [#1768](https://github.com/bolt/core/pull/1768))
- Fix: Correctly exclude fields that are used in the slug by default (bobdenotter, [#1766](https://github.com/bolt/core/pull/1766))
- Fall back to "dumb" numeric sorting for older SQLite versions (bobdenotter, [#1765](https://github.com/bolt/core/pull/1765))

### 📦 Additions and new features

- Filter and order using queryparams on listing pages (I-Valchev, [#1782](https://github.com/bolt/core/pull/1782))

### 🛠️ Miscellaneous

- Enhancement/tidying up and removing old cruft  (I-Valchev, [#1775](https://github.com/bolt/core/pull/1775))
- `localdate`prefers currentlocale over defaultlocale (I-Valchev, [#1763](https://github.com/bolt/core/pull/1763))

### 🤖 Tests

- Test/db supports json (I-Valchev, [#1773](https://github.com/bolt/core/pull/1773))

### ⚙️ Code Quality / Developer Experience

- Update `config.yaml` with documentation on curl options. (I-Valchev, [#1779](https://github.com/bolt/core/pull/1779))
- It has been   ̶5̶ 0 days since ECS last broke Travis (bobdenotter, [#1767](https://github.com/bolt/core/pull/1767))

## 4.0.0-rc.37

Released: 2020-08-24

### 🐛 Bug fixes

- Allow collections to include extension fields (I-Valchev, [#1761](https://github.com/bolt/core/pull/1761))
- Bugfix/support db without json (I-Valchev, [#1760](https://github.com/bolt/core/pull/1760))
- Prevent breakage when trying to excerpt a Field (bobdenotter, [#1759](https://github.com/bolt/core/pull/1759))
- Make "required" for Date fields work (bobdenotter, [#1747](https://github.com/bolt/core/pull/1747))
- Add "Number" and "Date" fields (bobdenotter, [#1742](https://github.com/bolt/core/pull/1742))
- Make multivalue filter with content fields (I-Valchev, [#1733](https://github.com/bolt/core/pull/1733))
- Remove services before `composer remove` (I-Valchev, [#1726](https://github.com/bolt/core/pull/1726))

### 📦 Additions and new features

- New `page` directive for setcontent to override the current page setting (I-Valchev, [#1738](https://github.com/bolt/core/pull/1738))
- Current filter works for locale (I-Valchev, [#1737](https://github.com/bolt/core/pull/1737))
- Add `url_decode` Twig Filter (bobdenotter, [#1732](https://github.com/bolt/core/pull/1732))

### 🛠️ Miscellaneous

- Prepare release 4.0.0-rc.36 (bobdenotter, [#1752](https://github.com/bolt/core/pull/1752))
- Tweaking default Skeleton theme (bobdenotter, [#1751](https://github.com/bolt/core/pull/1751))
- Remove the hover on the left-hand side logo (bobdenotter, [#1750](https://github.com/bolt/core/pull/1750))
- Make relative dates work for fields that have an underscore in their name (bobdenotter, [#1748](https://github.com/bolt/core/pull/1748))
- Clean up and refactor `SelectQuery` (I-Valchev, [#1744](https://github.com/bolt/core/pull/1744))
- Update contenttypes.yaml comments documentation (I-Valchev, [#1741](https://github.com/bolt/core/pull/1741))
- modification to SelectQuery.php related to #1619 (JTNMW, [#1739](https://github.com/bolt/core/pull/1739))
- Remove `JSON_CONTAINS` from `doctrine.yaml` (bobdenotter, [#1736](https://github.com/bolt/core/pull/1736))
- Rename `renderTemplate` to render for consistency with `AbstractController` (bobdenotter, [#1735](https://github.com/bolt/core/pull/1735))
- Set filter in "Templateselect" to a regex by default (bobdenotter, [#1730](https://github.com/bolt/core/pull/1730))
- add Slug field UTF-8 and Transliteration Support (denis-gorin, [#1723](https://github.com/bolt/core/pull/1723))
- Use translated bootbox (nestordedios, [#1720](https://github.com/bolt/core/pull/1720))

### ⚙️ Code Quality / Developer Experience

- Add "Article" and "Redactor" to contentfixtures (bobdenotter, [#1756](https://github.com/bolt/core/pull/1756))
- Fix ECS 😅 (bobdenotter, [#1731](https://github.com/bolt/core/pull/1731))


## 4.0.0-rc.35

Released: 2020-08-14

### 🐛 Bug fixes

- Output Fields correctly, when using the Fields Block (bobdenotter, [#1709](https://github.com/bolt/core/pull/1709))
- Don't let Vue parse Twig tags when editing content (bobdenotter, [#1708](https://github.com/bolt/core/pull/1708))
- Improve Sanitiser. Sanitise fields on save. (I-Valchev, [#1699](https://github.com/bolt/core/pull/1699))

### 🛠️ Miscellaneous

- Replaced hardcoded `/` by `DIRECTORY_SEPARATOR` (colorando-de, [#1716](https://github.com/bolt/core/pull/1716))
- Merge addition of Spanish (🇪🇸) translations into master (nestordedios, [#1714](https://github.com/bolt/core/pull/1714))
- Fix 'custom' Fields showing up with their Label, if `_fields.twig` is used. (bobdenotter, [#1707](https://github.com/bolt/core/pull/1707))
- Pager uses default listing records, if available. (I-Valchev, [#1705](https://github.com/bolt/core/pull/1705))
- Allow for `variant: inline` for most fields (bobdenotter, [#1704](https://github.com/bolt/core/pull/1704))
- Allow Fields added in extensions to show up in Sets and Collections (bobdenotter, [#1703](https://github.com/bolt/core/pull/1703))
- Refactor relations to be properly bidirectional (I-Valchev, [#1702](https://github.com/bolt/core/pull/1702))
- Use embed without `raw` filter (I-Valchev, [#1701](https://github.com/bolt/core/pull/1701))
- Enforce unique slugs (I-Valchev, [#1710](https://github.com/bolt/core/pull/1710))

### ⚙️ Code Quality / Developer Experience

- Cleanup for "Should Field be rendered as Twig?" (bobdenotter, [#1711](https://github.com/bolt/core/pull/1711))


## 4.0.0-rc.34

Released: 2020-08-09

### 🐛 Bug fixes

- Thumbnailing works with only width or only height (I-Valchev, [#1692](https://github.com/bolt/core/pull/1692))
- Localize submenu buttons (I-Valchev, [#1689](https://github.com/bolt/core/pull/1689))
- Fix menu `item|current` in _menu.html.twig (I-Valchev, [#1688](https://github.com/bolt/core/pull/1688))
- Do _not_ use taxonomy values. Use them as defined (I-Valchev, [#1683](https://github.com/bolt/core/pull/1683))
- Prettify thumbnail paths. Use Bolt 4 cropping options (I-Valchev, [#1679](https://github.com/bolt/core/pull/1679))
- Child fields can render html by default, without `|raw` filter (I-Valchev, [#1673](https://github.com/bolt/core/pull/1673))

### 📦 Additions and new features

- Feature/orderby taxonomies (I-Valchev, [#1694](https://github.com/bolt/core/pull/1694))
- Allow textarea `height` option (I-Valchev, [#1691](https://github.com/bolt/core/pull/1691))
- Show detailed localization for fields containing subfields (I-Valchev, [#1685](https://github.com/bolt/core/pull/1685))

### 🛠️ Miscellaneous

- Optimise `selected` filter to fetch records with a single DB call (I-Valchev, [#1695](https://github.com/bolt/core/pull/1695))
- Set user status, confirm user delete, select scalar value (I-Valchev, [#1686](https://github.com/bolt/core/pull/1686))
- Merge move concatenated 'website' string to translation file into master (nestordedios, [#1678](https://github.com/bolt/core/pull/1678))
- Deprecate `translated` filter in favour of `translate` (I-Valchev, [#1677](https://github.com/bolt/core/pull/1677))

### ⚙️ Code Quality / Developer Experience

- Fix missing files from `.gitignore` (bobdenotter, [#1682](https://github.com/bolt/core/pull/1682))
- Some minor code improvements (I-Valchev, [#1674](https://github.com/bolt/core/pull/1674))


## 4.0.0-rc.33

Released: 2020-08-01

### 🐛 Bug fixes

- Fix set field being localizable (I-Valchev, [#1662](https://github.com/bolt/core/pull/1662))
- Use `current` filter to check if menu item is this page (I-Valchev, [#1659](https://github.com/bolt/core/pull/1659))
- Make Sanitiser obey allowed tags and attributes from `config.yaml` (bobdenotter, [#1648](https://github.com/bolt/core/pull/1648))
- Pass on Query parameters to subrequest from Homepage (bobdenotter, [#1645](https://github.com/bolt/core/pull/1645))
- Don't output common Fields twice (in "block output helper") [#1671](https://github.com/bolt/core/pull/1671))

### 📦 Additions and new features

- `svg` filter outputs raw svg file (I-Valchev, [#1661](https://github.com/bolt/core/pull/1661))
- Allow extensions to have an `install` method, which is called on installation (to install assets, for example) (bobdenotter, [#1656](https://github.com/bolt/core/pull/1656))
- Allow for extensions to add new FieldTypes (bobdenotter, [#1649](https://github.com/bolt/core/pull/1649))
- Allow for custom `500 Internal Server Error` pages. (bobdenotter, [#1647](https://github.com/bolt/core/pull/1647))

### 🛠️ Miscellaneous

- Make `getFieldClassname` public so extensions can check if a Field exists (bobdenotter, [#1669](https://github.com/bolt/core/pull/1669))
- Add `image.extension` value (I-Valchev, [#1668](https://github.com/bolt/core/pull/1668))
- `FieldRepository::factory` can instantiate Extension fields (I-Valchev, [#1667](https://github.com/bolt/core/pull/1667))
- Allow auto-generated extension services to be overridden (I-Valchev, [#1666](https://github.com/bolt/core/pull/1666))
- added various German translations (ymarkus, [#1657](https://github.com/bolt/core/pull/1657))
- Update composer.json: Add `"public-dir": "public"` (bobdenotter, [#1650](https://github.com/bolt/core/pull/1650))

### ⚙️ Code Quality / Developer Experience

- Bump elliptic from 6.5.2 to 6.5.3 (dependabot[bot], [#1670](https://github.com/bolt/core/pull/1670))
- Update `.htaccess`, according to SF 5 defaults (bobdenotter, [#1646](https://github.com/bolt/core/pull/1646))


## 4.0.0-rc.32

Released: 2020-07-22

### 🐛 Bug fixes

- Fix Errorcontroller adding Exception when it shouldn't. (bobdenotter, [#1643](https://github.com/bolt/core/pull/1643))
- Make sure user default user status is set when instantiating entities from code (I-Valchev, [#1641](https://github.com/bolt/core/pull/1641))
- We were a bit overzealous in removing the compilerpass. Turns out we _do_ need `packages/bolt.yaml` (bobdenotter, [#1639](https://github.com/bolt/core/pull/1639))
- Saving an edited file should keep input as-is (bobdenotter, [#1637](https://github.com/bolt/core/pull/1637))
- Shuffle paginated records (I-Valchev, [#1633](https://github.com/bolt/core/pull/1633))
- Fix `setcontent` random directive to override anything else set by the OrderDirective (I-Valchev, [#1630](https://github.com/bolt/core/pull/1630))
- Make empty `multiselect` tag invisible (I-Valchev, [#1629](https://github.com/bolt/core/pull/1629))

### 📦 Additions and new features

- Add support for "403 Forbidden" pages (bobdenotter, [#1635](https://github.com/bolt/core/pull/1635))

### 🛠️ Miscellaneous

- Copy extension services and routes into Bolt (bobdenotter, [#1634](https://github.com/bolt/core/pull/1634))
- Richer content format option (I-Valchev, [#1628](https://github.com/bolt/core/pull/1628))
- Users extension prep (I-Valchev, [#1618](https://github.com/bolt/core/pull/1618))

### ⚙️ Code Quality / Developer Experience

- Fix ECS and update config with sets (TomasVotruba, [#1636](https://github.com/bolt/core/pull/1636))

## 4.0.0-rc.30 and 4.0.0-rc.31

Scrapped, due to [#1639](https://github.com/bolt/core/pull/1639) and followup shenanigans.

## 4.0.0-rc.29

Released: 2020-07-11

### 🐛 Bug fixes

- Allow optional space in `QueryParameterParser` between operand and keyword (bobdenotter, [#1621](https://github.com/bolt/core/pull/1621))
- Allow order by `number` field correctly  (I-Valchev, [#1616](https://github.com/bolt/core/pull/1616))
- Put `localedatetime` with previous format and deprecated notice. Use `localdate` with new format (I-Valchev, [#1612](https://github.com/bolt/core/pull/1612))

### 📦 Additions and new features

- Add `getuser` with alias `user` Twig function (I-Valchev, [#1611](https://github.com/bolt/core/pull/1611))
- Add `random` directive to `setcontent` tag (I-Valchev, [#1606](https://github.com/bolt/core/pull/1606))

### 🛠️ Miscellaneous

- Make the user-friendly notifications a bit more robust (bobdenotter, [#1613](https://github.com/bolt/core/pull/1613))
- Optimize queries for taxonomies, following #1541 (JTNMW, [#1619](https://github.com/bolt/core/pull/1619))

## 4.0.0-rc.28

Released: 2020-07-04

### 🐛 Bug fixes

- `setcontent` returns single for contenttype/id (I-Valchev, [#1595](https://github.com/bolt/core/pull/1595))
- Make sure form validation works with `data-patience` buttons (I-Valchev, [#1594](https://github.com/bolt/core/pull/1594))
- Use humanized taxonomy name when creating new taxonomies (I-Valchev, [#1591](https://github.com/bolt/core/pull/1591))
- Don't break line on date field with valueonly (I-Valchev, [#1582](https://github.com/bolt/core/pull/1582))

### 📦 Additions and new features

- Allow "focus" parameter for excerpts of plain strings (bobdenotter, [#1602](https://github.com/bolt/core/pull/1602))
- Relation factory (I-Valchev, [#1597](https://github.com/bolt/core/pull/1597))
- Allow configurable `date_format` in config.yaml (I-Valchev, [#1593](https://github.com/bolt/core/pull/1593))
- Paginate users listing (I-Valchev, [#1580](https://github.com/bolt/core/pull/1580))

### 🛠️ Miscellaneous

- Refactor `setcontent` handles and directives (I-Valchev, [#1599](https://github.com/bolt/core/pull/1599))
- Use `taxonomy|link` filter to get taxonomy links (I-Valchev, [#1592](https://github.com/bolt/core/pull/1592))

### ⚙️ Code Quality / Developer Experience

- Chore: Updating NPM dependencies (bobdenotter, [#1601](https://github.com/bolt/core/pull/1601))
- Sorry GitHub Actions 😢 you are breaking too often (I-Valchev, [#1598](https://github.com/bolt/core/pull/1598))
- Display Symfony version in "About" and `bolt:info` screens (bobdenotter, [#1586](https://github.com/bolt/core/pull/1586))
- Allow both Symfony 4.4 and 5.1 for now (bobdenotter, [#1579](https://github.com/bolt/core/pull/1579))

## 4.0.0-rc.27

Released: 2020-06-29

### 🐛 Bug fixes

- Slug regenerates on duplicate (I-Valchev, [#1574](https://github.com/bolt/core/pull/1574))
- Include proper css for file uploader (I-Valchev, [#1572](https://github.com/bolt/core/pull/1572))
- Ensure fields inside sets and collections have their correct definition (I-Valchev, [#1571](https://github.com/bolt/core/pull/1571))
- Cache clear does not loop in Config (I-Valchev, [#1570](https://github.com/bolt/core/pull/1570))
- Add defaultlocale to duplicate action twig (I-Valchev, [#1569](https://github.com/bolt/core/pull/1569))
- Singletons return single Content result with `{% setcontent %}` (bobdenotter, [#1565](https://github.com/bolt/core/pull/1565))
- Fix issue with undefined index 0 for a select field inside a a set (I-Valchev, [#1562](https://github.com/bolt/core/pull/1562))
- Allow "Homepage" to be in viewless ContentType (bobdenotter, [#1549](https://github.com/bolt/core/pull/1549))
- Make sure contenttypes routes are updated after changes to contenttypes.yaml (I-Valchev, [#1548](https://github.com/bolt/core/pull/1548))
- After adding `symfony/proxy-manager-bridge`, table prefix went missing (bobdenotter, [#1545](https://github.com/bolt/core/pull/1545))
- Ensure `$this->fields` is initialised (For Fields that haven't been accessed before) (bobdenotter, [#1544](https://github.com/bolt/core/pull/1544))
- Slugs follow `localize` setting (I-Valchev, [#1543](https://github.com/bolt/core/pull/1543))
- Re-set `setSingleFetchMode` to ensure returning a pager, if needed (bobdenotter, [#1576](https://github.com/bolt/core/pull/1576))

### 📦 Additions and new features

- Save content on <kbd>ctrl+s</kbd> and <kbd>cmd+s</kbd> (I-Valchev, [#1573](https://github.com/bolt/core/pull/1573))
- Update to Symfony 5.1 🎉🎊 (bobdenotter, [#1546](https://github.com/bolt/core/pull/1546))

### 🛠️ Miscellaneous

- Better UI for dragging in multiselect (I-Valchev, [#1550](https://github.com/bolt/core/pull/1550))
- Prepare release 4.0.0-rc.26 (bobdenotter, [#1539](https://github.com/bolt/core/pull/1539))

### 🤖 Tests

- GitHub Actions workflow (I-Valchev, [#1575](https://github.com/bolt/core/pull/1575))

### ⚙️ Code Quality / Developer Experience

- Better feedback on bolt:setup errors (I-Valchev, [#1551](https://github.com/bolt/core/pull/1551))
- Allow both Symfony 4.4 and 5.1 for now (bobdenotter, [#1578](https://github.com/bolt/core/pull/1578))

## 4.0.0-rc.26

Released: 2020-06-22

### 🐛 Bug fixes

- Use currently rendered locale in OrderDirective (I-Valchev, [#1529](https://github.com/bolt/core/pull/1529))
- Ensure `$boltConfig` is set for Controllers in extensions (bobdenotter, [#1527](https://github.com/bolt/core/pull/1527))
- Don't "warm up" cache on `composer update`, to save time and prevent dreaded `ProcessTimedOutException` after 10 seconds.. (bobdenotter, [#1526](https://github.com/bolt/core/pull/1526))
- Datefield uses global `app` to access user locale (I-Valchev, [#1521](https://github.com/bolt/core/pull/1521))
- Make sure sets inside collections are instantiated with actual values (I-Valchev, [#1520](https://github.com/bolt/core/pull/1520))

### 🛠️ Miscellaneous

- User edit actions require valid csrf tokens (I-Valchev, [#1532](https://github.com/bolt/core/pull/1532))
- Warn editors about file upload errors (I-Valchev, [#1531](https://github.com/bolt/core/pull/1531))
- Make sure `currentlocale` on edit is always set (I-Valchev, [#1530](https://github.com/bolt/core/pull/1530))
- Make canonical record URL consistent across routes (I-Valchev, [#1511](https://github.com/bolt/core/pull/1511))

### 🤖 Tests

- Localization tests for saved changes in different locales (I-Valchev, [#1516](https://github.com/bolt/core/pull/1516))
- More integration tests for localized content (I-Valchev, [#1509](https://github.com/bolt/core/pull/1509))

## 4.0.0-rc.25

Released: 2020-06-18

### 📦 Additions and new features

- Add Global search to backend (bobdenotter, [#1498](https://github.com/bolt/core/pull/1498))
- Add ability to override routing with record_route (I-Valchev, [#1484](https://github.com/bolt/core/pull/1484))
- Sort multiselect fields with drag and drop (I-Valchev, [#1481](https://github.com/bolt/core/pull/1481))

### 🐛 Bug fixes

- Fix current locale to be correct (I-Valchev, [#1499](https://github.com/bolt/core/pull/1499))
- Fix: Don't show spinners on invalid form submissions (bobdenotter, [#1497](https://github.com/bolt/core/pull/1497))
- Better check for maximum filesize (bobdenotter, [#1495](https://github.com/bolt/core/pull/1495))
- Fix Imagelist alt 'true' issue. Clean up Vue components (I-Valchev, [#1494](https://github.com/bolt/core/pull/1494))
- Include homepage record in Twig globals (I-Valchev, [#1491](https://github.com/bolt/core/pull/1491))
- Don't break on missing Collection Fields (bobdenotter, [#1490](https://github.com/bolt/core/pull/1490))
- Make Collection Field properly Iterable (bobdenotter, [#1485](https://github.com/bolt/core/pull/1485))
- Fix capitalisation in groups (bobdenotter, [#1479](https://github.com/bolt/core/pull/1479))
- Display the Set field correctly in the Editor when new field is added after saving record (I-Valchev, [#1471](https://github.com/bolt/core/pull/1471))
- Fix excerpt length (bobdenotter, [#1469](https://github.com/bolt/core/pull/1469))
- Update `getContentTypeName()`, add `getContentTypeSingularName()` (bobdenotter, [#1468](https://github.com/bolt/core/pull/1468))
- Placeholder in Image Field is not clickable if empty (bobdenotter, [#1505](https://github.com/bolt/core/pull/1505))

### 🛠️ Miscellaneous

- Hide one of two groups of record action buttons on mobile (I-Valchev, [#1501](https://github.com/bolt/core/pull/1501))
- Fixing some more deprecations (bobdenotter, [#1478](https://github.com/bolt/core/pull/1478))
- Use new Symfony Error Controller, instead of Twig's old one. (bobdenotter, [#1477](https://github.com/bolt/core/pull/1477))
- Fixing some deprecations (bobdenotter, [#1475](https://github.com/bolt/core/pull/1475))
- Two more deprecations fixed! (bobdenotter, [#1480](https://github.com/bolt/core/pull/1480))

### 🤖 Tests

- Tests/twig (I-Valchev, [#1502](https://github.com/bolt/core/pull/1502))

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
