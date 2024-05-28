# Changelog
All notable changes to **MailArchiver** are documented in this *changelog*.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and **MailArchiver** adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.0.0] - 2024-05-28

### Added
- [BC] To enable installation on more heterogeneous platforms, the plugin now adapts its internal logging mode to already loaded libraries.

### Changed
- Updated DecaLog SDK from version 4.1.0 to version 5.0.0.

### Fixed
- PHP error with some plugins like Woocommerce Paypal Payments.

## [3.0.2] - 2024-05-08

### Fixed
- There's some issues while pushing on wp.org repository.

## [3.0.1] - 2024-05-08

### Fixed
- There's some typos in the admin UI.
- All translations are not available.

## [3.0.0] - 2024-05-08

### Added
- Full compatibility with Elasticsearch 8.

### Changed
- [BC] Updated DecaLog SDK from version 3.0.0 to version 4.2.0.
- Upgraded Monolog library from version 2.9.1 to version 2.9.3.
- Upgraded GuzzleHttp library from version 7.2.0 to version 7.8.1.
- Upgraded Elasticsearch library from version 7.6.1 to version 8.13.0.
- Minimal required WordPress version is now 6.2.

## [2.14.0] - 2024-03-26

### Added
- New archiver to store archives in Hosterra Email.

### Fixed
- There's some typos in the admin UI.

## [2.13.0] - 2024-03-02

### Added
- Compatibility with WordPress 6.5.

### Changed
- Minimal required WordPress version is now 6.1.
- Minimal required PHP version is now 8.1.

## [2.12.0] - 2023-10-25

### Added
- Compatibility with WordPress 6.4.

## [2.11.0] - 2023-07-12

### Added
- Compatibility with WordPress 6.3.
- New option in archivers for proactive protection against XSS vulnerabilities.

### Changed
- Improved view for plain text email bodies.
- Improved compatibility with Contact Forms plugin.
- The color for `shmop` test in Site Health is now gray to not worry to much about it (was previously orange).

### Fixed
- [SEC004] XSS vulnerability / [CVE-2023-3136](https://www.cve.org/CVERecord?id=CVE-2023-3136) (thanks to [Alex Thomas](https://www.wordfence.com/threat-intel/vulnerabilities/researchers/alex-thomas) from [Wordfence](https://www.wordfence.com)).

## [2.10.1] - 2023-03-02

### Fixed
- [SEC003] CSRF vulnerability / [CVE-2023-27444](https://www.cve.org/CVERecord?id=CVE-2023-27444) (thanks to [Mika](https://patchstack.com/database/researcher/5ade6efe-f495-4836-906d-3de30c24edad) from [Patchstack](https://patchstack.com)).

## [2.10.0] - 2023-02-24

The developments of PerfOps One suite, of which this plugin is a part, is now sponsored by [Hosterra](https://hosterra.eu).

Hosterra is a web hosting company I founded in late 2022 whose purpose is to propose web services operating in a European data center that is water and energy efficient and ensures a first step towards GDPR compliance.

This sponsoring is a way to keep PerfOps One plugins suite free, open source and independent.

### Added
- Compatibility with WordPress 6.2.

### Changed
- Upgraded Monolog library from version 2.8.0 to version 2.9.1.
- Improved loading by removing unneeded jQuery references in public rendering (thanks to [Kishorchand](https://github.com/Kishorchandth)).

### Fixed
- In some edge-cases, detecting IP may produce PHP deprecation warnings (thanks to [YR Chen](https://github.com/stevapple)).

## [2.9.0] - 2022-10-06

### Added
- Compatibility with WordPress 6.1.
- [WPCLI] The results of `wp m-archive` commands are now logged in [DecaLog](https://wordpress.org/plugins/decalog/).

### Changed
- Upgraded Monolog library from version 2.5.0 to version 2.8.0.
- [WPCLI] The results of `wp m-archive` commands are now prefixed by the product name.

## [2.8.0] - 2022-04-21

### Added
- Compatibility with WordPress 6.0.
- New icons in archivers list to show activated privacy options.

### Changed
- Site Health page now presents a much more realistic test about object caching.
- Improved favicon handling for new Google API specifications.
- Updated DecaLog SDK from version 2.0.2 to version 3.0.0.
- Upgraded Monolog library from version 2.3.4 to version 2.5.0.

## [2.7.1] - 2022-01-17

### Fixed
- The Site Health page may launch deprecated tests.

## [2.7.0] - 2022-01-17

### Added
- Compatibility with PHP 8.1.

### Changed
- Updated DecaLog SDK from version 2.0.0 to version 2.0.2.
- Updated PerfOps One library from 2.2.1 to 2.2.2.
- Refactored cache mechanisms to fully support Redis and Memcached.
- Improved bubbles display when width is less than 500px (thanks to [Pat Ol](https://profiles.wordpress.org/pasglop/)).

### Fixed
- Object caching method may be wrongly detected in Site Health status (thanks to [freshuk](https://profiles.wordpress.org/freshuk/)).
- The console menu may display an empty screen (thanks to [Renaud Pacouil](https://www.laboiteare.fr)).
- There may be name collisions with internal APCu cache.

## [2.6.0] - 2021-12-07

### Added
- Compatibility with WordPress 5.9.
- New button in settings to install recommended plugins.
- The available hooks (filters and actions) are now described in `HOOKS.md` file.

### Changed
- Improved update process on high-traffic sites to avoid concurrent resources accesses.
- Better publishing frequency for metrics.
- Updated labels and links in plugins page.
- Updated the `README.md` file.

### Fixed
- Content type parsing may produce PHP errors in some cases.
- There's some typos in the admin UI.
- [SEC002] The password for some services are in plain text in "Site Health Info" page.
- Country translation with i18n module may be wrong.
- There's typos in `CHANGELOG.md`.

## [2.5.0] - 2021-09-29

### Added
- A new "operation mode" allows to not send emails but archive them as if they had been sent - see settings (thanks to [Loïc Antignac](https://github.com/webaxones) and [Xuan Nguyen](https://profiles.wordpress.org/xuxufr/) for the suggestion).
- New archiver to store individually each email as **json** or **eml** file, on server filesystem (local hard-drive or mounted storage).
- New archiver to forward each mail to a specified email address.
- New archiver to store archives in GMail.
- New archiver to store archives in Gandi Mail.
- New archiver to store archives in OVH (thanks to [Frank Bergère](https://profiles.wordpress.org/frank4/)).
- New archiver to store archives in Outlook.Com.
- New archiver to store archives in an Imap server.
- New archiver to store archives in an Elasticsearch instance.
- New archiver to store archives in Elastic Cloud.
- New archiver to store archives in a Loki instance.
- New archiver to store archives in Grafana Cloud service.
- New archiver to test and preview mails with Litmus service.
- New archiver to test and preview mails with Email on Acid service.
- New archiver to test mails with MailerCheck service.
- Privacy option for loggers allows now to encrypt the mail body.
- [WP-CLI] The new command `decrypt` allows to decrypt a mail body previously encrypted by MailArchiver.

### Changed
- MailArchiver now propagates `traceID` and `sessionID` for all archiver supporting it.
- The `traceID` field is now the trace ID generated by DecaLog if it's installed - it allows to "join" traces, events and mail archives.
- Improved internal IP detection: support for cloud load balancers.
- Improved compatibility with "Post SMTP Mailer" plugin.
- Improved hash handling and reporting for users and IPs.
- The archived metadata contains now a message "Mail sent." when sending is not in error.
- [WP-CLI] The command `status` now displays encryption details.
- Upgraded Monolog library from version 2.2.0 to version 2.3.4.

### Fixed
- System archivers are visible from the archiver selector.
- The remote IP can be wrongly detected when in AWS or GCP environments.
- Name collisions or PHP errors can occur with plugins using outdated versions of Monolog library (like BackWPup).

## [2.4.0] - 2021-09-07

### Added
- It's now possible to hide the main PerfOps One menu via the `poo_hide_main_menu` filter or each submenu via the `poo_hide_analytics_menu`, `poo_hide_consoles_menu`, `poo_hide_insights_menu`, `poo_hide_tools_menu`, `poo_hide_records_menu` and `poo_hide_settings_menu` filters (thanks to [Jan Thiel](https://github.com/JanThiel)).

### Changed
- Updated DecaLog SDK from version 1.2.0 to version 2.0.0.
- Designed has been improved for the archiver selector.

### Fixed
- There may be name collisions for some functions if version of WordPress is lower than 5.6.
- The main PerfOps One menu is not hidden when it doesn't contain any items (thanks to [Jan Thiel](https://github.com/JanThiel)).
- In some very special conditions, the plugin may be in the default site language rather than the user's language.
- The PerfOps One menu builder is not compatible with Admin Menu Editor plugin (thanks to [dvokoun](https://wordpress.org/support/users/dvokoun/)).

## [2.3.1] - 2021-08-11

### Changed
- New redesigned UI for PerfOps One plugins management and menus (thanks to [Loïc Antignac](https://github.com/webaxones), [Paul Bonaldi](https://profiles.wordpress.org/bonaldi/), [Axel Ducoron](https://github.com/aksld), [Laurent Millet](https://profiles.wordpress.org/wplmillet/), [Samy Rabih](https://github.com/samy) and [Raphaël Riehl](https://github.com/raphaelriehl) for their invaluable help).

### Fixed
- In some conditions, the plugin may be in the default site language rather than the user's language.
- With WordPress 5.8 it is impossible to move and close boxes.

## [2.3.0] - 2021-06-22

### Added
- Compatibility with WordPress 5.8.
- Integration with DecaLog SDK.
- Traces publication.

### Changed
- Redesigned archivers list.
- [WP-CLI] `m-archive status` command now displays DecaLog SDK version too.

## [2.2.0] - 2021-02-24

### Added
- Compatibility with WordPress 5.7.
- New setting to override local access privileges when in development or staging environments (thanks to [sebastienserre](https://github.com/sebastienserre) for the suggestion).
- New log message when the archiving of an email fails.

### Changed
- Consistent reset for settings.
- Improved translation loading.
- [WP_CLI] `m-archive` command have now a definition and all synopsis are up to date.
- Improved self monitoring to handle archivers internal errors.
- Code refactoring led to a huge execution speed gain: MailArchiver is now 40% faster.
- Upgraded Monolog library from version 2.0.2 to version 2.2.0.

### Fixed
- MailArchiver doesn't correctly honour previous error handler calls (thanks to [ajoah](https://github.com/ajoah)).
- MailArchiver jams the plugin/theme editor while editing PHP files (thanks to [ajoah](https://github.com/ajoah)).
- In Site Health section, Opcache status may be wrong (or generates PHP warnings) if OPcache API usage is restricted.
- The activation status of DecaLog (as logger) may be wrongly detected.

### Removed
- MailArchiver internal watchdog as it is no longer necessary.

## [2.1.0] - 2020-11-23

### Added
- Compatibility with WordPress 5.6.

### Changed
- Improvement in the way roles are detected.

### Fixed
- [SEC001] User may be wrongly detected in XML-RPC or Rest API calls.
- When site is in english and a user choose another language for herself/himself, menu may be stuck in english.
- Some typos in `readme.txt` and `README.md`.

## [2.0.0] - 2020-10-15

### Added
- [WP-CLI] New command to display MailArchiver status: see `wp help m-archive status` for details.
- [WP-CLI] New command to toggle on/off main settings: see `wp help m-archive settings` for details.
- [WP-CLI] New command to manage archivers (list, start, pause, clean, purge, remove, add and set): see `wp help m-archive archiver` for details.
- [WP-CLI] New command to view available archiver types (list and describe): see `wp help m-archive type` for details.
- New Site Health "info" section about shared memory.

### Changed
- The positions of PerfOps menus are pushed lower to avoid collision with other plugins (thanks to [Loïc Antignac](https://github.com/webaxones)).
- The selector for WordPress archivers is now sorted: running first, paused after (thanks to [Loïc Antignac](https://github.com/webaxones)).
- Improved layout for language indicator.
- Admin notices are now set to "don't display" by default.
- Improved IP detection  (thanks to [Ludovic Riaudel](https://github.com/lriaudel)).
- Improved changelog readability.
- The integrated markdown parser is now [Markdown](https://github.com/cebe/markdown) from Carsten Brandt.
- Prepares PerfOps menus to future 5.6 version of WordPress.

### Fixed
- The remote IP can be wrongly detected when behind some types of reverse-proxies.
- Some multi-attachments may be wrongly recorded.
- The rotating file archiver wrongly skips mails when sent from external process.
- With Firefox, some links are unclickable in the Control Center (thanks to [Emil1](https://wordpress.org/support/users/milouze/)).

### Removed
- Parsedown as integrated markdown parser.

## [1.6.0] - 2020-07-20

### Added
- Compatibility with WordPress 5.5.

### Changed
- Improved installation/uninstallation and activation/deactivation processes.
- In WordPress archiver, the shown columns are now automatically set.

### Fixed
- Uninstalling the plugin may produce a PHP error (thanks to [Emil1](https://wordpress.org/support/users/milouze/)).
- The WordPress archiver may be not purged when it should be (thanks to [Emil1](https://wordpress.org/support/users/milouze/)).
- The WordPress archiver may be wrongly purged when '0' is set as a limit (thanks to [Emil1](https://wordpress.org/support/users/milouze/)).
- In some conditions, some table may not be deleted while uninstalling.

### Removed
- The screen options in WordPress archiver (as it is now automatically set).

## [1.5.2] - 2020-06-29

### Changed
- Full compatibility with PHP 7.4.
- Automatic switching between memory and transient when a cache plugin is installed without a properly configured Redis / Memcached.

### Fixed
- When used for the first time, settings checkboxes may remain checked after being unchecked.

## [1.5.1] - 2020-05-05

### Changed
- The WordPress archives tables are now deleted when plugin is uninstalled.

### Fixed
- There's an error while activating the plugin when the server is Microsoft IIS with Windows 10.
- Some tabs may be hidden when site is switched in another language.
- With Microsoft Edge, some layouts may be ugly.

## [1.5.0] - 2020-04-12

### Added
- Compatibility with [DecaLog](https://wordpress.org/plugins/decalog/) early loading feature.

### Changed
- The settings page have now the standard WordPress style.
- Better styling in "PerfOps Settings" page.
- In site health "info" tab, the boolean are now clearly displayed.

### Fixed
- In some cases, the "screen options" tab may be invisible.
- Short links (in other dashboard pages) are wrong. 

## [1.4.0] - 2020-03-01

### Added
- Full integration with PerfOps One suite.
- Full compatibility with [APCu Manager](https://wordpress.org/plugins/apcu-manager/).
- Compatibility with WordPress 5.4.

### Changed
- New menus (in the left admin bar) for accessing features: "PerfOps Records" and "PerfOps Settings".

### Fixed
- Some raw mails may be wrongly rendered in WordPress archiver when they contains the character `"`.
- The header extraction of the "from" field may produce an error.

### Removed
- Compatibility with WordPress versions prior to 5.2.
- Old menus entries, due to PerfOps integration.

## [1.3.0] - 2020-01-03

### Added
- Full compatibility (for internal cache) with Redis and Memcached.
- Using APCu rather than database transients if APCu is available.
- New Site Health "status" sections about OPcache and object cache. 
- New Site Health "status" section about i18n extension for non `en_US` sites.
- New Site Health "info" sections about OPcache and object cache.
- New Site Health "info" section about the plugin itself.
- New Site Health "info" section about archivers settings. 

### Changed
- Upgraded Monolog library from version 2.0.1 to version 2.0.2.

## [1.2.0] - 2019-12-19

### Added
- Support for plugged (custom) `wp_mail` function (mu-plugins or themes).

### Changed
- Mail subject can now contains emojis.
- WordPress archivers now report full source of mail (even for plugged `wp_mail`).
- The "from" field is now detected for Post SMPT plugin.

### Fixed
- In some rare cases, recording a mail error may produce a PHP warning.
- Some plugin options may be not saved when needed (thanks to [Lucas Bustamante](https://github.com/Luc45)).
- Typo in a string.

## [1.1.0] - 2019-12-12

### Added
- Full compatibility with WP Mail SMTP.
- Full compatibility with Post SMTP.
- "from" field is now visible in the mail details in WordPress archiver.

### Changed
- The detection of the "from" field has been improved.

### Fixed
- Some strings are not translatable.
- Some typos in inline help. 

## [1.0.0] - 2019-12-03

Initial release
