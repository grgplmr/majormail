<?php
namespace InterpellerSonMaire\Admin;

class AdminMenu {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'addMenuPages']);
        add_action('admin_init', [$this, 'registerSettings']);
    }
    
    public function addMenuPages() {
        add_menu_page(
            __('Interpeller son Maire', 'interpeller-son-maire'),
            __('Interpeller son Maire', 'interpeller-son-maire'),
            'manage_options',
            'interpeller-son-maire',
            [$this, 'renderMainPage'],
            'dashicons-email-alt',
            30
        );
        
        add_submenu_page(
            'interpeller-son-maire',
            __('Communes & Maires', 'interpeller-son-maire'),
            __('Communes & Maires', 'interpeller-son-maire'),
            'manage_options',
            'interpeller-son-maire-communes',
            [$this, 'renderCommunesPage']
        );
        
        add_submenu_page(
            'interpeller-son-maire',
            __('Modèles de messages', 'interpeller-son-maire'),
            __('Modèles de messages', 'interpeller-son-maire'),
            'manage_options',
            'interpeller-son-maire-templates',
            [$this, 'renderTemplatesPage']
        );
        
        add_submenu_page(
            'interpeller-son-maire',
            __('Mots interdits', 'interpeller-son-maire'),
            __('Mots interdits', 'interpeller-son-maire'),
            'manage_options',
            'interpeller-son-maire-blocked-words',
            [$this, 'renderBlockedWordsPage']
        );
        
        add_submenu_page(
            'interpeller-son-maire',
            __('Statistiques', 'interpeller-son-maire'),
            __('Statistiques', 'interpeller-son-maire'),
            'manage_options',
            'interpeller-son-maire-stats',
            [$this, 'renderStatsPage']
        );
        
        add_submenu_page(
            'interpeller-son-maire',
            __('Réglages', 'interpeller-son-maire'),
            __('Réglages', 'interpeller-son-maire'),
            'manage_options',
            'interpeller-son-maire-settings',
            [$this, 'renderSettingsPage']
        );
    }
    
    public function registerSettings() {
        register_setting('ism_settings', 'ism_settings', [
            'sanitize_callback' => [$this, 'sanitizeSettings']
        ]);
    }
    
    public function renderMainPage() {
        include ISM_PLUGIN_PATH . 'templates/admin/dashboard.php';
    }
    
    public function renderCommunesPage() {
        include ISM_PLUGIN_PATH . 'templates/admin/communes.php';
    }
    
    public function renderTemplatesPage() {
        include ISM_PLUGIN_PATH . 'templates/admin/templates.php';
    }
    
    public function renderBlockedWordsPage() {
        include ISM_PLUGIN_PATH . 'templates/admin/blocked-words.php';
    }
    
    public function renderStatsPage() {
        include ISM_PLUGIN_PATH . 'templates/admin/statistics.php';
    }
    
    public function renderSettingsPage() {
        include ISM_PLUGIN_PATH . 'templates/admin/settings.php';
    }
    
    public function sanitizeSettings($input) {
        $sanitized = [];
        
        if (isset($input['email_subject'])) {
            $sanitized['email_subject'] = sanitize_text_field($input['email_subject']);
        }
        
        if (isset($input['confirmation_message'])) {
            $sanitized['confirmation_message'] = wp_kses_post($input['confirmation_message']);
        }
        
        $sanitized['recaptcha_enabled'] = isset($input['recaptcha_enabled']);
        $sanitized['auto_purge_enabled'] = isset($input['auto_purge_enabled']);
        
        if (isset($input['purge_delay_months'])) {
            $sanitized['purge_delay_months'] = absint($input['purge_delay_months']);
        }
        
        return $sanitized;
    }
}