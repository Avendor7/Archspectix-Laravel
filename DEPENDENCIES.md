# Dependency upgrade

Updated on October 3, 2026 to Laravel 13.34, Inertia 3, Vite 8, Vue 3.5,
Tailwind CSS 4.3, ESLint 10, and Pest 5. Both lockfiles include updated
transitive dependencies.

## Runtime requirements

- PHP 8.4.1 or later in the PHP 8 series. Pest 5 and Symfony 8 require
  PHP 8.4; Composer resolves packages against PHP 8.4.1 so updates made
  on newer development runtimes remain installable on CI and production.
- Node.js 22.13 or later in the Node 22 series, or Node.js 24 or later.
  CI and Nixpacks use Node 22, and `@types/node` follows that runtime.
- TypeScript stays on 6.0 because `typescript-eslint` currently requires
  TypeScript below 6.1. Update it to 7 once the lint tooling supports it.

Native build packages are installed through their parent dependencies instead
of direct Linux-only pins. The button component's missing `reka-ui` dependency
is now declared. Vue and TypeScript lint rules are configured directly to avoid
an unpatched `braces` vulnerability in `@vue/eslint-config-typescript`.

## Install and verify

```sh
composer install
npm ci
npm run build:ssr
npm run typecheck
npm run lint:check
composer test
composer audit
npm audit
```

For a new local checkout, copy `.env.example` to `.env` and run
`php artisan key:generate` before running the PHP tests.

GitLab CI checks PHP and frontend formatting without changing files. Existing
formatting issues were resolved while configuring the pipeline.

On deployment, clear compiled views with `php artisan view:clear` because
Inertia 3 changes its initial page markup. Rebuild application caches using
the normal deployment process, and restart any SSR process after rebuilding.
The Inertia configuration uses the new `pages` keys and preserves the lowercase
`resources/js/pages` directory. The theme component now reads browser APIs
after mounting so it can also render on the SSR server.

Upgrade references: [Laravel 13](https://laravel.com/framework/docs/13.x/upgrade),
[Inertia 3](https://inertiajs.com/docs/v3/getting-started/upgrade-guide),
[Vite 8](https://vite.dev/guide/migration), and
[Pest 5](https://pestphp.com/docs/upgrade-guide).
