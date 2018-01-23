# InFocus

Automatic activity time tracker application.

[InFocus Logo](https://raw.githubusercontent.com/jgmdev/infocus/master/static/images/logo.png)

**OS:** Linux
**License:** GPLv3
**Status:** Beta

## About

InFocus is an automatic activity time tracker that monitors the currently
focused window and stores the amount of time spent on it on a daily basis.
The application was developed as a way to monitor my self, and rises from
the concern that I may be trashing significant amounts of time on stuff that
doesn't matters. So InFocus should help in the auto-evaluation of our daily
work on the computer making it easier to make decisions concerning our daily
schedule.

## Installation

The application is developed in PHP and makes use of php's built-in webserver
to host the user interface and makes use of chromium or firefox as mediums
to display the web interface. Currently the --app flag of chromium is used
to get an experience more similar to that of a typical application.

Before using the application you have to fetch the composer dependencies by
doing:

```shell
cd infocus
composer install
```

**Dependencies**

* php - for interface and activity logger
* php-sqlite - the php pdo extension to store activity data
* chromium - to display the user interface (recommended)
* firefox - to display the user interface (optional)
* xprop - get active window
* wmctrl - information of active windows
* xprintidle - detect if the person is away from keyboard

If installing directly to system as root user:

```shell
cd infocus
./install.sh
```

The ./install.sh shell script also supports de DESTDIR environment flag, which
can be used to easily package the application for distribution by distro
packagers.

```shell
cd infocus
DESTDIR=~/infocus/install ./install.sh
```

### Uninstallation

```shell
cd infocus
./install.sh remove
```

## Usage

To start tracking your daily activity is needed to launch the infocus logger
by enabling the systemd infocus service for your user account.

```shell
systemctl --user enable infocus
systemctl --user start infocus
```

This will track all the applications that you use and how much time you spent
on them. This information is stored on a sqlite database located on:

> ~/.config/InFocus/activity_log.db

The web interface can then be launched from the applications menu.

## CLI Usage

For help on the available infocus commands, at your shell just invoke:

```shell
infocus help
```

## Screenshots

**Overview**
[overview](https://raw.githubusercontent.com/jgmdev/infocus/master/screenshots/overview.png)

**Applications**
[applications](https://raw.githubusercontent.com/jgmdev/infocus/master/screenshots/applications.png)

**Activities**
[activities](https://raw.githubusercontent.com/jgmdev/infocus/master/screenshots/activities.png)

**Inactivity**
[inactivity](https://raw.githubusercontent.com/jgmdev/infocus/master/screenshots/inactivity.png)

**Preferences**
[preferences](https://raw.githubusercontent.com/jgmdev/infocus/master/screenshots/preferences.png)