# MU-plugin reference implementations

These are **reference copies of the approach**, not verified byte-for-byte exports from the current WordPress server. The site's active files may contain later geometry fixes.

- `via-shared-template-assets.php` enqueues Elementor CSS for shared templates 57/58 on VIA pages. Necessary if the UAE header/footer markup appears unstyled.
- `via-uae-global-bridge.php` uses Elementor Site Kit CSS variables for navigation and dropdown typography / colors. Needed because some UAE widget control keys used during development were not registered as native Elementor Global control fields.

**Do not blindly copy into production** or overwrite the live development MU-plugins. First compare the active server files and test menu hover, mobile hamburger, logo crop and button appearance.

The temporary `via-dev-header-fix.php` used on the dev server is not versioned here because its current final contents haven't been extracted and reviewed. It includes geometry/logo crop rules that Elementor Site Kit cannot replace by itself.

The goal is to retire temporary patches after the shared header/footer can be controlled directly in Elementor. Never commit secrets.
