MailArchiver is fully usable from command-line, thanks to [WP-CLI](https://wp-cli.org/). You can set MailArchiver options, activate archivers and much more, without using a web browser.

1. [Managing archivers](#managing-archivers) - `wp m-archive archiver`
2. [Using archiver types](#managing-archivers) - `wp m-archive type`
3. [Getting MailArchiver status](#getting-mailachiver-status) - `wp m-archive status`
4. [Managing main settings](#managing-main-settings) - `wp m-archive settings`
5. [Decrypting content](#decrypting-content) - `wp m-archive decrypt`
6. [Misc flags](#misc-flags)
7. [Piping and storing](#piping-and-storing)

## Managing archivers

With the `wp m-archive archiver <list|start|pause|clean|purge|remove|add|set> [<uuid_or_type>] [--settings=<settings>] [--detail=<detail>] [--format=<format>] [--yes] [--stdout]` command you can perform all available operations on archivers.

### Listing archivers

To obtain a list of set archivers, use `wp m-archive archiver list`.

### Starting or pausing archivers

To change the status of a archiver, use `wp m-archive archiver <start|pause> <uuid>` where `<uuid>` is the identifier of the archiver.

### Cleaning or purging archivers

Some archivers allow to be cleaned (deletion of stale mails) or purged (deletion of all mails). To initiate such an operation on a archiver, use `wp m-archive archiver <clean|purge> <uuid>` where `<uuid>` is the identifier of the archiver.

### Removing an archiver

To permanently remove an archiver, use `wp m-archive archiver remove <uuid>` where `<uuid>` is the identifier of the archiver.

### Modifying an archiver

To modify an archiver, use `wp m-archive archiver set <uuid> --settings=<settings>` where: 

- `<uuid>` is the identifier of the archiver.
- `<settings>` a json string containing ***"parameter":value*** pairs. The available parameters can be browsed with the `wp m-archive type describe` command (see [using archiver types](#using-archiver-types)).
                                                                                                                                         
`<settings>`  must start with `'{` and end with `}'` (see examples).

### Adding an archiver

To add an archiver, use `wp m-archive archiver add <type> --settings=<settings>` where:

- `<type>` is the type of the archiver. The available types can be obtained with the `wp m-archive type list` command (see [using archiver types](#using-archiver-types)).
- `<settings>` a json string containing ***"parameter":value*** pairs. The available parameters can be browsed with the `wp m-archive type describe` command (see [using archiver types](#using-archiver-types)).
                                                                                                                                         
`<settings>`  must start with `'{` and end with `}'` (see examples).

### Examples

To list the archivers, type the following command:
```console
pierre@dev:~$ wp m-archive archiver list
+--------------------------------------+--------------------+-------------------------+---------+
| uuid                                 | type               | name                    | running |
+--------------------------------------+--------------------+-------------------------+---------+
| b72db900-af4e-4c37-91da-f8c158e323ca | Slack              | Alerts on emails errors | yes     |
| 9660a880-5db4-439c-8488-4bc907a71265 | WordPress archiver | Anonymized archives     | yes     |
| 350773c0-dd47-44b7-b8f9-ec1b4a48ddf5 | WordPress archiver | Errors archives         | yes     |
| 821aeafa-f7b6-4ac8-8c48-9155f0e2e9e4 | Rotating files     | Full rotating archives  | yes     |
| 20dc19ce-f7b6-4f40-bb15-e266e53ee564 | WordPress archiver | TEST                    | no      |
+--------------------------------------+--------------------+-------------------------+---------+
```

To start the archiver identified by 'c40c59dc-5e34-44a1-986d-e1ecb520e3ca', type the following command:
```console
pierre@dev:~$ wp m-archive archiver start c40c59dc-5e34-44a1-986d-e1ecb520e3ca
Success: archiver c40c59dc-5e34-44a1-986d-e1ecb520e3ca is now running.
```

To purge the archiver identified by 'c40c59dc-5e34-44a1-986d-e1ecb520e3ca' without confirmation prompt, type the following command:
```console
pierre@dev:~$ wp m-archive archiver purge c40c59dc-5e34-44a1-986d-e1ecb520e3ca --yes
Success: archiver c40c59dc-5e34-44a1-986d-e1ecb520e3ca successfully purged.
```

To remove the archiver identified by 'c40c59dc-5e34-44a1-986d-e1ecb520e3ca' without confirmation prompt, type the following command:
```console
pierre@dev:~$ wp m-archive archiver remove c40c59dc-5e34-44a1-986d-e1ecb520e3ca --yes
Success: archiver c40c59dc-5e34-44a1-986d-e1ecb520e3ca successfully removed.
```

To change the settings of the archiver identified by 'c40c59dc-5e34-44a1-986d-e1ecb520e3ca', type the following command:
```console
pierre@dev:~$ wp m-archive archiver set c40c59dc-5e34-44a1-986d-e1ecb520e3ca --settings='{"proc_trace": false, "level":"warning"}'
Success: archiver c40c59dc-5e34-44a1-986d-e1ecb520e3ca successfully set.
```

To add a WordPress archiver, type the following command:
```console
pierre@dev:~$ wp m-archive archiver add WordpressHandler --settings='{"rotate": 8000, "purge": 5, "level":"warning", "proc_wp": true}'
Success: archiver 5b09be13-16f6-4ced-972e-98408df0fd49 successfully created.
```

## Using archiver types

With the `wp m-archive type <list|describe> [<archiver_type>] [--format=<format>]` command you can query all available types for archiver creation / modification and obtain description of corresponding settings. This command helps you to fine-tune archivers via the command-line.

### Listing types

To obtain a list of available types, use `wp m-archive type list`.

### Describing types

To obtain the detail of a specific type, use `wp m-archive type describe <archiver_type>` where `<archiver_type>` is one of the type listed by the `wp m-archive type list` command.

In addition to a general description "sheet", this command outputs a detailed listing of the available settings that can be used in the `wp m-archive archiver set` and `wp m-archive archiver add` commands.

### Examples

To list the types, type the following command:
```console
pierre@dev:~$ wp m-archive type list
+---------------------+----------+-----------------------------+---------+
| type                | class    | name                        | version |
+---------------------+----------+-----------------------------+---------+
| FluentHandler       | logging  | Fluentd                     | 2.2.1   |
| LogentriesHandler   | logging  | Logentries &amp; insightOps | 2.2.1   |
| LogglyHandler       | logging  | Loggly                      | 2.0.0   |
| PshHandler          | alerting | Pushover                    | 2.2.1   |
| RotatingFileHandler | storing  | Rotating files              | 2.0.0   |
| SlackWebhookHandler | alerting | Slack                       | 2.0.0   |
| SyslogUdpHandler    | logging  | Syslog                      | 2.0.0   |
| WordpressHandler    | storing  | WordPress archiver          | 2.2.1   |
+---------------------+----------+-----------------------------+---------+
```

To obtain details about the WordpressHandler type, type the following command:
```console
pierre@dev:~$ wp m-archive type describe WordpressHandler
              
WordPress archiver - WordpressHandler
An archive stored in your WordPress database and available right in your admin dashboard.

Minimal Level

  all emails

Parameters

  * Name - Used only in admin dashboard.
    - field name: name
    - field type: string
    - default value: "New Archiver"

  * Archived emails - Archived emails level.
    - field name: level
    - field type: string
    - default value: "info"
    - available values:
       "info": All emails.
       "error": Only emails in error.

  * Mails - Maximum number of emails stored in this archive (0 for no limit).
    - field name: rotate
    - field type: integer
    - default value: 10000
    - range: [0-10000000]

  * Days - Maximum age of emails stored in this archive (0 for no limit).
    - field name: purge
    - field type: integer
    - default value: 15
    - range: [0-730]

  * IP obfuscation - Recorded fields will contain hashes instead of real IPs.
    - field name: obfuscation
    - field type: boolean
    - default value: false

  * User pseudonymization - Recorded fields will contain hashes instead of user IDs & names.
    - field name: pseudonymization
    - field type: boolean
    - default value: false

  * Email masking - Recorded fields will contain hashes instead of email adresses.
    - field name: mailanonymization
    - field type: boolean
    - default value: false

  * Reported details: WordPress - Allows to record site, user and remote IP of the current request.
    - field name: proc_wp
    - field type: boolean
    - default value: true

Example

  {"rotate": 10000, "purge": 15}

```

## Getting MailArchiver status

To get detailed status and operation mode, use the `wp m-archive status` command.

## Managing main settings

To toggle on/off main settings, use `wp m-archive settings <enable|disable> <early-loading|auto-logging|auto-start>`.

### Available settings

- `auto-start`: if activated, when a new archiver is added it automatically starts.

### Example

To disable auto-start without confirmation prompt, type the following command:
```console
pierre@dev:~$ wp m-archive settings disable auto-start --yes
Success: auto-start is now deactivated.
```

## Decrypting content

With the `wp m-archive decrypt <password> <encrypted-content> [--stdout]` command you can decrypt a mail body previously encrypted by MailArchiver.

### Examples

To decrypt the specified content (encrypted by MailArchiver with the password "password"), type the following command:
```console
pierre@dev:~$ wp m-archive decrypt "password" "IBP50CCSNgUIMVf99HKZ5n6FpaMY8WVUJNZvF5PZW1vofcqotHX/IZeCT1BmFCA9+qpR1vsZKRyNyWacEeQl/sNpww4tZnq/Yoh4dMzqkETfUQv0/LmvhuV258dMRqRGHzYhcbvzxUXX1vhVNRLv3g=="
Success: decrypted content is "MailArchiver rocks!".
```

## Misc flags

For most commands, MailArchiver lets you use the following flags:
- `--yes`: automatically answer "yes" when a question is prompted during the command execution.
- `--stdout`: outputs a clean STDOUT string so you can pipe or store result of command execution (see [piping and storing](#piping-and-storing)).

> It's not mandatory to use `--stdout` when using `--format=count` or `--format=ids`: in such cases `--stdout` is assumed.

## Piping and storing

As MailArchiver outputs only the element that makes the most sense when you use the `--stdout` flag, you can pipe commands the way you are used to doing it.

The `wp m-archive archiver ... --stdout`, for example, will in most case return the archiver uuid. So you can "chain" commands to create, set and start a archiver in one line:

```console
pierre@dev:~$ wp m-archive archiver add WordpressHandler --stdout | xargs wp m-archive archiver set --settings='{"name":"Nice archiver!"}' --stdout | xargs wp m-archive archiver start
Success: archiver f75dc435-2c63-4f16-bb29-cf77a478da4a is now running.
```

On the same "scheme" you can pause all set archivers by iterating the `wp m-archive archiver pause` on all uuid returned by `wp m-archive archiver list`:
```console
pierre@dev:~$ wp m-archive archiver list --format=ids | xargs -0 -d ' ' -I % wp m-archive archiver pause %
The archiver c40c59dc-5e34-44a1-986d-e1ecb520e3ca is already paused.
The archiver f1ee25c7-d9fe-42ee-86df-9394b411e2a7 is already paused.
The archiver 93f84673-a623-4c15-825e-d867f35565ff is already paused.
The archiver 078e124b-2122-4f03-91e9-2bbf70964618 is already paused.
The archiver 9553830a-75e7-4405-80c5-8bf726ccf45c is already paused.
Success: archiver 37cf1c00-d67d-4e7d-9518-e579f01407a7 is now paused.
Success: archiver 5bacf078-2a1f-4c43-8961-4d8ca647661b is now paused.
The archiver df59a30d-dc30-4771-a4a5-a654f0a5cd46 is already paused.
The archiver 6d9943e5-b4fa-4dee-8b02-93a9294b1373 is already paused.
The archiver 972d417f-2294-4888-8bfe-bf038e39f8e8 is already paused.
The archiver 29fdb590-41a8-4a1d-98e5-465a7be10a96 is already paused.
Error: system archivers can't be managed.
The archiver 83fdd893-0979-4bbb-848b-d38e8fbf813d is already paused.
The archiver 9c6e7967-a1b7-447c-9ed5-ec73853a6867 is already paused.
The archiver 8e2ee516-6f8d-40d1-ac16-c3e61274a41a is already paused.
```

You can use, of course, `--stdout` to store command result in variable when you write scripts:

```bash
#!/bin/bash

uuid=$(wp m-archive archiver start 37cf1c00-d67d-4e7d-9518-e579f01407a7 --stdout)
echo $uuid
```

And, as MailArchiver sets exit code, you can use `$?` to write scripts too:

```bash
#!/bin/bash

wp m-archive archiver add FluentHandler --stdout | xargs wp m-archive archiver start

if [ $? -eq 0 ]
then
  wp log send notice "All right!"
else
  wp log send error "Unable to start, aborting..."
  exit 1
fi

# continue
```

> To know the meaning of MailArchiver exit codes, just use the command `wp m-archive exitcode list`.