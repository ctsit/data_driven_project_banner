# Change Log
All notable changes to the REDCap Data Driven Project Banner will be documented in this file.


## [2.0.3] - 2023-12-20
### Changed
- Check for definition of PROJECT_ID if used in piping to prevent crash (@ChemiKyle, #25, #26)
- Replace db_query with EM framework's parameterized query Bump min REDCap version to reflect framework 14 requirement (@ChemiKyle, #25, #26)
- Update config_menu.js injection to execute in redcap_every_page_top Stop using redcap_module_configure_button_display due to framework changes causing it to throw errors Update framework version to 14 (@ChemiKyle, #25, #26)


## [2.0.2] - 2021-06-11
### Changed
- Use framework-version 5 and set minimum REDCap version to 10.0.5 (Philip Chase)


## [2.0.1] - 2021-06-11
### Changed
- Update config_menu.js to avoid clash with other modules overloading resetConfigInstances (Kyle Chesney)


## [2.0.0] - 2021-06-08
### Added
- Add support for custom SQL queries for data (Kyle Chesney)
- Add support for multi row data responses, surrounding rich text fields (Kyle Chesney)
- Add display criteria with custom SQL support (Kyle Chesney)

### Changed
- Remove prebuilt sql dropdown entirely, breaking this functionality (Kyle Chesney)


## [1.1.0] - 2019-11-18
### Added
- Add option to display banner on all pages of a project (Kyle Chesney)

### Changed
- Place banner at the top of the page to avoid obfuscating other text (Kyle Chesney)


## [1.0.1] - 2019-11-14
### Added
- Include option to select color/border of banner. (Kyle Chesney)

### Changed
 - Change name for banner_text in JS to avoid clashes with other banners. (Kyle Chesney)


## [1.0.0] - 2019-06-24
### Summary
 - This is the first release of REDCap Data Driven Project Banner. Behold!
