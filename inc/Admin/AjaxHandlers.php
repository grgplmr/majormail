<?php
namespace InterpellerSonMaire\Admin;

use InterpellerSonMaire\Core\Logger;

if (!defined('ABSPATH')) {
    exit;
}

class AjaxHandlers {
    public function __construct() {
        add_action('wp_ajax_ism_add_commune', [$this, 'addCommune']);
        add_action('wp_ajax_ism_delete_commune', [$this, 'deleteCommune']);
        add_action('wp_ajax_ism_import_csv', [$this, 'importCsv']);
        add_action('wp_ajax_ism_delete_template', [$this, 'deleteTemplate']);
        add_action('wp_ajax_ism_save_settings', [$this, 'saveSettings']);
        add_action('wp_ajax_ism_purge_logs', [$this, 'purgeLogs']);
        add_action('wp_ajax_ism_export_data', [$this, 'exportData']);
    }

    private function verifyRequest() {
        $nonce = $_REQUEST['nonce'] ?? '';
        if (!wp_verify_nonce($nonce, 'ism_admin')) {
            wp_send_json_error(__('Nonce invalide', 'interpeller-son-maire'));
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Droits insuffisants', 'interpeller-son-maire'));
        }
    }

    public function addCommune() {
        $this->verifyRequest();
        global $wpdb;
        $data = [
            'name' => sanitize_text_field($_POST['commune_name'] ?? ''),
            'code_insee' => sanitize_text_field($_POST['code_insee'] ?? ''),
            'mayor_email' => sanitize_email($_POST['mayor_email'] ?? ''),
            'population' => absint($_POST['population'] ?? 0),
            'region' => sanitize_text_field($_POST['region'] ?? '')
        ];

        if (empty($data['name']) || empty($data['code_insee']) || empty($data['mayor_email'])) {
            wp_send_json_error(__('Données manquantes', 'interpeller-son-maire'));
        }

        $table = $wpdb->prefix . 'ism_communes';
        $result = $wpdb->insert($table, $data);
        if ($result) {
            wp_send_json_success(['id' => $wpdb->insert_id]);
        } else {
            wp_send_json_error(__('Erreur lors de l\'ajout', 'interpeller-son-maire'));
        }
    }

    public function deleteCommune() {
        $this->verifyRequest();
        global $wpdb;
        $id = absint($_POST['commune_id'] ?? 0);
        if (!$id) {
            wp_send_json_error(__('ID invalide', 'interpeller-son-maire'));
        }
        $table = $wpdb->prefix . 'ism_communes';
        $deleted = $wpdb->delete($table, ['id' => $id]);
        if ($deleted) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Suppression échouée', 'interpeller-son-maire'));
        }
    }

    public function importCsv() {
        $this->verifyRequest();
        if (empty($_FILES['csv_file']['tmp_name'])) {
            wp_send_json_error(__('Fichier manquant', 'interpeller-son-maire'));
        }
        global $wpdb;
        $table = $wpdb->prefix . 'ism_communes';
        $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
        if (!$handle) {
            wp_send_json_error(__('Fichier illisible', 'interpeller-son-maire'));
        }
        $imported = 0;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count($row) < 3) {
                continue;
            }
            $data = [
                'name' => sanitize_text_field($row[0]),
                'code_insee' => sanitize_text_field($row[1]),
                'mayor_email' => sanitize_email($row[2]),
                'population' => isset($row[3]) ? absint($row[3]) : 0,
                'region' => isset($row[4]) ? sanitize_text_field($row[4]) : ''
            ];
            $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE code_insee = %s", $data['code_insee']));
            if ($exists) {
                continue;
            }
            if ($wpdb->insert($table, $data)) {
                $imported++;
            }
        }
        fclose($handle);
        wp_send_json_success(['imported' => $imported]);
    }

    public function deleteTemplate() {
        $this->verifyRequest();
        global $wpdb;
        $id = absint($_POST['template_id'] ?? 0);
        if (!$id) {
            wp_send_json_error(__('ID invalide', 'interpeller-son-maire'));
        }
        $table = $wpdb->prefix . 'ism_templates';
        $deleted = $wpdb->delete($table, ['id' => $id]);
        if ($deleted) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Suppression échouée', 'interpeller-son-maire'));
        }
    }

    public function saveSettings() {
        $this->verifyRequest();
        $data = [
            'email_subject' => sanitize_text_field($_POST['email_subject'] ?? ''),
            'confirmation_message' => wp_kses_post($_POST['confirmation_message'] ?? ''),
            'recaptcha_enabled' => isset($_POST['recaptcha_enabled']),
            'auto_purge_enabled' => isset($_POST['auto_purge_enabled']),
            'purge_delay_months' => absint($_POST['purge_delay_months'] ?? 12)
        ];
        update_option('ism_settings', $data);
        wp_send_json_success();
    }

    public function purgeLogs() {
        $this->verifyRequest();
        $deleted = Logger::purgeOldLogs();
        if ($deleted === false) {
            wp_send_json_error(__('Erreur lors de la purge', 'interpeller-son-maire'));
        }
        wp_send_json_success(['deleted' => $deleted]);
    }

    public function exportData() {
        $this->verifyRequest();
        $type = sanitize_text_field($_GET['type'] ?? '');
        $stats = Logger::getStatistics(30);
        wp_send_json_success([
            'type' => $type,
            'data' => $stats
        ]);
    }
}
