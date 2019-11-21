=== MailArchiver ===
Contributors: PierreLannoy
Tags: logs, error reporting, monitoring, site health, logging
Requires at least: 5.2
Requires PHP: 7.2
Tested up to: 5.3
Stable tag: 1.6.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Capture and log events on your site. View them in your dashboard and send them to logging services.

== Description ==

**Capture and log events on your site. View them in your dashboard and send them to logging services.**

**MailArchiver** is a tool that aims to:

* capture events generated by the core of WordPress and themes / plugins;
* enrich these events with many details regarding their triggering;
* record these events in the WordPress database and/or send them to external services for logging, monitoring and alerting;
* view (and filter) events recorded in the WordPress database.

It supports multisite logs delegation (see FAQ) and contains many features to help to protect personal information (user pseudonymization, IP obfuscation, etc.).

It can be used in dev/debug phases or on production sites.

At this time, **MailArchiver** can send events:

* by mail (alerting);
* in the browser console (for debugging purpose);
* to its internal logging storage and viewer;
* to external logger, like Syslog or Fluentd;
* to external logging services, like Logentries / insightOps or Loggly;
* to external alerting services, like Pushover or Slack;
* to local or network storage (with file rotation);

**MailArchiver** is a free and open source plugin for WordPress. It integrates many other free and open source works (as-is or modified). Please, see 'about' tab in the plugin settings to see the details.

= Developers =

If you're a plugins / themes developer and want to take advantage of the logging features of MailArchiver, visit the [GitHub reporistory](https://github.com/Pierre-Lannoy/wp-mailarchiver) of the plugin to learn how to develop a listener for your plugins / themes. Ah, and yes, it's PSR-3 compliant ;)

= Support =

This plugin is free and provided without warranty of any kind. Use it at your own risk, I'm not responsible for any improper use of this plugin, nor for any damage it might cause to your site. Always backup all your data before installing a new plugin.

Anyway, I'll be glad to help you if you encounter issues when using this plugin. Just use the support section of this plugin page.

= Donation =

If you like this plugin or find it useful and want to thank me for the work done, please consider making a donation to [La Quadrature Du Net](https://www.laquadrature.net/en) or the [Electronic Frontier Foundation](https://www.eff.org/) which are advocacy groups defending the rights and freedoms of citizens on the Internet. By supporting them, you help the daily actions they perform to defend our fundamental freedoms!

== Installation ==

= From your WordPress dashboard =

1. Visit 'Plugins > Add New'.
2. Search for 'MailArchiver'.
3. Click on the 'Install Now' button.
4. Activate MailArchiver.

= From WordPress.org =

1. Download MailArchiver.
2. Upload the `mailarchiver` directory to your `/wp-content/plugins/` directory, using your favorite method (ftp, sftp, scp, etc...).
3. Activate MailArchiver from your Plugins page.

= Once Activated =

1. Visit 'Settings > MailArchiver' in the left-hand menu of your WP Admin to adjust settings.
2. Enjoy!

== Frequently Asked Questions ==

= What are the requirements for this plugin to work? =

You need at least **WordPress 5.2** and **PHP 7.2**.

= Can this plugin work on multisite? =

Yes. It is designed to work on multisite too. Network Admins can configure the plugin and have access to all events logs. Sites Admins have access to the events logs of their sites.

= Where can I get support? =

Support is provided via the official [WordPress page](https://wordpress.org/support/plugin/mailarchiver/).

= Where can I find documentation? =

Documentation for users is provided as the form of inline help in the plugin.

Developer's documentation can be found in the [GitHub reporistory](https://github.com/Pierre-Lannoy/wp-mailarchiver) of the plugin.

= Where can I report a bug? =
 
You can report bugs and suggest ideas via the [GitHub issue tracker](https://github.com/Pierre-Lannoy/wp-mailarchiver/issues) of the plugin.

== Changelog ==

Please, see [full changelog](https://github.com/Pierre-Lannoy/wp-mailarchiver/blob/master/CHANGELOG.md) on GitHub.

== Upgrade Notice ==

== Screenshots ==

1. Set Loggers
2. Internal Viewer
3. Event Details in Internal Viewer