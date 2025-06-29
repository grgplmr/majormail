<?php
namespace InterpellerSonMaire\Admin;

defined('ABSPATH') || exit;

class AjaxHandler {
    public function __construct() {
        add_action('wp_ajax_ism_edit_template', [$this, 'editTemplate']);
        add_action('wp_ajax_ism_edit_commune', [$this, 'editCommune']);
        add_action('wp_ajax_ism_delete_commune', [$this, 'deleteCommune']);
        add_action('wp_ajax_ism_delete_template', [$this, 'deleteTemplate']);
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

    public function editCommune() {
        check_ajax_referer('ism_edit_commune', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'interpeller-son-maire'));
        }

        if (empty($_POST['commune_id'])) {
            wp_send_json_error(__('Invalid commune', 'interpeller-son-maire'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ism_communes';

        $data = [
            'name' => sanitize_text_field($_POST['commune_name'] ?? ''),
            'code_insee' => isset($_POST['code_insee']) && $_POST['code_insee'] !== '' ? sanitize_text_field($_POST['code_insee']) : null,
            'mayor_email' => sanitize_email($_POST['mayor_email'] ?? ''),
            'population' => isset($_POST['population']) ? absint($_POST['population']) : null,
            'region' => sanitize_text_field($_POST['region'] ?? '')
        ];

        $updated = $wpdb->update($table, $data, ['id' => absint($_POST['commune_id'])]);

        if ($updated !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Update failed', 'interpeller-son-maire'));
        }
    }

    public function deleteCommune() {
        check_ajax_referer('ism_delete_commune', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'interpeller-son-maire'));
        }

        if (empty($_POST['commune_id'])) {
            wp_send_json_error(__('Invalid commune', 'interpeller-son-maire'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ism_communes';

        $deleted = $wpdb->delete($table, ['id' => absint($_POST['commune_id'])]);

        if ($deleted !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Delete failed', 'interpeller-son-maire'));
        }
    }

    public function deleteTemplate() {
        check_ajax_referer('ism_delete_template', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'interpeller-son-maire'));
        }

        if (empty($_POST['template_id'])) {
            wp_send_json_error(__('Invalid template', 'interpeller-son-maire'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ism_templates';

        $deleted = $wpdb->delete($table, ['id' => absint($_POST['template_id'])]);

        if ($deleted !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Delete failed', 'interpeller-son-maire'));
        }
    }
}
