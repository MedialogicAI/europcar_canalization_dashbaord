# PHP 7.4 → 8.3 Migration Plan

> **Status: ✅ EXECUTED.** All 10 steps complete. Live since 2026-05-14 on PHP 8.3.31. Two post-sign-off regressions found and fixed (CI deprecation HTML leak, Excel export perms). See [MIGRATION_NOTES.md](MIGRATION_NOTES.md) for the running status, final test results, and what was actually done vs originally planned. This file is preserved as the historical plan and reference.

**Scope:** PHP version upgrade only. No UI changes. No refactors. No feature changes. Just make the existing app run on 8.3 and prove it still works.

## Current state

| Item | Value |
|---|---|
| PHP installed | 7.4.33 (sury build) — only version present |
| Apache PHP module | `libapache2-mod-php7.4` |
| Primary app | `dashboard2/` (CodeIgniter 3.1.10) |
| Our application code | 16 controllers + 1 model in `dashboard2/ci/application/` |
| Vendored libs | CodeIgniter 3.1.10 system/, PHPExcel 1.x at `dashboard2/src/phpexcel/` |
| DB | `127.0.0.1` (already switched) |
| Backup | `/home/ataraxia/backups/public_html_pre_php83_20260514_101406.tar.gz` (542MB, sha256 `552b094e...984a6`) |
| Git rollback point | tag `pre-php83-migration` at commit `ef47980` on `main` |

## What the audit found (PHPCompatibility, testVersion=8.3)

449 findings across 65 files. **The location of these findings determines our strategy:**

| Bucket | Findings | Files | Fix |
|---|---|---|---|
| `dashboard2/src/phpexcel/` (PHPExcel 1.x) | ~200 | curly braces, `set_magic_quotes_runtime` in pclzip | Auto-fix with `phpcbf` + 1-2 hand patches |
| `dashboard2/ci/system/` (CodeIgniter 3.1.10) | ~200 | `mysql_`, `mcrypt`, `mbstring.func_overload`, ibase/mssql/sqlite drivers | **Upgrade to CI 3.1.13** (drop-in — `system/` only, `application/` untouched) |
| Our application code | ~0 in top-20 | — | Likely none; verify after step 3 |

**The vast majority of fixes are upgrading two vendored libraries, not hand-patching code.**

## Plan (10 steps)

### Step 1 — Install PHP 8.3 alongside 7.4 (Apache stays on 7.4)
- `sudo apt install php8.3 php8.3-{cli,mysql,gd,mbstring,xml,curl,zip,intl,bcmath,opcache} libapache2-mod-php8.3`
- Confirm `php8.3 -v` works
- Do **not** enable mod-php8.3 yet; site stays on 7.4

### Step 2 — Build smoke-test catalog (test cases)
Enumerate the actual URLs the app uses. Stored in `tests/smoke_urls.txt`:
- Login flow: GET `/login.php`, POST `/login.php`
- Main dashboard: GET `/dashboard2/`
- CI route hits: each of the 16 controllers' index action (where reachable)
- API endpoints: `/api/` known endpoints
- Static/auxiliary: `/dbtest.php` (will be removed post-migration), `/index.php`

For each: expected HTTP status, expected substring in response, expected absence of `Fatal error|Parse error|Uncaught|Deprecated` markers.

### Step 3 — Baseline pass under PHP 7.4
Run the smoke catalog against the live site (still on 7.4). Record the **baseline set** of URLs that pass today. **Only these have to keep passing post-migration.** If anything is already broken on 7.4, it's not our responsibility to fix during this migration.

### Step 4 — Upgrade CodeIgniter 3.1.10 → 3.1.13 (both `dashboard2/` and `dashboard2_old/`)
- Download CI 3.1.13 archive
- Replace `dashboard2/ci/system/` only (NOT `application/`, NOT `index.php`, NOT config files)
- Diff for any backward-incompatible behavior (CI 3.1.10 → 3.1.13 is patch-level, expected clean)
- Same for `dashboard2_old/` (in case it's still hit)
- Commit: "Upgrade CodeIgniter system/ from 3.1.10 to 3.1.13 (PHP 8 compat)"

### Step 5 — Auto-fix PHPExcel curly braces with phpcbf
- `phpcbf --standard=PHPCompatibility --runtime-set testVersion 8.3 dashboard2/src/phpexcel/`
- Manually fix the remaining 1-2 PHPExcel findings (`set_magic_quotes_runtime` in `pclzip.lib.php` — wrap in `if (function_exists())`)
- Commit: "Fix PHPExcel curly-brace syntax + magic_quotes for PHP 8.3"

### Step 6 — Hand-fix any remaining audit findings in OUR code
- Re-run `php83_audit.sh` after steps 4-5; expect a much smaller set
- Hand-fix anything left in `dashboard2/ci/application/` or root-level PHP
- Commit each cluster as its own commit

### Step 7 — Static verification under PHP 8.3
- `find . -name '*.php' -not -path './pma/*' -not -path '*/vendor/*' | xargs -n1 php8.3 -l` — every file must parse clean
- Capture any `PHP Parse error:` lines and fix before proceeding

### Step 8 — Smoke tests under PHP 8.3 (CLI)
- Use `php8.3 -S 127.0.0.1:8083 -t /home/ataraxia/public_html` as a separate test server
- Run the smoke catalog from step 2 against port 8083
- Compare against the 7.4 baseline: **every baseline-passing URL must pass on 8.3**

### Step 9 — Cutover Apache to PHP 8.3
- `sudo a2dismod php7.4 && sudo a2enmod php8.3 && sudo systemctl reload apache2`
- Re-run smoke catalog against the live site (now on 8.3)
- **Rollback plan if anything fails:** `sudo a2dismod php8.3 && sudo a2enmod php7.4 && sudo systemctl reload apache2` — and we're back on 7.4 instantly. PHP 7.4 stays installed until we're confident.

### Step 10 — Final audit + cleanup
- Re-run `php83_audit.sh` under 8.3 — should be 0 findings in `dashboard2/ci/application/`, root-level PHP, and `api/`
- Document any remaining notices/warnings in `MIGRATION_NOTES.md`
- Push final state to GitHub
- Keep PHP 7.4 installed for 7 days as safety net before removing

## Test cases (concrete)

Stored in `tests/smoke_urls.txt` and exercised by `tests/run_smoke.sh`:

```
# format: METHOD URL EXPECTED_STATUS EXPECTED_SUBSTRING_OR_DASH
GET / 200 -
GET /login.php 200 password
GET /dashboard2/ 200 -
GET /dashboard2/ci/ 200 -
GET /api/ 200 -
```

Test rules:
1. HTTP status matches expected.
2. Response does NOT contain: `Fatal error`, `Parse error`, `Uncaught`, `PHP Warning`, `PHP Notice`.
3. Response contains `EXPECTED_SUBSTRING` (if specified).

`run_smoke.sh` runs the catalog twice (once against current site, once against PHP 8.3 test server on :8083) and prints a diff.

## Explicit non-goals

- **No** UI changes
- **No** removing the legacy ibase/mssql/sqlite CI drivers (they'll be in CI 3.1.13 too; gone-vendored, harmless)
- **No** PHPExcel → PhpSpreadsheet migration (just fix in place)
- **No** mysqli → PDO migration
- **No** composer changes outside of the CI upgrade itself
- **No** opportunistic deprecation cleanup beyond what 8.3 strictly requires
- **No** test framework adoption (PHPUnit etc.) — smoke tests are bash + curl

## Risk register

| Risk | Mitigation |
|---|---|
| Apache mod-php conflict (only one version active at a time) | Confirmed; switch atomically with a2dismod/a2enmod |
| CI 3.1.13 silently changes session/cookie behavior | Read CI changelog 3.1.10→3.1.13; test login flow specifically |
| PHPExcel still throws E_DEPRECATED notices on 8.3 | Acceptable — notices, not fatals. Suppress in `error_reporting` if noisy |
| `dashboard2-18-03-2024.zip` artifact references old code | Not loaded at runtime; ignore |
| `pma/` (phpMyAdmin) breaks under 8.3 | Out of scope. If you need it post-migration, install the latest pma from apt |

## Sign-off checklist before declaring done

- [ ] All baseline-passing smoke URLs pass under PHP 8.3
- [ ] `php83_audit.sh` shows 0 findings in `dashboard2/ci/application/`, `api/`, and root-level PHP
- [ ] Login flow works end-to-end
- [ ] At least one DB write flow works (insert + read back)
- [ ] No `PHP Fatal` or `PHP Parse` lines in Apache error log over a 10-minute live test window
- [ ] Rollback procedure tested at least once

