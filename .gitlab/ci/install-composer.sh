#!/bin/sh
set -eu

apt-get update -qq
apt-get install -y --no-install-recommends git unzip
rm -rf /var/lib/apt/lists/*

installer=/tmp/archspectix-composer-installer.php
curl --fail --silent --show-error https://getcomposer.org/installer -o "$installer"
expected=$(curl --fail --silent --show-error https://composer.github.io/installer.sig)
actual=$(php -r 'echo hash_file("sha384", $argv[1]);' "$installer")
if [ "$expected" != "$actual" ]; then
    echo 'Composer installer checksum does not match.' >&2
    exit 1
fi
php "$installer" --2 --install-dir=/usr/local/bin --filename=composer --quiet
rm "$installer"
