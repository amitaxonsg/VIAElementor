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
