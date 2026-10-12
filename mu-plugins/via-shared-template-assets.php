<?php
/**
 * Plugin Name: VIA Shared Elementor Template CSS Loader
 * Description: Loads the Elementor styles for the existing shared UAE templates only on VIA pages.
 *
 * Reference implementation. Compare against the current server MU-plugin before deployment.
 */
add_action('wp_enqueue_scripts', static function (): void {
    if (!is_singular('page')) {
        return;
    }
    $id = (int)get_queried_object_id();
    if ($id < 21 || $id > 49 ||
        !class_exists('\Elementor\Core\Files\CSS\Post')) {
        return;
    }
    foreach ([57, 58] as $templateId) {
        (new \Elementor\Core\Files\CSS\Post($templateId))->enqueue();
    }
}, 30);
