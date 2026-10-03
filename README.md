# Archspectix

Search packages from the official Arch Linux repositories, AUR and Omarchy.

## Omarchy packages

Omarchy search and details use the official stable x86_64 repository database.
The first request downloads and parses it, then Laravel caches the packages for
15 minutes. The next request after expiry refreshes it. No cron job, queue worker,
pacman or Omarchy installation is needed.

The server needs the `zstd` executable on its PATH and PHP's Phar extension.
The Nixpacks configuration includes `zstd`; on Debian/Ubuntu install it with
`apt install zstd`. Laravel uses the configured cache store (`CACHE_STORE`);
the default database store needs the existing cache migration to have run.

A failed download or invalid database is not cached. Search still shows Arch/AUR
results with an Omarchy unavailable message; the next request tries again.
Omarchy supplies build dates, not last-updated or out-of-date flags.
