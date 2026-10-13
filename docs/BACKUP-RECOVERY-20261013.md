# VIA Frontiers — Verified Private WordPress Recovery Snapshot

**Checkpoint date:** 2026-10-13 (server timezone UTC+08:00).  
**Status:** Backup completed; both recorded SHA-256 checksums verified by SSH.  
**Source of evidence:** User-supplied shell output. A full restoration test has **not** yet been performed.

## Snapshot manifest (metadata only)

| Item | Verified value |
| --- | --- |
| WordPress development website | `https://axonserver.com` |
| cPanel / WordPress account | `amitaxon` |
| WordPress root | `/home/amitaxon/public_html` |
| Active Elementor Site Kit expected | `19` |
| Recovery snapshot | `VIA-FULL-20261013-120355` |
| Server-local private location | `/home/amitaxon/via-fullsite-deployment/backups/full-site/VIA-FULL-20261013-120355/` |
| Source WordPress directory size reported | Approximately 353 MB |
| Completed snapshot disk usage | Approximately 106 MB |
| Database dump checksum | `database.sql: OK` |
| WordPress files checksum | `wordpress-files.tar.gz: OK` |
| Backup execution | Detached `nohup` backup, after initial SSH session disconnected |
| Prior interrupted attempt | `.incomplete-20261013-120013-COEqqM` (~23 MB), **NOT** a recovery point |

The snapshot is a **point-in-time** copy from 2026-10-13 12:03:55 +08:00. Later WordPress changes are not contained in it.

## Intended backup contents

- `database.sql`: WordPress database exported with WP-CLI (including Elementor pages/templates and saved options as of the export time).
- `wordpress-files.tar.gz`: archive of the entire WordPress root (including plugin, theme, MU-plugin and uploads directories).
- `SHA256SUMS.txt`: recorded hashes for database and files.
- `README.txt`, archive listing and, only if needed, `wp-config-outside-webroot.php`: supplementary manifest or configuration collected by the server backup script.

**Caution:** A SQL dump and WordPress configuration can contain sensitive information. This GitHub repository is public. **Never** commit any snapshot contents, database data, private configuration, user data, credentials, backups, or secret files to GitHub. Do not publish a download URL to the snapshot.

## Read-only SSH verification

Run this only in the intended server/account environment:

```bash
set -euo pipefail
SNAP=/home/amitaxon/via-fullsite-deployment/backups/full-site/VIA-FULL-20261013-120355
test -d "$SNAP"
test -s "$SNAP/database.sql"
test -s "$SNAP/wordpress-files.tar.gz"
test -s "$SNAP/SHA256SUMS.txt"
(
  cd "$SNAP"
  sha256sum -c SHA256SUMS.txt
)
gzip -t "$SNAP/wordpress-files.tar.gz"
du -sh "$SNAP"
```

Expected checksum lines: `database.sql: OK`, `wordpress-files.tar.gz: OK`. Checksums establish file integrity relative to the saved manifest, not that a full restore has been tested.

## Backup procedure for future changes

The **currently deployed server backup script** is at:

```text
/home/amitaxon/via-fullsite-deployment/backup-via-wordpress.sh
```

It is a **server-local** script, not yet verified line-by-line against a GitHub source copy. Use an account-scoped background launch for long running backups to survive an SSH disconnect, and never start a second backup while one is still running. Use `runuser -u amitaxon` or an authenticated session as that account; don't run WP-CLI as root. New snapshots receive unique timestamped directories.

When any major change is planned, check available disk space, create a **new** snapshot, wait for its completion, validate hashes, and note its exact path. Avoid publishing Elementor edits while the database and file snapshot is being generated.

## Recovery policy

1. **Prefer narrow rollback**: restore only the damaged Elementor template, widget data, or plugin from the last suitable backup. Header template **57**, footer **58**, and homepage **21** must not be overwritten wholesale to fix a local section.
2. For a catastrophic restore, confirm the intended site `https://axonserver.com`, the target cPanel account `amitaxon`, the snapshot date, and the expected post-snapshot changes that would be lost.
3. Re-verify checksum integrity and collect a fresh **pre-restore** safety copy before any full restore. Coordinate a short maintenance window.
4. Restore the database and corresponding files **together** only when full recovery is required; otherwise use a scoped backup item. Verify URL, pages, Elementor Site Kit 19, Header 57, Footer 58, typography plugin, images, menus, and responsive layout after recovery.
5. Never run an automated destructive restore from documentation without explicitly confirming which changes will be lost.
6. A full restore on a staging copy remains **recommended but untested**.

## Off-server protection

This verified snapshot exists on **the same server as WordPress**. It does not protect against complete server/disk loss. Maintain at least one separately secured, preferably encrypted **off-server** copy, with access restricted to authorised administrators. Confirm off-server integrity periodically.

## Source-of-truth boundary

GitHub records the backup **manifest, procedure, and recovery instructions**. Actual WordPress Elementor data, private server MU-plugin contents, database and complete website archive remain on the server unless separately and safely exported/reviewed. A GitHub commit does **not** mean the current WordPress database has been synchronized.

**Going forward:** SSH trial → visual and functional verification → approval → GitHub documentation or reviewed code commit. For high-impact changes, first confirm this recovery checkpoint or make a fresher verified snapshot.


## Later full-site backup — executed and verified 2026-10-13 at 14:09 (+08:00)

**Latest verified snapshot:** `/home/amitaxon/via-fullsite-deployment/backups/full-site/VIA-FULL-20261013-140913` (~108 MB). User's SSH output states “Database, files and checksums verified.” This is the current preferred full-site recovery point. The earlier `VIA-FULL-20261013-120355` (~106 MB, separate earlier checksum confirmation) remains preserved. No actual staging restore or off-server copy has been confirmed. The incomplete `.incomplete-20261013-120013-COEqqM` must not be treated as a complete backup. The following instructions are retained for future backup runs.

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
