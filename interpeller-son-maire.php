<?php
/**
 * Plugin Name: Interpeller son Maire
 * Plugin URI: https://touchepasamespoubelles.fr
 * Description: Plugin permettant aux citoyens de contacter leur maire concernant la suppression du ramassage porte-à-porte
 * Version: 1.0.0
 * Author: touchepasamespoubelles.fr
 * Text Domain: interpeller-son-maire
 * Domain Path: /languages
 * Requires at least: 6.5
 * Tested up to: 6.4
 * Requires PHP: 8.1
 * License: GPL v2 or later
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ISM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ISM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ISM_PLUGIN_VERSION', '1.0.0');
define('ISM_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader
spl_autoload_register(function ($class) {
    if (strpos($class, 'InterpellerSonMaire\\') === 0) {
        $class = str_replace('InterpellerSonMaire\\', '', $class);
        $class = str_replace('\\', '/', $class);
        $file = ISM_PLUGIN_PATH . 'inc/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Main plugin class
class InterpellerSonMaire {
    
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', [$this, 'init']);
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        register_uninstall_hook(__FILE__, [__CLASS__, 'uninstall']);
    }
    
    public function init() {
        // Load text domain
        load_plugin_textdomain('interpeller-son-maire', false, dirname(ISM_PLUGIN_BASENAME) . '/languages');
        
        // Initialize components
        new InterpellerSonMaire\Admin\AdminMenu();
        new InterpellerSonMaire\Frontend\Shortcode();
        new InterpellerSonMaire\Api\RestApi();
        new InterpellerSonMaire\Core\CronJobs();
        new InterpellerSonMaire\Gutenberg\Block();
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
    }
    
    public function enqueueScripts() {
        wp_enqueue_script('ism-frontend', ISM_PLUGIN_URL . 'assets/js/frontend.js', ['jquery'], ISM_PLUGIN_VERSION, true);
        wp_enqueue_style('ism-frontend', ISM_PLUGIN_URL . 'assets/css/frontend.css', [], ISM_PLUGIN_VERSION);

        $settings = get_option('ism_settings', []);
        $recaptcha_enabled = isset($settings['recaptcha_enabled']) && $settings['recaptcha_enabled'] && defined('ISM_RECAPTCHA_SITE_KEY') && ISM_RECAPTCHA_SITE_KEY;

        if ($recaptcha_enabled) {
            wp_enqueue_script(
                'google-recaptcha',
                'https://www.google.com/recaptcha/api.js?render=' . ISM_RECAPTCHA_SITE_KEY,
                [],
                null,
                true
            );
        }

        // Localize script
        wp_localize_script('ism-frontend', 'ismAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ism_nonce'),
            'restUrl' => rest_url('interpeller-son-maire/v1/'),
            'restNonce' => wp_create_nonce('wp_rest'),
            'recaptchaSiteKey' => $recaptcha_enabled ? ISM_RECAPTCHA_SITE_KEY : ''
        ]);
    }
    
    public function enqueueAdminScripts($hook) {
        if (strpos($hook, 'interpeller-son-maire') !== false) {
            wp_enqueue_script('ism-admin', ISM_PLUGIN_URL . 'assets/js/admin.js', ['jquery', 'chart-js'], ISM_PLUGIN_VERSION, true);
            wp_enqueue_style('ism-admin', ISM_PLUGIN_URL . 'assets/css/admin.css', [], ISM_PLUGIN_VERSION);
            wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], '3.9.1', true);
        }
    }
    
    public function activate() {
        InterpellerSonMaire\Core\Database::createTables();
        InterpellerSonMaire\Core\CronJobs::scheduleCronJobs();
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        InterpellerSonMaire\Core\CronJobs::clearCronJobs();
        flush_rewrite_rules();
    }
    
    public static function uninstall() {
        InterpellerSonMaire\Core\Database::dropTables();
        delete_option('ism_settings');
    }
}

// Initialize plugin
InterpellerSonMaire::getInstance();