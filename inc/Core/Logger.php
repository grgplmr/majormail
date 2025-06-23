<?php
namespace InterpellerSonMaire\Core;

class Logger {
    
    public static function logMessage($data) {
        global $wpdb;
        
        // Prepare sensitive data for encryption
        $sensitive_data = [
            'sender_email' => $data['sender_email'],
            'sender_name' => $data['sender_name'],
            'timestamp' => current_time('mysql')
        ];
        
        // Encrypt sensitive data
        $encrypted_data = Security::encryptData($sensitive_data);
        
        // Hash IP and User Agent for privacy
        $ip_hash = hash('sha256', $data['ip'] . wp_salt());
        $user_agent_hash = hash('sha256', $data['user_agent'] . wp_salt());
        
        // Insert log entry
        $table = $wpdb->prefix . 'ism_logs';
        $wpdb->insert($table, [
            'encrypted_data' => $encrypted_data,
            'commune_id' => $data['commune_id'],
            'template_id' => $data['template_id'],
            'ip_hash' => $ip_hash,
            'user_agent_hash' => $user_agent_hash
        ]);
    }
    
    public static function getStatistics($days = 30) {
        global $wpdb;
        
        $logs_table = $wpdb->prefix . 'ism_logs';
        $communes_table = $wpdb->prefix . 'ism_communes';
        $templates_table = $wpdb->prefix . 'ism_templates';
        
        $date_limit = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        // Total messages
        $total_messages = $wpdb->get_var(
            "SELECT COUNT(*) FROM $logs_table"
        );
        
        // Messages this week
        $week_limit = date('Y-m-d H:i:s', strtotime('-7 days'));
        $messages_this_week = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $logs_table WHERE created_at >= %s",
            $week_limit
        ));
        
        // Top communes
        $top_communes = $wpdb->get_results($wpdb->prepare(
            "SELECT c.name, COUNT(l.id) as count 
             FROM $logs_table l 
             JOIN $communes_table c ON l.commune_id = c.id 
             WHERE l.created_at >= %s 
             GROUP BY l.commune_id 
             ORDER BY count DESC 
             LIMIT 10",
            $date_limit
        ));
        
        // Template usage
        $template_usage = $wpdb->get_results($wpdb->prepare(
            "SELECT t.title, COUNT(l.id) as count 
             FROM $logs_table l 
             JOIN $templates_table t ON l.template_id = t.id 
             WHERE l.created_at >= %s AND l.template_id IS NOT NULL 
             GROUP BY l.template_id 
             ORDER BY count DESC 
             LIMIT 10",
            $date_limit
        ));
        
        // Daily stats for the last 7 days
        $daily_stats = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(*) as count 
             FROM $logs_table 
             WHERE created_at >= %s 
             GROUP BY DATE(created_at) 
             ORDER BY date ASC",
            $week_limit
        ));
        
        return [
            'total_messages' => (int) $total_messages,
            'messages_this_week' => (int) $messages_this_week,
            'top_communes' => $top_communes,
            'template_usage' => $template_usage,
            'daily_stats' => $daily_stats
        ];
    }
    
    public static function purgeOldLogs() {
        global $wpdb;
        
        $settings = get_option('ism_settings', []);
        $months = $settings['purge_delay_months'] ?? 12;
        
        $date_limit = date('Y-m-d H:i:s', strtotime("-$months months"));
        
        $table = $wpdb->prefix . 'ism_logs';
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM $table WHERE created_at < %s",
            $date_limit
        ));
        
        return $deleted;
    }
}