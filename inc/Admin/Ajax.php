<?php
namespace InterpellerSonMaire\Admin;

use InterpellerSonMaire\Core\Logger;

class Ajax {
    public function __construct() {
        add_action('wp_ajax_ism_add_commune', [$this, 'addCommune']);
        add_action('wp_ajax_ism_delete_commune', [$this, 'deleteCommune']);
        add_action('wp_ajax_ism_import_csv', [$this, 'importCSV']);
        add_action('wp_ajax_ism_delete_template', [$this, 'deleteTemplate']);
        add_action('wp_ajax_ism_save_settings', [$this, 'saveSettings']);
        add_action('wp_ajax_ism_purge_logs', [$this, 'purgeLogs']);
        add_action('wp_ajax_ism_export_data', [$this, 'exportData']);
    }

    private function verifyNonce() {
        check_ajax_referer('ism_admin', 'nonce');
    }

    public function addCommune() {
        $this->verifyNonce();

        global $wpdb;
        $table = $wpdb->prefix . 'ism_communes';

        $result = $wpdb->insert($table, [
            'name'       => sanitize_text_field($_POST['commune_name'] ?? ''),
            'code_insee' => sanitize_text_field($_POST['code_insee'] ?? ''),
            'mayor_email'=> sanitize_email($_POST['mayor_email'] ?? ''),
            'population' => absint($_POST['population'] ?? 0),
            'region'     => sanitize_text_field($_POST['region'] ?? '')
        ]);

        if ($result) {
            wp_send_json_success();
        }

        wp_send_json_error($wpdb->last_error);
    }

    public function deleteCommune() {
        $this->verifyNonce();

        $id = absint($_POST['commune_id'] ?? 0);
        if (!$id) {
            wp_send_json_error('Invalid ID');
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ism_communes';
        $deleted = $wpdb->delete($table, ['id' => $id]);

        if ($deleted) {
            wp_send_json_success();
        }

        wp_send_json_error('Deletion failed');
    }

    public function importCSV() {
        $this->verifyNonce();

        if (empty($_FILES['csv_file']['tmp_name'])) {
            wp_send_json_error('No file');
        }

        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        if (!$file) {
            wp_send_json_error('Unable to open file');
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ism_communes';
        $imported = 0;

        while (($data = fgetcsv($file)) !== false) {
            if (count($data) < 5) {
                continue;
            }

            $wpdb->insert($table, [
                'name'       => sanitize_text_field($data[0]),
                'code_insee' => sanitize_text_field($data[1]),
                'mayor_email'=> sanitize_email($data[2]),
                'population' => absint($data[3]),
                'region'     => sanitize_text_field($data[4])
            ]);
            $imported++;
        }

        fclose($file);

        wp_send_json_success(['imported' => $imported]);
    }

    public function deleteTemplate() {
        $this->verifyNonce();

        $id = absint($_POST['template_id'] ?? 0);
        if (!$id) {
            wp_send_json_error('Invalid ID');
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ism_templates';
        $deleted = $wpdb->delete($table, ['id' => $id]);

        if ($deleted) {
            wp_send_json_success();
        }

        wp_send_json_error('Deletion failed');
    }

    public function saveSettings() {
        $this->verifyNonce();

        $settings = [
            'email_subject'       => sanitize_text_field($_POST['email_subject'] ?? ''),
            'confirmation_message'=> wp_kses_post($_POST['confirmation_message'] ?? ''),
            'recaptcha_enabled'   => isset($_POST['recaptcha_enabled']),
            'auto_purge_enabled'  => isset($_POST['auto_purge_enabled']),
            'purge_delay_months'  => absint($_POST['purge_delay_months'] ?? 12),
        ];

        update_option('ism_settings', $settings);
        wp_send_json_success();
    }

    public function purgeLogs() {
        $this->verifyNonce();

        $deleted = Logger::purgeOldLogs();
        wp_send_json_success(['deleted' => $deleted]);
    }

    public function exportData() {
        $this->verifyNonce();

        $type = sanitize_text_field($_GET['type'] ?? 'communes');
        global $wpdb;
        if ($type === 'templates') {
            $table = $wpdb->prefix . 'ism_templates';
            $rows  = $wpdb->get_results("SELECT title, subject, category FROM $table", ARRAY_A);
            $filename = 'templates.csv';
            $headers = ['Title', 'Subject', 'Category'];
        } else {
            $table = $wpdb->prefix . 'ism_communes';
            $rows  = $wpdb->get_results("SELECT name, code_insee, mayor_email, population, region FROM $table", ARRAY_A);
            $filename = 'communes.csv';
            $headers = ['Name', 'Code INSEE', 'Mayor Email', 'Population', 'Region'];
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
}
