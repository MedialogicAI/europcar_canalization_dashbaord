#!/usr/bin/env bash
# Smoke-test runner for the PHP 7.4 -> 8.3 migration.
# Reads tests/smoke_urls.txt and exercises each URL against $BASE_URL.
#
# Usage:
#   ./run_smoke.sh                       # defaults to http://127.0.0.1
#   ./run_smoke.sh http://127.0.0.1:8083 # against the PHP 8.3 CLI test server
#   BASE_URL=... ./run_smoke.sh
#
# Output: one TAB-separated line per URL:
#   PASS|FAIL<TAB>STATUS<TAB>METHOD<TAB>PATH<TAB>reason
# Exit code: number of failed URLs (0 = all passed).

set -u

BASE_URL="${1:-${BASE_URL:-http://127.0.0.1}}"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
CATALOG="$SCRIPT_DIR/smoke_urls.txt"
TMP_BODY="$(mktemp)"
trap 'rm -f "$TMP_BODY"' EXIT

if [[ ! -f "$CATALOG" ]]; then
  echo "ERROR: catalog not found at $CATALOG" >&2
  exit 99
fi

FAILS=0
TOTAL=0

printf 'BASE_URL=%s\n' "$BASE_URL" >&2
printf '%-4s\t%-3s\t%-6s\t%s\n' RESULT HTTP METHOD URL >&2

while IFS=$'\t' read -r METHOD PATH_ EXPECTED_STATUS EXPECTED_SUBSTR; do
  # skip blanks and comments
  [[ -z "${METHOD// }" || "${METHOD#\#}" != "$METHOD" ]] && continue
  TOTAL=$((TOTAL+1))

  HTTP_CODE=$(curl -s -o "$TMP_BODY" -w "%{http_code}" -X "$METHOD" "$BASE_URL$PATH_") || HTTP_CODE="000"

  reason=""
  status_ok=0
  body_ok=0
  substr_ok=0

  if [[ "$HTTP_CODE" == "$EXPECTED_STATUS" ]]; then
    status_ok=1
  else
    reason="status=$HTTP_CODE expected=$EXPECTED_STATUS"
  fi

  # PHP error markers must NOT be in the body.
  # "A PHP Error was encountered" is CodeIgniter's HTML-rendered notice
  # block — it leaks deprecation/warning HTML into JSON API responses on
  # PHP 8.2+ and breaks anything that parses the response as JSON.
  if grep -qE "Fatal error|Parse error|Uncaught |A PHP Error was encountered" "$TMP_BODY"; then
    body_ok=0
    err_line=$(grep -nE "Fatal error|Parse error|Uncaught |A PHP Error was encountered" "$TMP_BODY" | head -1 | cut -c1-120)
    [[ -n "$reason" ]] && reason="$reason; "
    reason="${reason}php_error=$err_line"
  else
    body_ok=1
  fi

  # Required substring (if any)
  if [[ "$EXPECTED_SUBSTR" == "-" ]]; then
    substr_ok=1
  elif grep -qF -- "$EXPECTED_SUBSTR" "$TMP_BODY"; then
    substr_ok=1
  else
    [[ -n "$reason" ]] && reason="$reason; "
    reason="${reason}missing_substr=\"$EXPECTED_SUBSTR\""
  fi

  if (( status_ok && body_ok && substr_ok )); then
    printf 'PASS\t%s\t%s\t%s\n' "$HTTP_CODE" "$METHOD" "$PATH_"
  else
    printf 'FAIL\t%s\t%s\t%s\t%s\n' "$HTTP_CODE" "$METHOD" "$PATH_" "$reason"
    FAILS=$((FAILS+1))
  fi
done < "$CATALOG"

printf '\n%d/%d passed\n' $((TOTAL-FAILS)) "$TOTAL" >&2
exit "$FAILS"
