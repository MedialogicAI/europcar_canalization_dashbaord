#!/usr/bin/env bash
# php83_audit.sh — run PHP 8.3 compatibility audit on .61
# Usage:
#     APP_DIR=/var/www/canalization ./php83_audit.sh
#   or:
#     ./php83_audit.sh /var/www/canalization
#
# Self-contained, no sudo, no system installs. All work happens in /tmp/php83-audit.
# Idempotent on re-run (skips composer install if vendor/bin/phpcs already exists).

set -uo pipefail

APP_DIR="${1:-${APP_DIR:-}}"
WORK="/tmp/php83-audit"
LOG="$WORK/audit.log"

banner() { printf '\n========== %s ==========\n' "$*"; }

if [[ -z "$APP_DIR" ]]; then
  echo "ERROR: APP_DIR not set." >&2
  echo "Usage: APP_DIR=/var/www/canalization $0     (or)    $0 /var/www/canalization" >&2
  exit 2
fi
if [[ ! -d "$APP_DIR" ]]; then
  echo "ERROR: APP_DIR '$APP_DIR' does not exist or is not a directory." >&2
  exit 2
fi

mkdir -p "$WORK" && cd "$WORK"
: > "$LOG"
exec > >(tee -a "$LOG") 2>&1

banner "Environment"
date -u +'UTC %Y-%m-%dT%H:%M:%SZ'
echo "host=$(hostname)  user=$(whoami)  pwd=$(pwd)"
echo "APP_DIR=$APP_DIR"

banner "PHP version & SAPI"
php -v || { echo "FATAL: php not on PATH"; exit 3; }
php -r 'echo PHP_VERSION . "\n" . PHP_SAPI . "\n";'

banner "Loaded PHP modules (CLI)"
php -m | sort

banner "PHP packages (best-effort, no sudo)"
{ command -v dpkg >/dev/null \
    && dpkg -l 2>/dev/null \
       | awk '/^ii / && $2 ~ /(php|libapache2-mod-php)/ {print $2, $3}'; } \
  || echo "(dpkg unavailable)"

banner "Apache & MySQL versions (best-effort)"
{ command -v apache2 >/dev/null && apache2 -v; } || echo "(apache2 not on PATH)"
{ command -v mysql   >/dev/null && mysql --version; } || echo "(mysql client not on PATH)"

banner "Application directory overview"
ls -la "$APP_DIR" | head -30
TOTAL_PHP=$(find "$APP_DIR" -type f \( -name '*.php' -o -name '*.inc' -o -name '*.phtml' \) 2>/dev/null | wc -l)
echo "PHP source files (.php/.inc/.phtml): $TOTAL_PHP"

banner "Composer"
if command -v composer >/dev/null 2>&1; then
  COMPOSER_BIN="composer"
  composer --version
else
  echo "Composer not on PATH — installing locally to $WORK/composer.phar"
  curl -sS https://getcomposer.org/installer \
    | php -- --install-dir="$WORK" --filename=composer.phar \
    || { echo "FATAL: failed to install composer.phar"; exit 4; }
  COMPOSER_BIN="php $WORK/composer.phar"
  $COMPOSER_BIN --version
fi

banner "Install PHPCompatibility (isolated, no sudo)"
if [[ ! -x "$WORK/vendor/bin/phpcs" ]]; then
  cat > "$WORK/composer.json" <<'JSON'
{
  "name": "med/php83-audit",
  "require-dev": {
    "squizlabs/php_codesniffer": "^3.10",
    "phpcompatibility/php-compatibility": "^9.3"
  },
  "minimum-stability": "stable"
}
JSON
  $COMPOSER_BIN install --no-interaction --no-progress --prefer-dist \
    || { echo "FATAL: composer install failed (proxy / no internet?)"; exit 5; }
else
  echo "phpcs already installed at $WORK/vendor/bin/phpcs (skipping install)"
fi

PHPCS="$WORK/vendor/bin/phpcs"
RULESET="$WORK/vendor/phpcompatibility/php-compatibility/PHPCompatibility/ruleset.xml"
"$PHPCS" --version
[[ -f "$RULESET" ]] || { echo "FATAL: PHPCompatibility ruleset missing at $RULESET"; exit 6; }

banner "Scan: PHPCompatibility / testVersion=8.3 (full report)"
"$PHPCS" -p -s \
  --standard="$RULESET" \
  --runtime-set testVersion 8.3 \
  --extensions=php,inc,phtml \
  --report=full \
  --report-file="$WORK/full-report.txt" \
  "$APP_DIR"
RC_FULL=$?
echo "phpcs exit code (0=clean, 1=warnings, 2=errors, 3=fatal): $RC_FULL"

banner "Summary by error code (source counts)"
"$PHPCS" \
  --standard="$RULESET" \
  --runtime-set testVersion 8.3 \
  --extensions=php,inc,phtml \
  --report=source \
  "$APP_DIR" | tee "$WORK/summary.txt"

banner "Histogram — top sniffs by frequency"
grep -hoE 'PHPCompatibility\.[A-Za-z0-9_.]+' "$WORK/full-report.txt" 2>/dev/null \
  | sort | uniq -c | sort -rn | head -30 \
  | tee "$WORK/histogram.txt"

banner "Files with most findings"
awk '/^FILE: /{f=$2} /\| (ERROR|WARNING) \|/{print f}' "$WORK/full-report.txt" 2>/dev/null \
  | sort | uniq -c | sort -rn | head -20

banner "First 80 finding lines (file:line | severity | message | source)"
grep -nE '\| (ERROR|WARNING) \|' "$WORK/full-report.txt" 2>/dev/null | head -80

banner "Done"
echo "Artifacts in $WORK:"
ls -la "$WORK"/{full-report.txt,summary.txt,histogram.txt,audit.log} 2>/dev/null

cat <<EOM

>>> Paste back to me:
>>>   1) the entire stdout above (it's also saved at $LOG), OR
>>>   2) at minimum: $WORK/summary.txt and $WORK/histogram.txt
>>> If summary.txt is huge, attach as a file rather than pasting.
EOM
