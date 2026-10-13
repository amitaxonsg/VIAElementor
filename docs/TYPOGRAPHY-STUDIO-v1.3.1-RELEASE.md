# VIA Typography Studio 1.3.1 — verified working version (2026-10-13)

The user confirmed that VIA Typography Studio **v1.3.1** saves and displays changes correctly, including within the styled Pathways section where earlier versions conflicted with scoped `!important` rules.

## Version and behavior

- WordPress plugin slug: `via-typography-studio`.
- Validated version: **1.3.1**, upgraded from 1.3.0.
- Frontend selection + live preview work for Elementor text elements.
- AJAX save persists override rules in the WordPress option `via_typography_studio_rules_v2`.
- CSS endpoint `/wp-admin/admin-ajax.php?action=via_type_css` previously returned HTTP 200 and CSS.
- v1.3.1 boosts specificity **for new widget-relative `data-via-type-key` selectors only**; it intentionally does not amplify historical `data-via-type-id` overrides.
- WP admin bar navigation must remain functional; pause/resume selection remains available.
- Do not hardcode global VIA font family names in page presentation styles. Elementor Site Kit ID 19 stays the baseline. User changes through Typography Studio are intentional scoped overrides.

## Safety / status

- Development: `https://axonserver.com`, cPanel account `amitaxon`. Other accounts and the production website are out of scope.
- Source package delivered in this chat: `VIA-Typography-Studio-v1.3.1-priority-fix.zip`; the user has confirmed it works.
- This document **does not contain or claim to be the distributable plugin source**. A clean reviewed copy of the source files should be committed under `plugins/via-typography-studio/` separately. Do not attempt to rebuild the package from this document.
- Never commit saved client typography overrides, WordPress database options, server secrets or backups.

## Future releases

Changes must be tested on both normal header text and Elementor section/card text, with Save → page refresh → persistence verification. A success message alone is not proof that CSS won the cascade.
