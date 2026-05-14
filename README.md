# Europcar Canalization Dashboard

Internal dashboard for Europcar's canalization / channeling workflow, built for [Medialogic AI](https://medialogicai.it). Active production application running on PHP 8.3 + MariaDB.

## What this is

- **Frontend (`dashboard2/`)** — single-page ExtJS application (`dashboard2/index.html` + `app.js`)
- **Backend (`dashboard2/ci/`)** — CodeIgniter 3.1.13 REST API serving the SPA
- **Legacy app (`dashboard2_old/`)** — kept on disk; not actively routed
- **Standalone API (`api/`)** — separate small DAO layer used by mail-reader and a few utility endpoints
- **Root-level PHP (`*.php`)** — pre-CI legacy auth pages (login.php, index.php, etc.) — see [Legacy entry points](TECHNICAL.md#legacy-root-level-php) for what's still used

## Quick start

### Prerequisites

| | |
|---|---|
| OS | Ubuntu 24.04 LTS (works on any Apache + PHP host) |
| PHP | 8.3 (`apt install php8.3 php8.3-{cli,mysql,gd,mbstring,xml,curl,zip,intl,bcmath,opcache}`) |
| Apache | 2.4 + `libapache2-mod-php8.3` |
| DB | MariaDB 10.x or MySQL 5.7+; database `ataraxia` |
| Browser | Anything modern; the SPA uses ExtJS classic |

### Local setup

```bash
# 1. Clone
git clone https://github.com/MedialogicAI/europcar_canalization_dashbaord.git
cd europcar_canalization_dashbaord

# 2. Configure DB connection (4 files — they're gitignored, see *.example for templates)
cp dbconf.php.example dbconf.php
cp dashboard2/ci/application/config/database.php.example dashboard2/ci/application/config/database.php
cp dashboard2_old/ci/application/config/database.php.example dashboard2_old/ci/application/config/database.php
cp api/classes/dao/parent/DBConnector.class.php.example api/classes/dao/parent/DBConnector.class.php
# Then edit each and set the real DB_HOST/DB_USER/DB_PASSWORD/DB_NAME

# 3. Point Apache at this dir
sudo tee /etc/apache2/sites-available/dashboard.conf <<EOF
<VirtualHost *:80>
    ServerName dashboard.local
    DocumentRoot $(pwd)
    <Directory $(pwd)>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF
sudo a2ensite dashboard && sudo systemctl reload apache2

# 4. Fix write perms (Apache runs as www-data; uploads/cache/logs must be writable)
sudo usermod -aG ataraxia www-data && sudo systemctl restart apache2
chmod g+ws dashboard2/uploads dashboard2/uploads/temp \
            dashboard2/ci/application/cache dashboard2/ci/application/logs \
            dashboard2_old/ci/application/cache dashboard2_old/ci/application/logs
```

Then open `http://dashboard.local/dashboard2/` in a browser.

### Smoke tests

```bash
./tests/run_smoke.sh http://127.0.0.1
# expected: 8/8 passed
```

The runner hits a fixed catalog (`tests/smoke_urls.txt`) and asserts:
- HTTP status matches the expected code
- Response body does **not** contain `Fatal error | Parse error | Uncaught | A PHP Error was encountered`
- Response body contains the expected substring (e.g. `"success":true` for the JSON-returning endpoint)

The PHP 7.4 baseline is frozen in `tests/baseline_php74.txt`; the post-migration PHP 8.3 result in `tests/result_php83.txt`.

## Documentation

| Doc | Read when |
|---|---|
| [TECHNICAL.md](TECHNICAL.md) | You need to understand architecture, routes, auth flow, DB, file ops, permissions model |
| [MIGRATION_PLAN.md](MIGRATION_PLAN.md) | You're doing another platform/version migration and want the structured approach we used for PHP 7.4 → 8.3 |
| [MIGRATION_NOTES.md](MIGRATION_NOTES.md) | You hit a regression and want to know whether it's a known-and-documented quirk |

## Production server

| | |
|---|---|
| Host | `automatan` (10.10.41.61, 10.23.10.61) |
| Webroot | `/home/ataraxia/public_html` |
| Apache vhost | `/etc/apache2/sites-enabled/ataraxia.conf` |
| DB host | `127.0.0.1` (same machine) |
| Pre-migration safety tag | `pre-php83-migration` (commit `ef47980`) |
| Pre-migration tarball | `/home/ataraxia/backups/public_html_pre_php83_20260514_101406.tar.gz` (542MB) |

## Rollback

Apache module swap (instant; PHP 7.4 stays installed for the safety window):

```bash
sudo a2dismod php8.3 && sudo a2enmod php7.4 && sudo systemctl reload apache2
```

Full filesystem restore from the pre-migration snapshot:

```bash
cd /home/ataraxia
tar xzf backups/public_html_pre_php83_20260514_101406.tar.gz
# overwrites ./public_html/ — back up the current state first if you want to keep it
```

## Security debt

These items predate the migration but were surfaced during it. **Address before forgetting:**

| Severity | Item |
|---|---|
| 🔴 | `index.php:9-12` has hardcoded plaintext credentials. Move out of source. |
| 🔴 | `dbtest.php` is web-accessible and prints DB server info. Delete or move out of webroot. |
| 🟡 | `dashboard2-18-03-2024.zip` (146MB) is in webroot — it's a downloadable URL right now. |
| 🟢 | `controlli_automa/comp_inst/mypersonal.php:43` has a parse error (was already 500'ing on PHP 7.4). |

## License

Proprietary — Medialogic AI. Not for public distribution.

## Contact

[Krishna Mallam](mailto:kmallam@medialogicai.it) — Medialogic AI
