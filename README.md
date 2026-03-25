# JussiPress AI - WordPress site

A modern WordPress project built on [Roots Bedrock](https://roots.io/bedrock/) and the [Sage](https://roots.io/sage/) starter theme, containerised for [Google Cloud Platform](https://cloud.google.com/) with a focus on minimal infrastructure cost.

**Stack**
- WordPress 6.9 managed by Composer (Bedrock)
- Sage theme with [Acorn](https://roots.io/acorn/) (Laravel in WordPress), Blade templating, Tailwind CSS v4, Vite 8
- PHP 8.3 + nginx + supervisord (single container)
- MariaDB 11
- GCP Cloud Run + Cloud Build (Kaniko) + Artifact Registry

---

## Requirements

### Local development

| Tool | Version |
|---|---|
| Docker Engine + Compose v2 | latest |
| Node.js | `^20.19.0` or `>=22.12.0` |
| PHP | `>=8.3` (for running Composer/tools on host) |
| Composer | `^2` |

> PHP and Composer are only needed on the host for IDE tooling (PHPStan, Pint). The application itself runs entirely in Docker.

### Production (GCP)

- GCP project with billing enabled
- APIs enabled: Cloud Build, Cloud Run, Artifact Registry
- Cloud SQL instance (MySQL 8 or compatible) **or** a managed MySQL host
- Cloud Build service account with:
  - `roles/artifactregistry.writer`
  - `roles/storage.admin` (Kaniko layer cache)
  - `roles/run.admin` + `roles/iam.serviceAccountUser` *(if using the Cloud Run deploy step)*

---

## Local development

### 1. Clone and configure environment

```bash
git clone <repo-url> jussipress-ai
cd jussipress-ai

cp .env.example .env
```

Edit `.env` — the minimum required values:

```dotenv
DB_NAME=wordpress
DB_USER=wordpress
DB_PASSWORD=wordpress
DB_HOST=db              # matches the docker-compose service name

WP_ENV=development
WP_HOME=http://localhost:8080
WP_SITEURL="${WP_HOME}/wp"

# Generate at https://roots.io/salts.html
AUTH_KEY='...'
SECURE_AUTH_KEY='...'
# ... (all 8 keys)
```

### 2. Install dependencies

```bash
# PHP — root project
composer install

# PHP — theme
cd web/app/themes/jussipress-theme && composer install && cd -

# Node — theme
cd web/app/themes/jussipress-theme && npm ci && cd -
```

### 3. Start Docker services

```bash
./dev up
```

This starts:
- `app` — PHP 8.3-FPM + nginx on `http://localhost:8080`
- `db` — MariaDB 11

### 4. Install WordPress

```bash
./dev wp core install \
  --url=http://localhost:8080 \
  --title="JussiPress AI" \
  --admin_user=admin \
  --admin_password=secret \
  --admin_email=admin@example.com
```

### 5. Start Vite dev server (theme HMR)

In a separate terminal:

```bash
./dev theme-dev
```

Vite runs at `http://localhost:5173` and injects assets with hot-module replacement into the WordPress site.

---

## `dev` script reference

```
./dev up [svc]          Start services (detached)
./dev down              Stop and remove containers
./dev stop [svc]        Stop without removing
./dev restart [svc]     Restart service(s)
./dev rebuild [svc]     Rebuild image and restart
./dev logs [svc]        Follow logs
./dev ps                Container status

./dev shell             sh into app container (www-data)
./dev root-shell        sh into app container (root)

./dev wp <cmd>          WP-CLI  e.g. ./dev wp plugin list
./dev composer <cmd>    Composer inside container
./dev lint              PHP style check (Pint)
./dev lint-fix          PHP style auto-fix (Pint)
./dev analyse           PHP static analysis (PHPStan)
./dev test              Run tests (Pest)

./dev db                Open MariaDB CLI
./dev db-dump           Dump database to stdout
./dev db-import <file>  Import a SQL file

./dev theme-install     npm ci in theme directory
./dev theme-dev         Vite HMR dev server (host)
./dev theme-build       Build theme assets for production
./dev theme-lint        ESLint check
./dev theme-lint-fix    ESLint auto-fix
./dev theme-format      Prettier write (JS + CSS)
./dev theme-format-check  Prettier check
./dev blade-format      Format all Blade templates
./dev blade-format-check  Check Blade formatting
```

You can also source the script to use the commands as shell functions:

```bash
source dev
up
logs app
```

---

## Code quality

| Tool | Scope | Command |
|---|---|---|
| [Laravel Pint](https://laravel.com/docs/pint) | PHP (PER preset) | `./dev lint` / `./dev lint-fix` |
| [PHPStan](https://phpstan.org/) level 5 + [phpstan-wordpress](https://github.com/szepeviktor/phpstan-wordpress) | PHP static analysis | `./dev analyse` |
| [ESLint](https://eslint.org/) 9 flat config | `resources/js/**` | `./dev theme-lint` |
| [Prettier](https://prettier.io/) | JS + CSS | `./dev theme-format` |
| [blade-formatter](https://github.com/shufo/blade-formatter) | `resources/views/**/*.blade.php` | `./dev blade-format` |
| [Pest](https://pestphp.com/) | PHP tests | `./dev test` |

---

## Production (GCP)

### 0. Authenticate and configure project

Install the [Google Cloud CLI](https://cloud.google.com/sdk/docs/install) if you haven't already, then:

```bash
# Log in with your Google account
gcloud auth login

# Set the active project
gcloud config set project client-jussimatic

# Authorise Docker to push to Artifact Registry in europe-north1
gcloud auth configure-docker europe-north1-docker.pkg.dev

# Enable the required GCP APIs (one-time per project)
gcloud services enable \
  cloudbuild.googleapis.com \
  run.googleapis.com \
  artifactregistry.googleapis.com \
  secretmanager.googleapis.com
```

### 1. Create Artifact Registry repository

```bash
gcloud artifacts repositories create jussipress-ai \
  --repository-format=docker \
  --location=europe-north1 \
  --description="JussiPress AI container images"
```

### 2. Connect the repository to Cloud Build

Create a trigger in the GCP Console (or via `gcloud`) pointing to your repository and using `cloudbuild.yaml` as the build config. The default substitution variables are:

| Variable | Default | Description |
|---|---|---|
| `_REGION` | `europe-north1` | GCP region |
| `_REPO` | `jussipress-ai` | Artifact Registry repo name |
| `_IMAGE` | `app` | Image name within the repo |
| `_SERVICE` | `jussipress-ai` | Cloud Run service name |

### 3. Build and push the image

Manually or via the Cloud Build trigger:

```bash
gcloud builds submit \
  --config=cloudbuild.yaml \
  --substitutions=_REGION=europe-north1
```

Kaniko caches layers in Artifact Registry between builds — subsequent builds are significantly faster.

### 4. Deploy to Cloud Run

Uncomment the deploy step in `cloudbuild.yaml`, or deploy manually:

```bash
gcloud run deploy jussipress-ai \
  --image=europe-north1-docker.pkg.dev/client-jussimatic/jussipress-ai/app:latest \
  --region=europe-north1 \
  --platform=managed \
  --allow-unauthenticated \
  --min-instances=0 \
  --max-instances=3 \
  --memory=512Mi \
  --cpu=1 \
  --port=8080 \
  --set-env-vars="WP_ENV=production,DB_HOST=...,DB_NAME=...,DB_USER=...,DB_PASSWORD=..."
```

> Set all sensitive values (`DB_PASSWORD`, WordPress salts, etc.) as [Cloud Run secrets](https://cloud.google.com/run/docs/configuring/secrets) rather than plain environment variables.

### 5. Uploads / media

Persistent media uploads (`web/app/uploads/`) must be stored on an external volume since Cloud Run containers are stateless. Mount a [Cloud Storage FUSE](https://cloud.google.com/run/docs/tutorials/network-filesystems-fuse) bucket or use an offload plugin (e.g. WP Offload Media) pointing to a GCS bucket.

---

## Project structure

```
.
├── config/                     # WordPress environment config
│   └── environments/           # development.php, staging.php
├── docker/                     # nginx, php.ini, supervisord configs
├── web/                        # Document root
│   ├── app/
│   │   ├── mu-plugins/         # Must-use plugins
│   │   ├── plugins/            # Composer-managed plugins
│   │   ├── themes/
│   │   │   └── jussipress-theme/   # Sage theme
│   │   │       ├── app/            # PHP — providers, composers
│   │   │       ├── resources/
│   │   │       │   ├── css/        # Tailwind CSS
│   │   │       │   ├── js/         # ES modules
│   │   │       │   └── views/      # Blade templates
│   │   │       └── public/         # Built assets (gitignored)
│   │   └── uploads/            # Media (gitignored, volume-mounted)
│   └── wp/                     # WordPress core (gitignored, Composer-managed)
├── Dockerfile                  # Multi-stage production image
├── docker-compose.yml          # Local dev services
├── cloudbuild.yaml             # GCP Cloud Build + Kaniko
├── dev                         # Docker / tooling helper script
└── phpstan.neon                # PHPStan configuration
```
