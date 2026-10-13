# VIA Frontiers — Approved Header / Hero Checkpoint (2026-10-13)

## User-verified appearance
- Development site https://axonserver.com; account amitaxon, homepage 21, Elementor Site Kit 19.
- Shared header template 57 uses a full-width layout, original logo left, centred navigation and orange Let's Talk CTA right.
- The approved photographic hero retains Sylvia's boardroom background, left text and buttons, and the statistics strip below.
- The last approved desktop Hero 0000015 / Stage 0000014 minimum height is 70vh. Tablet/mobile values are separate.
- The user approved the appearance in the latest screenshot. The repository script wp-cli/hero-height-70vh-approved.php records this approved desktop setting.
- The site's actual Elementor database and local MU-plugin edits are NOT automatically mirrored to GitHub.

## Footer width request (SSH test first)
- Shared footer template 58 uses the approved white four-column structure and bottom copyright/links row.
- Request: full-width content layout like header; spread logo and columns across the available screen width while maintaining all links, wording, responsiveness, and global font families.
- Test the change in an isolated footer-only MU-plugin via SSH, take a backup, and visually verify before committing the footer CSS to GitHub.
- Do not alter the footer's internal card/column widths blindly, or change Hero, Pathways, FIRE, Header 57, or Site Kit 19.

## Working process
- SSH test -> desktop/mobile visual approval -> GitHub commit.
- Never commit private WordPress backups or credentials.
- Global font families are controlled by Elementor Site Kit 19, not CSS literals. Small section headings 12px, main headings 36px, Pathways card headings 25px.