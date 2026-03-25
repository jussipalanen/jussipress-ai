# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-03-26

### Added

- WP Stateless 4.4.1 plugin for GCS media offload — uploads stored in `jussipress-bucket` instead of the ephemeral container filesystem
- SMTP mu-plugin for transactional email

### Fixed

- Cloud Build: removed `images:` block incompatible with Kaniko's direct-push model (was causing false build failures)
- Cloud Build: `_DEPLOY` now defaults to `true` so every build deploys to Cloud Run automatically
- Cloud Run: corrected `_CLOUDSQL_INSTANCE` region from `eu-north1` to `europe-north1`

## [1.0.0] - 2026-03-25

### Added

- Initial project setup based on [Roots Bedrock](https://roots.io/bedrock/) with WordPress 6.9
- [Sage](https://roots.io/sage/) starter theme (`jussipress-theme`) with Acorn 5, Blade templating, Tailwind CSS v4, and Vite 8
- Multi-stage `Dockerfile` (Node 22 + Composer 2 + PHP 8.4-FPM + nginx + supervisord) with parameterised `ARG` versions
- `docker-compose.yml` for local development (app + MariaDB 11)
- `cloudbuild.yaml` for GCP Cloud Build using Kaniko (layer cache via Artifact Registry, optional Cloud Run deploy step)
- `docker/nginx.conf` — Bedrock-aware nginx config (port 8080, uploads PHP execution blocked, gzip enabled)
- `docker/supervisord.conf` — manages nginx and php-fpm processes
- `docker/php.ini` — WordPress-optimised PHP settings (OPcache, upload limits, error logging to stderr)
- `.dockerignore` — excludes vendor dirs, node_modules, built assets, and secrets from build context
- `dev` helper script — Docker Compose aliases, WP-CLI, Composer, database, and theme tooling
- GitHub Actions CI workflow: PHP job (Pint, PHPStan, Pest) and JS job (ESLint, Prettier, blade-formatter, Vite build)
- PHP code style with [Laravel Pint](https://laravel.com/docs/pint) (PER preset, configured in `pint.json`)
- PHP static analysis with [PHPStan](https://phpstan.org/) level 5 + [phpstan-wordpress](https://github.com/szepeviktor/phpstan-wordpress) stubs (`phpstan.neon`), with theme vendor bootstrapped so Acorn and Illuminate classes resolve correctly
- JavaScript linting with [ESLint](https://eslint.org/) 9 flat config — browser + WordPress globals, enforces `const`/`let`
- Code formatting with [Prettier](https://prettier.io/) for JS and CSS
- Blade template formatting with [blade-formatter](https://github.com/shufo/blade-formatter)
- PHP test suite with [Pest](https://pestphp.com/) 4
