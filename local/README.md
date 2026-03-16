# Oranguru Local

Oranguru Local is a version of [oranguru.com](https://oranguru.com) that you can run on your computer. With Frogab Local, you can:

  - Use Oranguru offline
  - Automatically back up your data in any browser
  - Send tasks to Oranguru via a terminal

Oranguru Local supports personal links, but your device will be registered with oranguru.com. If you self-host Oranguru, you can configure Oranguru Local to use your own server instead.

In this README:

  - [Installing Oranguru Local](#installing-oranguru-local)
  - [Starting Oranguru Local](#starting-oranguru-local)
  - [Sending tasks to Oranguru](#sending-tasks-to-oranguru)
  - [Command reference](#command-reference)

See also:

  - [Release notes](https://github.com/dwilding/oranguru/releases)

## Installing Oranguru Local

These instructions explain how to install Oranguru Local as a Python package in a virtual environment. You'll need Python 3.8 or later. See [Download Python](https://www.python.org/downloads/).

If you use Linux, I recommend that you install the [Oranguru Local snap](https://snapcraft.io/oranguru) instead.

To install Oranguru Local in a virtual environment, run the following commands:

```
python3 -m venv .venv
. .venv/bin/activate
pip install oranguru
```

## Starting Oranguru Local

To start Oranguru Local, run the following commands:

```
. .venv/bin/activate
oranguru start
```

Oranguru Local starts:

```
✓ Started Oranguru Local
To access Oranguru, open http://localhost:5000 in your browser
```

If you see the error "a different app is using port 5000", you'll need to use a different port. In this case, run the following commands:

```
oranguru set port 5001  # For example
oranguru start
```

As you use Oranguru, your data is automatically backed up by Oranguru Local. The default location of the backup file is *Oranguru_backup.json* in the working directory. You can use `oranguru set backup-file` to change the location of the backup file. To learn more, run `oranguru help` or see [Command reference](#command-reference).

## Sending tasks to Oranguru

To send a task to Oranguru:

 1. Run the following commands:

    ```
    . .venv/bin/activate
    oranguru
    ```

 2. Type the task, then press Enter. For example:

    ```
    Add a task to your inbox:
    > Record a demo video
    ✓ Sent task to Oranguru
    ```

## Command reference

Here's the output of `oranguru help`:

```
Oranguru Local enables you to run the Oranguru task manager on localhost.
Use 'oranguru' to manage Oranguru Local and send tasks to Oranguru.

Usage:
  oranguru              Send a task to Oranguru, starting Oranguru Local if needed
  oranguru start        Start Oranguru Local
  oranguru stop         Stop Oranguru Local
  oranguru status       Check whether Oranguru Local is running
  oranguru find-backup  Display the full location of the Oranguru backup file

Display/change settings:
  oranguru get <setting>
  oranguru set <setting> <value>

Available settings:
  port                 Port that Oranguru Local runs on
                       (default: 5000)
  expose yes/no        Allow access to Oranguru Local on all network interfaces
                       (default: no)
  backup-file          Location of the Oranguru backup file
                       (default: Oranguru_backup.json in the working directory)
  registration-server  Server that Oranguru uses if you register this device
                       (default: https://oranguru.com/)

Additional commands:
  oranguru help         Display a summary of how to use 'oranguru'
  oranguru --version    Display the version of Oranguru Local that is installed

Environment variables:
  ORANGURU_CONFIG_FILE  If set, specifies where Oranguru Local stores settings
                       and internal state. If not set, Oranguru Local uses
                       Oranguru_config.json in the working directory.
  ORANGURU_PORTS_FILE   If set, specifies where Oranguru Local writes a list of
                       ports that Oranguru Local is running on.
  NO_COLOR=1           If set, 'oranguru' doesn't display any colored text.
```
