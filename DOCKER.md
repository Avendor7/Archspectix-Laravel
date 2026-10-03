# Docker and GitLab CI

GitLab runs the pipeline in `.gitlab-ci.yml` for branch pushes, tags, and merge
requests. An open merge request replaces the duplicate branch pipeline.

The pipeline checks formatting, lint, TypeScript, and dependency audits;
builds the browser and SSR assets; runs Pest on PHP 8.4 and 8.5; and then
builds and publishes a production image. Tests use an in-memory SQLite database
and an ephemeral application key. JUnit results appear in GitLab's test report.

Images are published with the full commit SHA:

```text
registry.avendor.ca/stephen/archspectix-laravel:<commit-sha>
```

Successful builds on the default branch (`master`) also update
`registry.avendor.ca/stephen/archspectix-laravel:latest`. Other branches and merge
requests publish only their commit SHA tag.

The `publish_image` job also provides `image.env` with the exact `APP_IMAGE`
value pinned to the commit SHA. Registry authentication uses GitLab's built-in
job credentials; no additional project secrets are needed. The default branch (`master`) updates
the registry build cache. GitLab does not deploy to your server.

## Runner requirements

Use a Linux GitLab runner with the Docker executor and no job tags. The existing
instance runner accepts untagged jobs. Image builds use rootless BuildKit.
For a self-managed Docker executor, allow its user namespaces and mounts:

```toml
[runners.docker]
  security_opt = ["seccomp:unconfined", "apparmor:unconfined"]
```

This setting belongs in the runner's `config.toml`. The pipeline does not use
Docker-in-Docker or require a Docker socket mount. See GitLab's
[BuildKit runner requirements](https://docs.gitlab.com/ci/docker/using_buildkit/).

## Run the published image

The image contains PHP 8.4, FrankenPHP's HTTP server, Node 22, production Composer
dependencies, and compiled frontend assets. Compose runs the app, its Inertia
SSR process, and PostgreSQL 18 in separate containers. HTTP is published on
`127.0.0.1:8080`; point your reverse proxy at that port and terminate HTTPS there.
Uploaded files and database data live in named volumes.

Copy `compose.yaml` and `.env.docker.example` to your server, then:

```sh
cp .env.docker.example .env.docker
chmod 600 .env.docker
docker login registry.avendor.ca
```

Use a GitLab deploy token with `read_registry` for ongoing image pulls. Edit
`.env.docker` to set `APP_IMAGE` to a published commit tag, your `APP_URL`, a
database password, and an application key. Keep your existing Laravel Cloud key
when migrating an existing installation. To generate a key for a new installation:

```sh
docker run --rm --entrypoint php YOUR_IMAGE artisan key:generate --show
```

Pull the image, start the database, run migrations, then start the stack:

```sh
docker compose --env-file .env.docker pull
docker compose --env-file .env.docker up -d --wait database
docker compose --env-file .env.docker run --rm --no-deps app php artisan migrate --force
docker compose --env-file .env.docker up -d --wait
```

Import your Cloud database before moving production traffic to this stack.
PostgreSQL is the default here; any existing database needs a compatible data
migration if it uses another engine. Docker does not copy Cloud data or secrets.

For later releases, change `APP_IMAGE` to the new commit tag and repeat the
pull/migration/start commands. To roll back application code, select a previous
image tag and recreate the services. Database migrations need their own rollback
plan. The app prepares its Laravel caches at startup; secrets enter through the
runtime environment and are excluded from Docker builds.

## Build locally

```sh
docker build --target production -t archspectix:local .
```

Set `APP_IMAGE=archspectix:local` in `.env.docker` and use the Compose startup
commands above, skipping `pull`. For HTTP-only local testing, set
`APP_URL=http://localhost:8080` and `SESSION_SECURE_COOKIE=false`.

SSR runs as a single process per container. Set `INERTIA_SSR_CLUSTER=true` only
when you want a worker for each CPU available to that container.
