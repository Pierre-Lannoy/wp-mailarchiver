=== MailArchiver ===
Contributors: PierreLannoy, hosterra
Tags: archive, email, email log, log, mail
Requires at least: 6.2
Requires PHP: 8.1
Tested up to: 6.5
Stable tag: 4.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Automatically archive all emails sent from your site. Store them in your WordPress database or send them to external services.

== Description ==

**Automatically archive all emails sent from your site. Store them in your WordPress database or send them to external services.**

> 🎁 Give this plugin a drive test on a free dummy site: [One-Click Test!](https://tastewp.com/new/?pre-installed-plugin-slug=mailarchiver)

**MailArchiver** is a tool that allows you to:

* catch emails sent by WordPress itself or plugins;
* enrich them with details regarding their sending;
* archive them in the WordPress database and/or send them to external services for logging and alerting;
* view (and filter) archived emails in the WordPress database.

It supports multisite archive delegation (see FAQ) and contains many features to help to protect personal information (user pseudonymization, IP obfuscation, etc.).

At this time, **MailArchiver** can archive emails:

* to its internal archiving storage and viewer;
* to local or network storage (with file rotation);
* to Imap servers, like GMail, Gandi, OVH or Outlook.Com;
* to external storage services, like Elasticsearch, Elastic Cloud, Loki or Grafana Cloud;
* to external alerting services, like Pushover or Slack;
* to external testing & previewing services, like Litmus, MailerCheck or Email on Acid;
* to external logging services, like Logentries / insightOps or Loggly;
* to system loggers, like Syslog or Fluentd.

For a full list of supported - internal or third-party - services please, jump to the official [supported services list](https://perfops.one/mailarchiver#services).

**MailArchiver** supports an extensive set of WP-CLI commands to:

* decrypt a previously encrypted mail body - see `wp help m-archive decrypt` for details;
* toggle on/off main settings - see `wp help m-archive settings` for details;
* manage archivers (list, start, pause, clean, purge, remove, add and set) - see `wp help m-archive archiver` for details;
* view available archiver types (list and describe) - see `wp help m-archive type` for details.

For a full help on WP-CLI commands in MailArchiver, please [read this guide](https://perfops.one/mailarchiver-wpcli).

> **MailArchiver** is part of [PerfOps One](https://perfops.one/), a suite of free and open source WordPress plugins dedicated to observability and operations performance.

**MailArchiver** is a free and open source plugin for WordPress. It integrates many other free and open source works (as-is or modified). Please, see 'about' tab in the plugin settings to see the details.

= Support =

This plugin is free and provided without warranty of any kind. Use it at your own risk, I'm not responsible for any improper use of this plugin, nor for any damage it might cause to your site. Always backup all your data before installing a new plugin.

Anyway, I'll be glad to help you if you encounter issues when using this plugin. Just use the support section of this plugin page.

= Privacy =

This plugin, as any piece of software, is neither compliant nor non-compliant with privacy laws and regulations. It is your responsibility to use it - by activating the corresponding options or services - with respect for the personal data of your users and applicable laws.

This plugin doesn't set any cookie in the user's browser.

This plugin may handle personally identifiable information (PII). If the GDPR or CCPA or similar regulation applies to your case, you must use the anonymization options embedded in the plugin and/or adapt your processes (consent management, security measure, treatment register, etc.).

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

1. Visit 'PerfOps One > Control Center > MailArchiver' in the left-hand menu of your WP Admin to adjust settings.
2. Enjoy!

== Frequently Asked Questions ==

= What are the requirements for this plugin to work? =

You need at least **WordPress 5.2** and **PHP 7.2**.

= Can this plugin work on multisite? =

Yes. It is designed to work on multisite too. Network Admins can configure the plugin and have access to all archives. Sites Admins have access to the archives of their own sites.

= Where can I get support? =

Support is provided via the official [WordPress page](https://wordpress.org/support/plugin/mailarchiver/).

= Where can I find documentation? =

Documentation for users is provided as the form of inline help in the plugin.

= Where can I report a bug? =
 
You can report bugs and suggest ideas via the [GitHub issue tracker](https://github.com/Pierre-Lannoy/wp-mailarchiver/issues) of the plugin.

== Changelog ==

Please, see [full changelog](https://perfops.one/mailarchiver-changelog).

== Upgrade Notice ==

== Screenshots ==

1. Set Archivers
2. Available Archivers Types
3. Internal Viewer
4. Email Details in Internal Viewer