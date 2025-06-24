<?php
namespace InterpellerSonMaire\Admin;

class Ajax {
    public function __construct() {
        add_action('wp_ajax_ism_import_csv', [$this, 'importCsv']);
    }

    public function importCsv() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'interpeller-son-maire'));
        }

        check_ajax_referer('ism_nonce', 'nonce');

        if (empty($_FILES['csv_file']['tmp_name'])) {
            wp_send_json_error(__('No file uploaded', 'interpeller-son-maire'));
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $extension = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
        if ($extension !== 'csv') {
            wp_send_json_error(__('Invalid file type', 'interpeller-son-maire'));
        }

        $handle = fopen($file, 'r');
        if (!$handle) {
            wp_send_json_error(__('Unable to read file', 'interpeller-son-maire'));
        }

        $delimiter = ';';
        $firstRow = fgetcsv($handle, 0, $delimiter);
        if (count($firstRow) <= 1) {
            rewind($handle);
            $delimiter = ',';
            $firstRow = fgetcsv($handle, 0, $delimiter);
        }

        $expected = ['name', 'code_insee', 'mayor_email', 'population', 'region'];
        $header = array_map('trim', $firstRow);
        $hasHeader = count(array_intersect(array_map('strtolower', $header), $expected)) >= 3;
        if (!$hasHeader) {
            rewind($handle);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ism_communes';
        $imported = 0;

        if ($hasHeader) {
            $columns = array_map('strtolower', $header);
            $map = [];
            foreach ($columns as $i => $col) {
                $map[$col] = $i;
            }
        } else {
            $map = [
                'name' => 0,
                'code_insee' => 1,
                'mayor_email' => 2,
                'population' => 3,
                'region' => 4,
            ];
        }

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $data = [];
            foreach ($map as $col => $idx) {
                $data[$col] = $row[$idx] ?? '';
            }

            if (empty($data['name']) || empty($data['code_insee']) || empty($data['mayor_email'])) {
                continue;
            }

            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE code_insee = %s", $data['code_insee']));
            if ($exists) {
                continue;
            }

            $inserted = $wpdb->insert($table, [
                'name' => sanitize_text_field($data['name']),
                'code_insee' => sanitize_text_field($data['code_insee']),
                'mayor_email' => sanitize_email($data['mayor_email']),
                'population' => absint($data['population']),
                'region' => sanitize_text_field($data['region']),
            ]);

            if ($inserted) {
                $imported++;
            }
        }

        fclose($handle);

        wp_send_json_success(['imported' => $imported]);
    }
}
