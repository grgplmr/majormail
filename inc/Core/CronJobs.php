<?php
namespace InterpellerSonMaire\Core;

class CronJobs {
    
    public function __construct() {
        add_action('ism_daily_cleanup', [$this, 'dailyCleanup']);
    }
    
    public static function scheduleCronJobs() {
        if (!wp_next_scheduled('ism_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'ism_daily_cleanup');
        }
    }
    
    public static function clearCronJobs() {
        wp_clear_scheduled_hook('ism_daily_cleanup');
    }
    
    public function dailyCleanup() {
        $settings = get_option('ism_settings', []);
        
        if (isset($settings['auto_purge_enabled']) && $settings['auto_purge_enabled']) {
            Logger::purgeOldLogs();
        }
    }
}