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
