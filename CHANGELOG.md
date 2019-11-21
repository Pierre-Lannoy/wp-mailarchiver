# Changelog
All notable changes to **MailArchiver** is documented in this *changelog*.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and **MailArchiver** adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
- Upgraded Monolog library from version 2.0.0 to version 2.0.1.
- Events for `wp_ajax_sample_permalink` hook are now rendered at debug level.
- The events levels from WP Security Audit Log listener are now more consistent. 
- Unit symbols and abbreviations are now visually differentiated.
- There's now a non-breaking space between values and units.
### Fixed
- Some very long fields may be displayed outside the box in the WordPress events logs. 
- With some OPcache configurations there may be PHP warning in "CoreListener".
- Some cached items may not be deleted when needed.

## [1.6.0] - 2019-11-11
### Added
- New listener for WooCommerce.
- New listener for Jetpack.
- New listener for WP Security Audit Log.
- New listener for W3 Total Cache.
- New listener for WP Super Cache.
- New logger to send logs to Solawinds Loggly.
- New logger to send logs to Logentries / insightOps.
- New "Content" box in event viewer (for WordPress events logs) to display detailed error code and message.
### Changed
- PHP listener now detects all OPcache resets or status changes.
- Message size (for WordPress events logs) has been increased from 1000 to 7500 characters.
- Improved display for message column in WordPress events logs.
- Upgraded Feather library from version 4.22.1 to version 4.24.1.
- The PHP and WordPress backtraces are now cleaned from MailArchiver and Monolog references.
- The name and help message for backtraces settings are more clear.
- cURL timeouts have now an "error" level (it was previously a "critical" one).
### Fixed
- Non blocking HTTP request may sometimes generate a "Global Timeout" event in core listener (thanks to [Julio Potier](https://github.com/JulioPotier)).
- Changelog date of version 1.5.3 is wrong.
### Security
- [PRV001] In case of failed login, the username may appear in clear text logs even if pseudonymisation is activated.

## [1.5.3] - 2019-11-01
### Fixed
- The message for PHP upgrading/downgrading was wrongly named "WordPress" (instead of "PHP").
- A PHP notice may appear when enqueuing some plugin assets.

## [1.5.2] - 2019-10-24
### Changed
- Normalization of cache IDs to avoid name collisions.
- Developer's documentation modified as wp.org [now allows](https://meta.trac.wordpress.org/ticket/3791) PHP7.1+ code.
### Fixed
- Some cached elements may be autoloaded even if not needed.
- [MultiSite] The "what's new?" screen is only viewable by network admin.
- [MultiSite] Action link in sites list for network admins.
- [MultiSite] Action link in "my sites" for local admins.

## [1.5.1] - 2019-10-05
### Changed
- New logo, more in line with the plugin topic.
- The (nag) update message has now a link to display changelog.

## [1.5.0] - 2019-10-04
### Added
- Compatibility with WordPress 5.3.
- It's now possible to use public CDN to serve MailArchiver scripts and stylesheets (see _Settings | MailArchiver | Options_).
### Changed
- Finally better IP reporting with local address fallback.
- Improved information message when in developer preview or release candidate version.
- The right logo is now displayed in the "about box".
### Removed
- "Compatibility Mode" for Monolog 2, as wp.org [now allows](https://meta.trac.wordpress.org/ticket/3791) PHP7.1+ code.

## [1.4.1] - 2019-09-17
### Changed
- Better IP reporting with local address fallback.
- Better timezone detection for multisites.
### Fixed
- In some cases, the remote IP is not correctly set (for sites behind a proxy).

## [1.4.0] - 2019-09-13
### Added
- Full support for [User Switching](https://wordpress.org/plugins/user-switching/) plugin.
- New events in core listener for posts (trashed, untrashed, drafted, draft saved, published, privately published, scheduled, unscheduled, pending review).
- New events in core listener for comments (created, updated, deleted, marked as "spam", marked as "not spam", trashed, untrashed, approved, unapproved, duplicate triggered).
- New events in core listener for menus (created, updated, deleted, item added, item updated).
- New events in core listener for users (updated, role added).
- New events in WPMU listener for users (marked as "spam", marked as "not spam").
- The core listener can now detect plugin and theme installations/updates.
- The core listener can now detect translations updates.
### Changed
- Plugin activation/deactivation give the full plugin name (instead of its slug).
- Events regarding "options" and "transients" now differentiate site/network operations.
- Events relating to HTTP error codes (outbound requests) are now classified according to their severity.
- The word "blog" has been replaced by "site" in events messages.
- Some help strings have been modified to be more clear.
- Lower severity of serialized json messages (triggered by `wp_die` handler) from criticial to debug.
### Removed
- "Switch Blog" event (for WPMU) because lack of documentation.
### Fixed
- PHP notice when accessing a multisites log as a local admin.
- PHP notice when displaying an event with no backtrace.
- Removing a user of a site may produce an inconsistent log message.

## [1.3.0] - 2019-09-11
### Added
- New listener for WordPress multisite specific events.
- New listener for [htaccess Server-Info & Server-Status](https://wordpress.org/plugins/htaccess-server-info-server-status/) plugin.
- Links to support, site and GitHub repository in plugin list.
- New class (Decalog\Logger) to use as a standard PSR-3 logger (for plugins and themes developers).
- New shortcodes to query the plugin statistics.
### Removed
- WordPress release now excludes GitHub .wordpress-org directory.
### Fixed
- A WordPress logger may record log in the wrong table in multisites instances.

## [1.2.1] - 2019-09-05
### Changed
- Pushover logger now allows to set socket timeout.
- Watchdog (for self listening) is now fully operational for PHP and MySQL channels.
### Fixed
- ChromePHP logger header size limit is unsuitable for the most recent Chromium versions (thanks to [dotMastaz](https://github.com/dotMastaz)).

## [1.2.0] - 2019-08-31
### Added
- The WordPress listener can now detect version upgrading/downgrading.
- The PHP listener can now detect version upgrading/downgrading.
- The PHP listener can now detect extensions activation/deactivation.
- The database listener can now detect version upgrading/downgrading.
- The plugin now embeds its own inline help (help tab in all screens).
### Changed
- Upgraded Monolog version from 2.0.0-beta-2 to 2.0.0.
### Security
- In a multisite, a non-admin user was able to read the name of a logger (not its content).

## [1.1.1] - 2019-08-29
### Changed
- Better 'Page not found' detection and report.
- Better handling of malformed `wp_die` calls.
- 'Component' become 'source' in WordPress events viewer.
- Some events have new levels and/or messages to comply with rules described in `DEVELOPER.md`.
### Fixed
- WordPress formatter may (wrongly) emit warnings when source ip is unknown.

## [1.1.0] - 2019-08-28
### Added
- A test message can now be sent to a specific logger, from the loggers list.
### Changed
- Status (in loggers list) have now their own column.
- Polishing loggers list.
- The `README.md` displays badges.
### Removed
- MailArchiver admin pages no longer render emoji.
- The section "install from GitHub" has been removed from `README.md`.
### Fixed
- Url of the EFF website was wrong in `readme.txt`.

## [1.0.1] - 2019-08-26
### Changed
- Language file (`.pot`) is updated.
- Main file has now a consistent GPL version license.
- In admin dashboard, `&` character is now outputted in UTF, not with its HTML entity equivalent.
### Fixed
- Erroneous date in `CHANGELOG.md` is changed.
- Wrongly rendered unordered lists in `readme.txt` are fixed.

## [1.0.0] - 2019-08-26
### Initial release
