<?php
namespace InterpellerSonMaire\Admin;

use InterpellerSonMaire\Core\Logger;
use InterpellerSonMaire\Core\Security;

class AjaxHandler {
    public function __construct() {
        add_action('wp_ajax_ism_export_data', [$this, 'exportData']);
        add_action('wp_ajax_ism_purge_logs', [$this, 'purgeLogs']);
    }

    public function exportData() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'interpeller-son-maire'));
        }

        check_admin_referer('ism_export', 'nonce');

        global $wpdb;
        $table = $wpdb->prefix . 'ism_logs';
        $logs = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="ism_logs.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Timestamp', 'Sender Name', 'Sender Email', 'Commune ID', 'Template ID']);
        foreach ($logs as $log) {
            $data = Security::decryptData($log->encrypted_data);
            $name = $data['sender_name'] ?? '';
            $email = $data['sender_email'] ?? '';
            $timestamp = $data['timestamp'] ?? $log->created_at;
            fputcsv($output, [$log->id, $timestamp, $name, $email, $log->commune_id, $log->template_id]);
        }
        fclose($output);
        exit;
    }

    public function purgeLogs() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Unauthorized', 'interpeller-son-maire'));
        }

        check_ajax_referer('ism_purge_logs', 'nonce');

        $deleted = Logger::purgeOldLogs();
        wp_send_json_success(['deleted' => $deleted]);
    }
}
