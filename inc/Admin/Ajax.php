<?php
namespace InterpellerSonMaire\Admin;

use InterpellerSonMaire\Admin\AdminMenu;

class Ajax {
    public function __construct() {
        add_action('wp_ajax_ism_save_settings', [$this, 'saveSettings']);
    }

    public function saveSettings() {
        check_ajax_referer('ism_save_settings', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'interpeller-son-maire'), 403);
        }

        $sanitizer = new AdminMenu();
        $sanitized = $sanitizer->sanitizeSettings($_POST);
        update_option('ism_settings', $sanitized);

        wp_send_json_success();
    }
}
