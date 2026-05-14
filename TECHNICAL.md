# Technical Documentation

Deep-dive on the Europcar Canalization Dashboard — architecture, request flow, file ops, permissions, and common operations.

> For setup and quick start, see [README.md](README.md).
> For migration history, see [MIGRATION_PLAN.md](MIGRATION_PLAN.md) and [MIGRATION_NOTES.md](MIGRATION_NOTES.md).

---

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│  Browser                                                        │
│  http://automatan/dashboard2/                                   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ 1. GET /dashboard2/index.html  (static SPA shell)
                              │ 2. GET /dashboard2/app.js      (static ExtJS bundle)
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  Apache 2.4 + libapache2-mod-php8.3 (runs as www-data)          │
│  vhost: /etc/apache2/sites-enabled/ataraxia.conf                │
│  DocumentRoot: /home/ataraxia/public_html                       │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ 3. POST /dashboard2/ci/<route>  (XHR from app.js)
                              │    .htaccess rewrites to /dashboard2/ci/index.php?/<route>
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  CodeIgniter 3.1.13                                             │
│  dashboard2/ci/index.php  (front controller)                    │
│  └─ application/config/routes.php   (route → controller/method) │
│  └─ application/controllers/*Controller.php  (16 controllers)   │
│  └─ application/config/database.php  (DB connection)            │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ 4. mysqli to 127.0.0.1:3306
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  MariaDB 10.11.14                                               │
│  database: ataraxia                                             │
│  ~196 tables (app_cfg, pratices, profiles, users, ...)          │
└─────────────────────────────────────────────────────────────────┘
```

### Request types

| URL prefix | Served by | Notes |
|---|---|---|
| `/` (root PHP) | Apache + mod-php directly | Legacy auth pages (login.php, index.php). Pre-CI. |
| `/dashboard2/index.html`, `/dashboard2/app.js`, `/dashboard2/resources/*` | Apache static | ExtJS SPA assets. No PHP involved. |
| `/dashboard2/ci/<route>` | CI front controller (index.php) | `.htaccess` mod_rewrite routes everything through `/dashboard2/ci/index.php?/<route>`. CI's router maps to `<Controller>/<method>`. |
| `/api/...` | Direct PHP execution | Small DAO layer; mail_reader, test scripts. |

---

## Directory map

| Path | Purpose |
|---|---|
| `dashboard2/` | Main app (active) |
| `dashboard2/index.html` | SPA shell loaded by browser |
| `dashboard2/app.js` | ExtJS bundle (~2.3MB) |
| `dashboard2/src/phpexcel/` | Bundled PHPExcel 1.x — used for `.xlsx` exports |
| `dashboard2/uploads/temp/` | Generated export files; written by web user |
| `dashboard2/ci/index.php` | CodeIgniter front controller |
| `dashboard2/ci/system/` | CodeIgniter 3.1.13 framework (do not modify) |
| `dashboard2/ci/application/controllers/` | Our 16 controllers |
| `dashboard2/ci/application/config/routes.php` | URL → controller/method map |
| `dashboard2/ci/application/config/database.php` | DB credentials (gitignored) |
| `dashboard2/ci/application/cache/` | CI query/output cache; web-writable |
| `dashboard2/ci/application/logs/` | CI error logs; web-writable; logging is OFF by default (`log_threshold=0`) |
| `dashboard2_old/` | Legacy dashboard; same shape; preserved for reference |
| `api/` | Standalone DAO; not wired through CI |
| `controlli_automa/` | Automation/control side scripts |
| `pma/` | phpMyAdmin (gitignored; for DB admin) |
| `vendor/` | Composer deps (gitignored) |
| `bower_components/` | Frontend deps (gitignored) |
| `tests/` | Smoke test catalog + bash runner |

---

## Routes (CodeIgniter)

Routes are defined in `dashboard2/ci/application/config/routes.php`. All require `POST` unless noted. Full path is `/dashboard2/ci/<route>` after `.htaccess` rewrite.

| Area | Sample routes |
|---|---|
| App config | `get_app_cfg`, `get_functions` |
| Auth | `login`, `logout`, `recovery`, `confirm/<id>/<token>` (GET) |
| Users | `users/list`, `users/add`, `users/edit`, `users/delete/<id>` (GET) |
| Profiles | `profiles/list`, `profiles/add`, `profiles/edit`, `profiles/del/<id>` (GET) |
| Home | `home/messages`, `home/reports` |
| Dashboard | `dashboard/daily/{total,pratices,irm}`, `dashboard/year/{total,pratices,irm}` |
| Pratices | `pratices/{daily,archive/{all,automated,manually,rejected},picked,notpicked,waiting}/list` (+ `/exportall` variants) |
| Robot | `robot/status`, `robot/switchon` (GET), `robot/switchoff` (GET), `robot/action/<name>` (GET) |
| Availability | `availability/getdata` |
| Exports | `export/pratices/excel`, `export/pratices/csv` |
| Reports | `reports/suppliers/permanence/avg` |
| Common | `common/{regions,provinces,defect,suppliers,stations}` |

**Controllers:** `AppController`, `AvailabilityController`, `CommonController`, `DashboardController`, `ExportController`, `HomeController`, `LoginController`, `MainController` (base for all auth-required controllers), `PraticesController`, `ProfilesController`, `ReportsController`, `RobotController`, `UsersController`, `ZoneController`.

---

## Authentication & session flow

1. SPA loads `/dashboard2/index.html` (static, no auth)
2. SPA POSTs to `get_app_cfg` → `AppController::getAppCfg()` returns app metadata (no auth required)
3. User submits login → SPA POSTs `login` → `LoginController::doLogin()`
   - On success, `$_SESSION['loggedUser']` is set (via CI's session library, file-based by default)
4. Subsequent requests carry CI's session cookie. Controllers extending `MainController` call `$this->isLogged()` in `__construct` (or per-method) to gate access
5. Failed auth returns `sendNotAuthorized()` (HTTP 403 + JSON `{"success":false,"msg":"accessFailed"}`)

CSRF protection is **off** (`csrf_protection = FALSE` in `config.php`). Sessions are file-based; see `ci/application/config/config.php` for `sess_*` settings.

---

## File operations

### Excel export flow (`ExportController::exportPraticesExcel`)

1. POST `dataexport` JSON to `/dashboard2/ci/export/pratices/excel`
2. Controller deserializes, instantiates `PHPExcel`, writes via `PHPExcel_Writer_Excel2007`
3. Writer creates a `.xlsx` (ZIP-based format) at `dashboard2/uploads/temp/export-<random>.xlsx`
4. Response JSON includes `download` URL
5. Frontend downloads via that URL (direct Apache static serve)

**Critical**: `ZipArchive` needs the target directory to be writable by the Apache user. See [Permissions](#permissions) below.

### Web-writable directories

| Path | Why writable |
|---|---|
| `dashboard2/uploads/temp/` | Excel/CSV exports |
| `dashboard2/uploads/` | Mid-flight upload artifacts |
| `dashboard2/ci/application/cache/` | CI query cache, view cache |
| `dashboard2/ci/application/logs/` | CI error log (currently disabled, but writable for future use) |
| `dashboard2_old/ci/application/cache/`, `logs/` | Same for the legacy dashboard |

---

## Permissions

Apache (mod-php) runs as `www-data`. The application user is `ataraxia`. Mixed ownership is handled with **group membership + setgid**:

```bash
# www-data must be in the ataraxia group:
sudo usermod -aG ataraxia www-data
sudo systemctl restart apache2

# Web-writable dirs: mode 2775 (group rwx + setgid so new files inherit ataraxia group):
chmod 2775 dashboard2/uploads dashboard2/uploads/temp \
            dashboard2/ci/application/cache dashboard2/ci/application/logs \
            dashboard2_old/ci/application/cache dashboard2_old/ci/application/logs
```

After this, files created by `www-data` are owned `www-data:ataraxia`, which `ataraxia` can still read/write.

If you ever see `Permission denied` writing under `uploads/temp/` or similar, check:

```bash
id www-data                          # must include ataraxia in groups
stat -c '%a %U:%G' dashboard2/uploads/temp   # must be 2775 (or 775 + ataraxia group)
```

---

## Database

| | |
|---|---|
| Engine | MariaDB 10.11.14 |
| Host | `127.0.0.1` (same server as Apache) |
| Database | `ataraxia` |
| User | `ataraxia` |
| Driver | `mysqli` (set in `dashboard2/ci/application/config/database.php`) |
| Table count | ~196 |

### Where credentials live (all 4 are gitignored; `.example` templates committed)

| File | Used by |
|---|---|
| `dbconf.php` | Root-level legacy PHP (`index.php`, `login.php`, `funzioni-dash.php`, fetcher scripts) |
| `dashboard2/ci/application/config/database.php` | dashboard2 CI |
| `dashboard2_old/ci/application/config/database.php` | dashboard2_old CI |
| `api/classes/dao/parent/DBConnector.class.php` | api/ standalone DAO |

### Direct DB access for debugging

```bash
mysql -u ataraxia -p ataraxia          # interactive
mysql -u ataraxia -p ataraxia -e "SHOW TABLES" | head
```

Or via phpMyAdmin at `/pma/` (vendored, gitignored, served by Apache).

---

## CodeIgniter quirks (PHP 8.3 specific)

CI 3.1.13 still uses **dynamic property creation** internally (`$this->benchmark`, `$this->router`, `$this->db`, etc. on `CI_Controller`). PHP 8.2+ emits `E_DEPRECATED` for every dynamic property. CI's `_error_handler` catches these and renders each as an HTML `<div>` block — which would prepend JSON API responses and break `JSON.decode` in the SPA.

**Mitigation** (already applied, see `dashboard2/ci/index.php` line ~70):

```php
case 'development':
    // PHP 8.2+ deprecates dynamic property creation, which CI 3.1.13 still
    // triggers internally. Excluding E_DEPRECATED keeps real errors visible
    // while preventing CI's error_handler from polluting JSON responses.
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
    ini_set('display_errors', 1);
break;
```

**Do not revert** this to `error_reporting(-1)` — it breaks every JSON endpoint.

---

## Legacy root-level PHP

These pre-date the CI dashboard. Most are still wired into Apache (no rewrite):

| File | What it does |
|---|---|
| `index.php` | Legacy auth landing page; has hardcoded creds (see security debt in README) |
| `login.php` | Legacy login form; reads `dbconf.php` |
| `dash.php` | Old dashboard page |
| `downloader.php`, `request.php` | Helper endpoints |
| `funzioni-dash.php` | Shared functions (required by many root files) |
| `dbconf.php` | DB credentials for the legacy stack |
| `dbtest.php` | DB connection smoke test (security risk — see README) |
| `test_active.php` | Trivial ping (returns `1`) — useful for monitors |
| `*-pag.php`, `not_used_*`, `top_not_used`, `user_*` | Page fragments (HTML; included by other files) |

The CI dashboard (`/dashboard2/`) is the active app for new work. The root-level pages are kept because legacy URLs still hit them.

---

## Smoke test runner

`tests/run_smoke.sh BASE_URL`

- Reads `tests/smoke_urls.txt`
- For each line `METHOD<TAB>PATH<TAB>EXPECTED_STATUS<TAB>EXPECTED_SUBSTR`:
  1. `curl -X METHOD BASE_URL$PATH`
  2. Assert HTTP status matches expected
  3. Assert body does NOT contain `Fatal error|Parse error|Uncaught |A PHP Error was encountered`
  4. Assert body contains `EXPECTED_SUBSTR` (or `-` to skip)
- Exit code = number of failing URLs

Add a URL to the catalog whenever you ship a new public endpoint. The runner's job is to catch the kind of regressions CI's HTML deprecation leak caused (silent breakage of JSON parsing in the SPA).

---

## Common operations

### Tail Apache error log
```bash
sudo tail -f /var/log/apache2/ataraxia_error.log
```

### Enable CI error logging (for debugging only)
Edit `dashboard2/ci/application/config/config.php`:
```php
$config['log_threshold'] = 4;     // 1=all, 4=errors only
$config['log_path'] = '';         // empty = uses application/logs/
```
Then check `dashboard2/ci/application/logs/log-YYYY-MM-DD.php`.

Don't leave logging at threshold 1 in production — it generates one file per day and grows fast.

### Restart Apache
```bash
sudo systemctl reload apache2     # graceful, picks up most config changes
sudo systemctl restart apache2    # full restart, needed for group membership changes
```

### Switch PHP version
```bash
# To PHP 7.4:
sudo a2dismod php8.3 && sudo a2enmod php7.4 && sudo systemctl reload apache2

# To PHP 8.3:
sudo a2dismod php7.4 && sudo a2enmod php8.3 && sudo systemctl reload apache2
```

### Backup before risky changes
```bash
cd /home/ataraxia
tar czf backups/public_html_$(date +%Y%m%d_%H%M%S).tar.gz public_html
```

---

## Gotchas

| Symptom | Likely cause | Where to check |
|---|---|---|
| Blank dashboard, console shows `Uncaught Error: invalid JSON String` | CI emitting deprecation HTML into JSON. Check `error_reporting` in `dashboard2/ci/index.php`. | `dashboard2/ci/index.php:70` |
| Excel export → HTTP 500 | `www-data` can't write to `uploads/temp/`. | `id www-data` should include `ataraxia`; `stat dashboard2/uploads/temp` should show mode 2775 |
| `Could not connect to database` | `dbconf.php` / CI database.php missing real credentials | Files are gitignored; copy from `.example` and fill in |
| `404 Page Not Found` on all CI routes | mod_rewrite disabled, or `.htaccess` not loaded | `sudo a2enmod rewrite`; vhost must have `AllowOverride All` |
| CI 404 page renders but contains visible PHP notices | `display_errors` is on AND `error_reporting` includes the notice level | See "CodeIgniter quirks" above |
