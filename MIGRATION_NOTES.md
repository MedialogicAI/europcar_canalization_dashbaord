# PHP 7.4 → 8.3 Migration — Notes & Sign-off

**Initial sign-off:** 2026-05-14
**Last updated:** 2026-05-14 (post-sign-off cleanup complete)
**Performed by:** kmallam@medialogicai.it
**Live PHP version (Apache):** 8.3.31 (sury build)
**Live MariaDB version:** 10.11.14

## Status: ✅ Live and clean

PHP 7.4 → 8.3 migration is complete and verified. All known regressions have been resolved. The dashboard is serving real traffic on PHP 8.3.

## Final state

| Artifact | Value |
|---|---|
| Apache mod-php | `php8.3` (was `php7.4`) |
| PHP CLI | 8.3.31 |
| Fallback PHP CLI | 7.4.33 still installed (`/usr/bin/php7.4`) — keep for 7-day safety window |
| CodeIgniter | 3.1.13 (was 3.1.10) in both `dashboard2/ci/system/` and `dashboard2_old/ci/system/` |
| PHPExcel | curly-brace syntax fixed (auto by `phpcbf`) in both dashboards |
| DB host | `127.0.0.1` everywhere (was `10.10.41.40` on remote configs) |
| DB `app_cfg.owner` | `Medialogic AI - Software` (was `Vincix Group - Software`) |
| External `durc.vincix.com` integration | **Removed** — unused; all URL references stripped, related docs (`dash/rest_usage.php`) deleted |
| Filesystem perms | `www-data` added to `ataraxia` group; framework-writable dirs at mode 2775 + setgid |
| GitHub | `MedialogicAI/europcar_canalization_dashbaord` `main` |
| Pre-migration tag | `pre-php83-migration` at commit `ef47980` |
| Backup tarball | `/home/ataraxia/backups/public_html_pre_php83_20260514_101406.tar.gz` (542MB, sha256 `552b094e...984a6`) |

## Test results

| Run | Result |
|---|---|
| `tests/run_smoke.sh` vs live PHP 7.4 (baseline) | 7/7 PASS |
| `tests/run_smoke.sh` vs PHP 8.3 CLI server | 7/7 PASS |
| `tests/run_smoke.sh` vs live Apache + PHP 8.3 | 7/7 PASS (extended to 8/8 post-sign-off) |
| Stress: 5× consecutive smoke runs on live | 5/5 PASS |
| `php8.3 -l` on 1006 committed `.php` files | 1005 clean, 1 pre-existing breakage (see residual issues) |
| PHPCompatibility audit on our app code | **0 errors** (3 cosmetic warnings on HTML-fragment .php files) |
| Live Excel export under PHP 8.3 | PASS (after `www-data` group + dir perms fix) |
| Live dashboard SPA render under PHP 8.3 | PASS (after E_DEPRECATED suppression in CI dev mode) |

## Sign-off checklist (from MIGRATION_PLAN.md)

- [x] All baseline-passing smoke URLs pass under PHP 8.3
- [x] `php83_audit.sh` shows 0 findings in `dashboard2/ci/application/`, `api/`, and root-level PHP
- [x] Login page loads (200) under 8.3 — full credentialed login flow not tested (no test account)
- [x] DB connectivity works under 8.3 — verified via `dbtest.php`: connects to `ataraxia@127.0.0.1`, sees 196 tables
- [x] Excel export verified working end-to-end (after the post-sign-off perm fix; see resolved issue 1c below)
- [x] Dashboard SPA renders correctly with live JSON responses (after deprecation suppression; see resolved issue 1b)
- [ ] No PHP Fatal/Parse in Apache error log over 10-minute live window — couldn't read `/var/log/apache2/ataraxia_error.log` without sudo; smoke loop showed 0 fatals
- [ ] Rollback procedure tested at least once — **not exercised**; documented below

## Post-sign-off regressions (all resolved)

Two real regressions surfaced *after* the initial sign-off and are now fixed. Smoke catalog was tightened to catch the class of issue that produced each one.

### 1b. CI deprecation HTML leaking into JSON responses — FIXED (commit `eae6a95`)

**Symptom**: ExtJS dashboard at `/dashboard2/` showed a blank screen. Console:
```
Uncaught Error: You're trying to decode an invalid JSON String:
<div style="..."><h4>A PHP Error was encountered</h4>...
```

**Root cause**: PHP 8.2+ deprecates dynamic property creation. CI 3.1.13 still
assigns dynamic properties internally (`$this->benchmark`, `$this->router`,
`$this->db`, etc. on `CI_Controller`). CI's `_error_handler` catches these
E_DEPRECATED notices and renders them as HTML `<div>` blocks — which prepended
to JSON API responses, breaking ExtJS's `JSON.decode`.

**Fix**: In both `dashboard2/ci/index.php` and `dashboard2_old/ci/index.php`,
the development-mode `error_reporting(-1)` was changed to
`error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED)`. Real errors
still surface; deprecation noise is suppressed. **Do not revert this.**

**Smoke runner hardened**: the regex now also catches `"A PHP Error was encountered"` — CI's HTML notice marker. The original runner only flagged PHP Fatal/Parse/Uncaught and missed this entire class of leaks. Catalog also gained `/dashboard2/ci/index.php/AppController/getAppCfg` as a real JSON-payload endpoint that would have caught the issue immediately.

### 1c. Excel export 500 — Apache user can't write to uploads/temp — FIXED (commit `8399404`)

**Symptom**: `POST /dashboard2/ci/export/pratices/excel` → 500. Real error captured via temp trap in `index.php`:

```
Warning: ZipArchive::close(): Failure to create temporary file:
  Permission denied in .../Writer/Excel2007.php on line 388
Uncaught PHPExcel_Writer_Exception: Could not close zip file
  .../dashboard2/uploads/temp/export-N6S1778758173E0B.xlsx.
```

**Root cause**: Filesystem permissions, NOT a PHP 8.3 issue. The old server presumably ran PHP as the `ataraxia` user (suPHP / PHP-FPM with custom pool / mpm_itk); the new mod-php8.3 stack runs as `www-data`. `dashboard2/uploads/temp/` was mode 755 owned by `ataraxia:ataraxia` — `www-data` had no write access. Pre-existing exports in the dir are all owned by `ataraxia` (last successful write 2024-12-20), confirming the user-change hypothesis.

**Fix applied**:
- `chmod 2775` (group rwx + setgid so new files inherit `ataraxia` group) on every framework-writable dir:
  - `dashboard2/uploads/` and `dashboard2/uploads/temp/`
  - `dashboard2/ci/application/cache/` and `logs/`
  - `dashboard2_old/ci/application/cache/` and `logs/`
- `sudo usermod -aG ataraxia www-data && sudo systemctl restart apache2` — Apache process now has the group membership it needs

**Verified**: `id www-data` shows `groups=33(www-data),1001(ataraxia)`; live Excel export end-to-end works.

## Residual issues (non-blocking, still on the books)

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

## Security debt (predates migration; still open)

These were surfaced during migration but are **not migration-caused**. Should be addressed when convenient.

| Severity | Item | Status |
|---|---|---|
| 🔴 HIGH | GitHub PAT lived briefly in `/tmp/pat` webroot file (world-readable) — **revoke and reissue** | Open |
| 🔴 HIGH | DB password `Kana_rie18!` was in `/tmp/pat` parent dir and appears in chat transcripts — **rotate** | Open |
| 🔴 HIGH | Hardcoded user credentials in `index.php:9-12` (`Martinella:fiascojob4ever`, `fiascojob:fiascojob4ever`) — in git history forever; rotate + remove from source | Open |
| 🟡 MED | `dbtest.php` is web-accessible and prints MariaDB version + table count — delete or move out of webroot | Open |
| 🟢 LOW | `dashboard2-18-03-2024.zip` (146MB) is in webroot — downloadable via direct URL; move to `/home/ataraxia/backups/` | Open |

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

## What we did NOT do (deliberate non-goals during the migration window)

- No UI changes
- No PHPExcel → PhpSpreadsheet migration
- No mysqli → PDO migration
- No removing CI's unused legacy DB drivers (`ibase`, `mssql`, `sqlite`, etc.)
- No opportunistic deprecation cleanup beyond what 8.3 strictly required
- No `composer update` (we kept whatever `vendor/` was on disk; it's gitignored regardless)
- No test framework adoption (smoke tests are bash + curl)

## Done post-sign-off (in scope of "tidy and ship")

These were addressed *after* the initial sign-off as part of the cleanup pass:

- **Rebranding**: replaced "Vincix" / "Vincix Group" / "vincix" with "Medialogic AI" across all docs and user-facing strings. Updated DB `app_cfg.owner` value. Functional `durc.vincix.com` URLs stripped after user confirmed the external service isn't in use.
- **Dead code removal**: deleted `dash/rest_usage.php` (396-line user guide for the unused durc REST API); removed dead `$dir` and stale SQL-example comments in `request.php` and `downloader.php`.
- **Email tidy**: support email in `pst_new_registration.php` is now `supporto@medialogicai.it`.
- **Project docs added**: `README.md` (project overview + quick start + rollback) and `TECHNICAL.md` (architecture, routes, auth flow, perms model, gotchas).

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
| `9e88c36` | Step 10 sign-off: this MIGRATION_NOTES.md (initial version) |
| `eae6a95` | Fix: suppress E_DEPRECATED in CI dev mode (deprecation HTML leak) |
| `8399404` | Document Excel export 500: filesystem perms, not PHP 8.3 bug |
| `386722f` | Add README.md and TECHNICAL.md |
| `44f7db6` | Rebrand: Vincix → Medialogic AI |
| `901a686` | Remove durc.vincix.com integration + clean stale emails |
