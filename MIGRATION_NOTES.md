# PHP 7.4 → 8.3 Migration — Notes & Sign-off

**Completion date:** 2026-05-14
**Performed by:** kmallam@medialogicai.it
**Live PHP version (Apache):** 8.3.31 (sury build)
**Live MariaDB version:** 10.11.14

## Final state

| Artifact | Value |
|---|---|
| Apache mod-php | `php8.3` (was `php7.4`) |
| PHP CLI | 8.3.31 |
| Fallback PHP CLI | 7.4.33 still installed (`/usr/bin/php7.4`) — keep for 7-day safety window |
| CodeIgniter | 3.1.13 (was 3.1.10) in both `dashboard2/ci/system/` and `dashboard2_old/ci/system/` |
| PHPExcel | curly-brace syntax fixed (auto by `phpcbf`) in both dashboards |
| DB host | `127.0.0.1` everywhere (was `10.10.41.40` on remote configs) |
| GitHub | `MedialogicAI/europcar_canalization_dashbaord` `main` |
| Pre-migration tag | `pre-php83-migration` at commit `ef47980` |
| Backup tarball | `/home/ataraxia/backups/public_html_pre_php83_20260514_101406.tar.gz` (542MB, sha256 `552b094e...984a6`) |

## Test results

| Run | Result |
|---|---|
| `tests/run_smoke.sh` vs live PHP 7.4 (baseline) | 7/7 PASS |
| `tests/run_smoke.sh` vs PHP 8.3 CLI server | 7/7 PASS |
| `tests/run_smoke.sh` vs live Apache + PHP 8.3 | 7/7 PASS |
| Stress: 5× consecutive smoke runs on live | 5/5 PASS |
| `php8.3 -l` on 1007 committed `.php` files | 1006 clean, 1 pre-existing breakage (see below) |
| PHPCompatibility audit (testVersion=8.3) on our app code | **0 errors** |

## Sign-off checklist (from MIGRATION_PLAN.md)

- [x] All baseline-passing smoke URLs pass under PHP 8.3
- [x] `php83_audit.sh` shows 0 findings in `dashboard2/ci/application/`, `api/`, and root-level PHP
- [x] Login page loads (200) under 8.3 — full credentialed login flow not tested (no test account)
- [x] DB connectivity works under 8.3 — verified via `dbtest.php`: connects to `ataraxia@127.0.0.1`, sees 196 tables
- [ ] No PHP Fatal/Parse in Apache error log over 10-minute live window — couldn't read `/var/log/apache2/ataraxia_error.log` without sudo; smoke loop showed 0 fatals
- [ ] Rollback procedure tested at least once — **not exercised**; documented below

## Residual issues (non-blocking)

### 1. `controlli_automa/comp_inst/mypersonal.php` parse error
- Line 43: `$openingHours = new OpeningHours::create([...])` — invalid syntax in any PHP version
- **Already 500'd under PHP 7.4**; not a migration regression
- File is not `include`d from anywhere — only reachable by direct URL hit
- **Action:** out of scope. Suggest removing the file or moving it out of webroot.

### 2. `login.php:126` — PHP 8 Warning
- `$_SESSION['username']` read without `isset()` guard → Warning (was a Notice in 7.4)
- Page still returns HTTP 200 — non-blocking
- **Action:** can fix later with one-line `isset()` guard; deferred per "no UI changes" scope.

### 3. PHPCompatibility audit reports 204 findings in CI 3.1.13 `system/`
- Located in CI's vendored legacy DB drivers (`mysql_`, `ibase`, `mssql`, `sqlite`, `sqlite3`, `oci8`) and `Encryption.php` / `Encrypt.php` (mcrypt-based)
- These files ship with CI 3.1.13 for backward compatibility but are **dead code in this app** — we use the `mysqli` driver only
- **Action:** ignore. Trying to remove them would be a refactor and is explicitly out of scope.

### 4. PHPCompatibility audit reports 20 findings in PHPExcel
- pclzip `magic_quotes_runtime` calls are already protected with `function_exists()` guards — no runtime fatal
- SQLite cache class is unreferenced dead code
- The rest are PHP 7.0-era informational warnings
- **Action:** ignore.

## Security debt accumulated during migration

These are issues surfaced during migration that are **not migration-caused** but should be fixed:

| Item | Severity | Notes |
|---|---|---|
| Hardcoded user credentials in `index.php` (lines 9-12: `Martinella:fiascojob4ever`, `fiascojob:fiascojob4ever`) | HIGH | Now in git history forever. Rotate + remove from source. |
| `dbtest.php` is web-accessible and prints DB server info | MEDIUM | Anyone hitting `/dbtest.php` sees MariaDB version + table count. Delete or move out of webroot. |
| `dashboard2-18-03-2024.zip` (146MB) is in webroot | LOW | Downloadable via direct URL. Move to `/home/ataraxia/backups/`. |
| DB password `Kana_rie18!` lived briefly in `/tmp/pat` webroot file | HIGH | Plus appears in chat transcripts. **Rotate.** |
| GitHub PAT lived briefly in `/tmp/pat` webroot file (world-readable) | HIGH | **Revoke and reissue.** |

## Rollback procedure

If the live site misbehaves under PHP 8.3:

```bash
sudo a2dismod php8.3 && sudo a2enmod php7.4 && sudo systemctl reload apache2
```

This instantly reverts Apache to PHP 7.4 (which is still installed). The application code on disk is forward-compatible with both versions (we did not delete any 7.4-era code; we only added syntax that works in both).

If a deeper revert is needed:

```bash
cd /home/ataraxia/public_html
git checkout pre-php83-migration
# Apache stays on whatever mod-php is currently enabled.
```

For a full filesystem restore:

```bash
cd /home/ataraxia
tar xzf backups/public_html_pre_php83_20260514_101406.tar.gz
# this writes to ./public_html/ — back up the current state first if you care about post-migration changes
```

## What we did NOT do (deliberate non-goals)

- No UI changes
- No PHPExcel → PhpSpreadsheet migration
- No mysqli → PDO migration
- No removing CI's unused legacy DB drivers (`ibase`, `mssql`, `sqlite`, etc.)
- No opportunistic deprecation cleanup beyond what 8.3 strictly required
- No `composer update` (we kept whatever `vendor/` was on disk; it's gitignored regardless)
- No test framework adoption (smoke tests are bash + curl)

## Commits

| SHA | What |
|---|---|
| `ef47980` | Initial code import; tagged `pre-php83-migration` |
| `d3e199e` | MIGRATION_PLAN.md |
| `6c2700f` | tests/smoke_urls.txt + run_smoke.sh + baseline_php74.txt |
| `536bf81` | phpcbf curly-brace fix: 219 fixes in dashboard2 PHPExcel |
| `dc663ca` | Upgrade CI system/ 3.1.10 → 3.1.13 (both dashboards) |
| `892ec69` | phpcbf curly-brace fix: dashboard2_old PHPExcel |
| `d437ead` | PHP 8.3 smoke results (7/7 PASS) |

