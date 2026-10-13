# VIA development server — SSH, deployment and rollback

## Environment restrictions

- Development URL: `https://axonserver.com`; **not** the client's production website.
- cPanel user: `amitaxon`; WordPress root: `/home/amitaxon/public_html`.
- Run WP-CLI as `amitaxon`, never root WP-CLI, and pass the explicit `--path`.
- Do not change another account or server-wide configuration.
- Active Elementor Site Kit expected at **19**; homepage expected **21**.
- Guarded backups: `/home/amitaxon/via-fullsite-deployment/backups/` (private, outside document root).

## Site verification

```bash
WP=/home/amitaxon/public_html
runuser -u amitaxon -- wp --path="$WP" option get siteurl
runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit
runuser -u amitaxon -- wp --path="$WP" option get page_on_front
runuser -u amitaxon -- wp --path="$WP" post list --post_type=page --fields=ID,post_title,post_status
```

## Source-control deployment procedure

Files on GitHub do **not** change WordPress until copied to the account and executed. Clone/update only a trusted branch in a private account directory; do not put `.git` inside `public_html`.

```bash
sudo -u amitaxon git clone https://github.com/amitaxonsg/VIAElementor.git /home/amitaxon/VIAElementor
# subsequent pulls: runuser -u amitaxon -- git -C /home/amitaxon/VIAElementor pull --ff-only
```

Inspect changes before executing PHP. To check shared-template migration:

```bash
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/VIAElementor/wp-cli/unify-shared-layout.php
php -l "$SCRIPT"
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" dry-run
```

Only when dry run succeeds and the **About shared template** is visually approved, run `... eval-file "$SCRIPT" apply`. It backs up existing metadata before edits. Refresh Elementor cache:

```bash
runuser -u amitaxon -- wp --path="$WP" elementor flush-css
```

Check homepage and About in a normal browser and mobile responsive view. If visual issues appear, restore via `wp-cli/rollback-shared-layout.php` and the backup filename printed by the apply run.

## Expected unified architecture

Templates **57/58** must be published, with `ehf_template_type=type_header/type_footer`. Their include locations must have `rule=['specifics']` and `specific=['post-21', ..., 'post-49']`. Each VIA page layout should be `elementor_header_footer`. Page **21** must contain only homepage content sections, not its embedded `01 Header` and `07 Footer`. Inner-page publication statuses remain unchanged.

Do not put the header/footer into both a page AND a template.

The UAE styles may require `via-shared-template-assets.php` to enqueue Elementor CSS for templates 57/58. The earlier About test used this loader. Preserve it during rollout; don't apply sitewide CSS overrides without reviewing compatibility.

## Public repository and backups

**Never commit** database backups, private content, secrets, or the private `/home/amitaxon/via-fullsite-deployment/backups` directory. There is no automatic server-to-GitHub upload; page and kit exports require an explicit reviewed transfer.

## Rollback

Apply script prints a dated JSON snapshot. Verify its name, then dry-run `wp-cli/rollback-shared-layout.php` with the full absolute backup path, and apply only if the restore report matches page and template IDs. The rollback restores only modified Elementor data/layout/conditions/status, not unrelated WordPress settings.


## cPanel upload convention (confirmed 2026-10-13)

The user uploads downloaded VIA PHP scripts through cPanel File Manager to the **private account home directory** `/home/amitaxon/`. This is the default upload location for future requests; scripts should not be assumed to live under `/tmp`, `public_html`, or a Git clone.

Before re-downloading a script, use its original filename in `/home/amitaxon/`:
```bash
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-inner-pages-hero-template.php
test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com"
test "$(runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit)" = "19"
test -s "$SCRIPT"
stat -c '%U:%G %a %s bytes %n' "$SCRIPT"
php -l "$SCRIPT"
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" dry-run
```

Always use **the script's exact actual filename**, not one from an unrelated example. Run `apply` only after the dry run passes and the user approves the change. Upload progress reaching 100% proves transfer, **not** that contents or version match a GitHub commit; validate before using. Preserve private backups outside the website root. If GitHub changes after the user's upload, do not silently replace their uploaded script—inform them and verify the new revision explicitly.


## After-template full-site backup procedure — 2026-10-13 (executed and verified)

The user supplied terminal output confirming the new snapshot **`VIA-FULL-20261013-140913`** completed (~108 MB) at `/home/amitaxon/via-fullsite-deployment/backups/full-site/VIA-FULL-20261013-140913`, with database, files and checksums verified. This supersedes earlier pending-backup language; `VIA-FULL-20261013-120355` remains an older verified snapshot. Retain the following instructions for the next fresh backup. No off-server copy or test restore has been verified.

The tested existing server-local backup creator is:

```text
/home/amitaxon/via-fullsite-deployment/backup-via-wordpress.sh
```

It exports the account's WordPress database and archives `/home/amitaxon/public_html`, storing copies and SHA-256 checksums in private timestamped directories below `/home/amitaxon/via-fullsite-deployment/backups/full-site/`.

### SSH: launch in background (survives SSH disconnection)

```bash
set -euo pipefail
WP=/home/amitaxon/public_html
BASE=/home/amitaxon/via-fullsite-deployment
SCRIPT="$BASE/backup-via-wordpress.sh"
OUT="$BASE/backups/full-site"
test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com"
test "$(runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit)" = "19"
test -x "$SCRIPT"
bash -n "$SCRIPT"
test -d "$OUT"
if pgrep -u amitaxon -f '[b]ackup-via-wordpress.sh' >/dev/null; then
  echo "STOP: another VIA backup is running"
  exit 1
fi
df -h "$OUT"
STAMP=$(date +%Y%m%d-%H%M%S)
LOG="$OUT/backup-$STAMP.log"
PID="$OUT/backup-$STAMP.pid"
runuser -u amitaxon -- env VIA_SCRIPT="$SCRIPT" VIA_LOG="$LOG" VIA_PID="$PID" sh -c '
  umask 077
  nohup nice -n 10 "$VIA_SCRIPT" >"$VIA_LOG" 2>&1 </dev/null &
  echo $! >"$VIA_PID"
'
echo "LOG: $LOG"
echo "PID: $(cat "$PID")"
sleep 3
tail -n 20 "$LOG"
```

Do not edit/publish Elementor during the backup window. **Do not launch another backup if an existing one is still running.**

### SSH: monitor and verify completion

```bash
set -euo pipefail
OUT=/home/amitaxon/via-fullsite-deployment/backups/full-site
LOG=$(find "$OUT" -maxdepth 1 -type f -name 'backup-*.log' -printf '%f\n' | sort | tail -1)
test -n "$LOG"
LOG="$OUT/$LOG"
echo "Latest log: $LOG"
tail -n 35 "$LOG"
if ! grep -q 'VIA FULL WORDPRESS BACKUP COMPLETED' "$LOG"; then
  echo "Not yet complete. Do NOT treat as a recovery point."
  exit 1
fi
DEST=$(sed -n 's/^Location: //p' "$LOG" | tail -1)
case "$DEST" in "$OUT"/VIA-FULL-*) ;; *) echo "Unexpected backup destination"; exit 1 ;; esac
test -d "$DEST"
test -s "$DEST/database.sql"
test -s "$DEST/wordpress-files.tar.gz"
test -s "$DEST/SHA256SUMS.txt"
(cd "$DEST"; sha256sum -c SHA256SUMS.txt; gzip -t wordpress-files.tar.gz)
du -sh "$DEST"
echo "NEW BACKUP VERIFIED: $DEST"
```

Record the **actual new snapshot directory** and its successful checksum output in GitHub *after* running these commands. Do not store archives, SQL dumps or access secrets in the public repository. Keep an encrypted copy off-server for disaster recovery. Checkums do not substitute for a test restore on a staging site.
