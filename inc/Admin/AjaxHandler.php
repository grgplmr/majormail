<?php
namespace InterpellerSonMaire\Admin;

defined('ABSPATH') || exit;

class AjaxHandler {
    public function __construct() {
        add_action('wp_ajax_ism_edit_template', [$this, 'editTemplate']);
    }

    public function editTemplate() {
        check_ajax_referer('ism_edit_template', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'interpeller-son-maire'));
        }

        if (empty($_POST['template_id'])) {
            wp_send_json_error(__('Invalid template', 'interpeller-son-maire'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ism_templates';

        $data = [
            'title' => sanitize_text_field($_POST['template_title'] ?? ''),
            'subject' => sanitize_text_field($_POST['template_subject'] ?? ''),
            'content' => wp_kses_post($_POST['template_content'] ?? ''),
            'category' => sanitize_text_field($_POST['template_category'] ?? '')
        ];

        $updated = $wpdb->update($table, $data, ['id' => absint($_POST['template_id'])]);

        if ($updated !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Update failed', 'interpeller-son-maire'));
        }
    }
}
