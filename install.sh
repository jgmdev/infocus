#!/usr/bin/env bash
# Script to package the application for install.

cd "$(dirname "$0")" || exit

install_infocus()
{
    if [ -d "$DESTDIR/usr/lib/infocus" ]; then
        echo "Please un-install the previous version first"
        exit 0
    else
        mkdir -p "$DESTDIR/usr/lib/infocus"
    fi

    if [ ! -d "$DESTDIR/usr/bin" ]; then
        mkdir -p "$DESTDIR/usr/bin"
    fi

    if [ ! -d "$DESTDIR/usr/share/applications" ]; then
        mkdir -p "$DESTDIR/usr/share/applications"
    fi

    if [ ! -d "$DESTDIR/usr/lib/systemd/user/" ]; then
        mkdir -p "$DESTDIR/usr/lib/systemd/user/"
    fi

    if [ ! -d "$DESTDIR/usr/share/icons/hicolor/48x48/apps/" ]; then
        mkdir -p "$DESTDIR/usr/share/icons/hicolor/48x48/apps/"
    fi

    echo -n 'Copying files.'

    # Core
    cp -r src "$DESTDIR/usr/lib/infocus/" > /dev/null 2>&1
    cp -r static "$DESTDIR/usr/lib/infocus/" > /dev/null 2>&1
    cp -r vendor "$DESTDIR/usr/lib/infocus/" > /dev/null 2>&1
    cp -r views "$DESTDIR/usr/lib/infocus/" > /dev/null 2>&1
    cp -r controllers "$DESTDIR/usr/lib/infocus/" > /dev/null 2>&1

    echo -n '.'

    cp main.php "$DESTDIR/usr/lib/infocus/" > /dev/null 2>&1
    cp index.php "$DESTDIR/usr/lib/infocus/" > /dev/null 2>&1
    cp LICENSE.md "$DESTDIR/usr/lib/infocus/" > /dev/null 2>&1

    # System
    cp resources/infocus "$DESTDIR/usr/bin/" > /dev/null 2>&1
    cp resources/infocus.desktop "$DESTDIR/usr/share/applications/" > /dev/null 2>&1
    cp resources/infocus.service "$DESTDIR/usr/lib/systemd/user/" > /dev/null 2>&1
    cp static/images/icon.svg "$DESTDIR/usr/share/icons/hicolor/48x48/apps/infocus.svg" > /dev/null 2>&1

    echo -n '.'

    echo " (done)"
}

remove_infocus()
{
    echo -n 'Removing installation files...'

    rm -rf "$DESTDIR/usr/lib/infocus" > /dev/null 2>&1
    rm "$DESTDIR/usr/bin/infocus" > /dev/null 2>&1
    rm "$DESTDIR/usr/share/applications/infocus.desktop" > /dev/null 2>&1
    rm "$DESTDIR/usr/lib/systemd/user/infocus.service" > /dev/null 2>&1
    rm "$DESTDIR/usr/share/icons/hicolor/48x48/apps/infocus.svg" > /dev/null 2>&1

    echo " (done)"
}

case $1 in
    'remove' )
        remove_infocus
        exit
        ;;
esac

install_infocus