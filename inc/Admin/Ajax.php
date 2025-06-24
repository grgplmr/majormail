<?php
namespace InterpellerSonMaire\Admin;

class Ajax {

    public function __construct() {
        add_action('wp_ajax_ism_delete_template', [$this, 'deleteTemplate']);
        add_action('wp_ajax_ism_delete_commune', [$this, 'deleteCommune']);
        add_action('wp_ajax_ism_save_settings', [$this, 'saveSettings']);
    }

    public function deleteTemplate() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'interpeller-son-maire'));
        }

        check_ajax_referer('ism_delete_template', 'nonce');

        $template_id = isset($_POST['template_id']) ? absint($_POST['template_id']) : 0;
        if (!$template_id) {
            wp_send_json_error(__('Invalid template ID', 'interpeller-son-maire'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ism_templates';
        $deleted = $wpdb->delete($table, ['id' => $template_id], ['%d']);

        if ($deleted !== false) {
            wp_send_json_success();
        }

        wp_send_json_error(__('Deletion failed', 'interpeller-son-maire'));
    }

    public function deleteCommune() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'interpeller-son-maire'));
        }

        check_ajax_referer('ism_delete_commune', 'nonce');

        $commune_id = isset($_POST['commune_id']) ? absint($_POST['commune_id']) : 0;
        if (!$commune_id) {
            wp_send_json_error(__('Invalid commune ID', 'interpeller-son-maire'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ism_communes';
        $deleted = $wpdb->delete($table, ['id' => $commune_id], ['%d']);

        if ($deleted !== false) {
            wp_send_json_success();
        }

        wp_send_json_error(__('Deletion failed', 'interpeller-son-maire'));
    }

    public function saveSettings() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'interpeller-son-maire'));
        }

        check_ajax_referer('ism_save_settings', 'nonce');

        $new_settings = [
            'email_subject'       => isset($_POST['email_subject']) ? sanitize_text_field($_POST['email_subject']) : '',
            'confirmation_message' => isset($_POST['confirmation_message']) ? wp_kses_post($_POST['confirmation_message']) : '',
            'recaptcha_enabled'   => isset($_POST['recaptcha_enabled']),
            'auto_purge_enabled'  => isset($_POST['auto_purge_enabled']),
            'purge_delay_months'  => isset($_POST['purge_delay_months']) ? absint($_POST['purge_delay_months']) : 12,
        ];

        update_option('ism_settings', $new_settings);

        wp_send_json_success();
    }
}
