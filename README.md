# MailArchiver
[![version](https://badgen.net/github/release/Pierre-Lannoy/wp-mailarchiver/)](https://wordpress.org/plugins/mailarchiver/)
[![php](https://badgen.net/badge/php/7.2+/green)](https://wordpress.org/plugins/mailarchiver/)
[![wordpress](https://badgen.net/badge/wordpress/5.2+/green)](https://wordpress.org/plugins/mailarchiver/)
[![license](https://badgen.net/github/license/Pierre-Lannoy/wp-mailarchiver/)](/license.txt)

Automatically archive all emails sent from your site. Store them in your WordPress database or send them to external services.

See [WordPress directory page](https://wordpress.org/plugins/mailarchiver/) or [official website](https://perfops.one/mailarchiver). 

__MailArchiver__ is a tool that allows you to:

* catch emails sent by WordPress itself or plugins;
* enrich them with details regarding their sending;
* archive them in the WordPress database and/or send them to external services for logging and alerting;
* view (and filter) archived emails in the WordPress database.

It supports multisite archive delegation (see FAQ) and contains many features to help to protect personal information (user pseudonymization, IP obfuscation, etc.).

At this time, __MailArchiver__ can archive emails:

* to its internal archiving storage and viewer;
* to local or network storage (with file rotation);
* to Imap servers, like GMail, Gandi, OVH or Outlook.Com;
* to external storage services, like Elasticsearch, Elastic Cloud, Loki or Grafana Cloud;
* to external alerting services, like Pushover or Slack;
* to external testing & previewing services, like Litmus, MailerCheck or Email on Acid;
* to external alerting services, like Pushover or Slack;
* to external logging services, like Logentries / insightOps or Loggly;
* to system loggers, like Syslog or Fluentd;

> __MailArchiver__ is part of [PerfOps One](https://perfops.one/), a suite of free and open source WordPress plugins dedicated to observability and operations performance.

__MailArchiver__ is a free and open source plugin for WordPress. It integrates many other free and open source works (as-is or modified). Please, see 'about' tab in the plugin settings to see the details.

## WP-CLI

__MailArchiver__ implements a set of WP-CLI commands. For a full help on these commands, please read [this guide](WP-CLI.md).

## Hooks

__MailArchiver__ introduces some filters and actions to allow plugin customization. Please, read the [hooks reference](HOOKS.md) to learn more about them.

## Installation

1. From your WordPress dashboard, visit _Plugins | Add New_.
2. Search for 'MailArchiver'.
3. Click on the 'Install Now' button.

You can now activate __MailArchiver__ from your _Plugins_ page.

## Support

For any technical issue, or to suggest new idea or feature, please use [GitHub issues tracker](https://github.com/Pierre-Lannoy/wp-mailarchiver/issues). Before submitting an issue, please read the [contribution guidelines](CONTRIBUTING.md).

Alternatively, if you have usage questions, you can open a discussion on the [WordPress support page](https://wordpress.org/support/plugin/mailarchiver/). 

## Contributing

Before submitting an issue or a pull request, please read the [contribution guidelines](CONTRIBUTING.md).

> ⚠️ The `master` branch is the current development state of the plugin. If you want a stable, production-ready version, please pick the last official [release](https://github.com/Pierre-Lannoy/wp-mailarchiver/releases).

## Smoke tests
[![WP compatibility](https://plugintests.com/plugins/mailarchiver/wp-badge.svg)](https://plugintests.com/plugins/mailarchiver/latest)
[![PHP compatibility](https://plugintests.com/plugins/mailarchiver/php-badge.svg)](https://plugintests.com/plugins/mailarchiver/latest)