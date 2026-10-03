# Archspectix

Search packages from the official Arch Linux repositories, AUR and Omarchy.

## Local development

Install dependencies with `composer install` and `npm ci`. For a new checkout,
copy `.env.example` to `.env`, run `php artisan key:generate`, and run
`php artisan migrate` to create the local database.

Start the development services with:

```sh
composer run dev
```

This uses Laravel's `artisan dev` command and
[`@laravel/multiplex`](https://github.com/laravel/multiplex), as in the current
Laravel starter kits. On macOS and Linux, the server, queue, logs, and Vite run
in separate tabs. Press `1`–`4` to select a tab, `s` for combined output, `r` to
restart the selected process, `/` to search, and `q` to quit.

Use `composer run dev -- --inline` for plain terminal output or
`composer run dev -- --stream` to start with combined output. Windows uses
Laravel's Concurrently fallback. Node.js 22.13 or later is required; see
[DEPENDENCIES.md](DEPENDENCIES.md) for the project's runtime requirements.

## Omarchy packages

Omarchy search and details use the official stable x86_64 repository database.
The first request downloads and parses it, then Laravel caches the packages for
15 minutes. The next request after expiry refreshes it. No cron job, queue worker,
pacman or Omarchy installation is needed.

The server needs the `zstd` executable on its PATH and PHP's Phar extension.
The Docker image, Nixpacks configuration and CI jobs include `zstd`; on
Debian/Ubuntu install it with `apt install zstd`.
Laravel uses the configured cache store (`CACHE_STORE`);
the default database store needs the existing cache migration to have run.

A failed download or invalid database is not cached. Search still shows Arch/AUR
results with an Omarchy unavailable message; the next request tries again.
Omarchy supplies build dates, not last-updated or out-of-date flags.
