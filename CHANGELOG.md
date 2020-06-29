# Changelog
All notable changes to **MailArchiver** are documented in this *changelog*.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and **MailArchiver** adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased - will be 1.5.3]

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
### Initial release
