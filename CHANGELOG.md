# Changelog
All notable changes to **MailArchiver** are documented in this *changelog*.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and **MailArchiver** adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.3.1] - Unreleased

### Changed
- New redesigned UI for Perfops One plugins management and menus.

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
- Full integration with PerfOps.One suite.
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
