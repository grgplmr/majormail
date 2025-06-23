<?php
namespace InterpellerSonMaire\Core;

class Security {
    
    public static function verifyRecaptcha($token) {
        $settings = get_option('ism_settings', []);
        
        if (!isset($settings['recaptcha_enabled']) || !$settings['recaptcha_enabled']) {
            return true; // Skip if disabled
        }
        
        $secret_key = defined('ISM_RECAPTCHA_SECRET') ? ISM_RECAPTCHA_SECRET : '';
        if (empty($secret_key)) {
            return true; // Skip if no key configured
        }
        
        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $secret_key,
                'response' => $token,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
            ]
        ]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        return isset($data['success']) && $data['success'] && 
               isset($data['score']) && $data['score'] >= 0.5;
    }
    
    public static function containsBlockedWords($text) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'ism_blocked_words';
        $blocked_words = $wpdb->get_results("SELECT word, is_regex FROM $table");
        
        $text_lower = strtolower($text);
        
        foreach ($blocked_words as $blocked) {
            if ($blocked->is_regex) {
                if (preg_match('/' . $blocked->word . '/i', $text)) {
                    return true;
                }
            } else {
                if (strpos($text_lower, strtolower($blocked->word)) !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    public static function encryptData($data) {
        $key = self::getEncryptionKey();
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt(json_encode($data), 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    public static function decryptData($encrypted_data) {
        $key = self::getEncryptionKey();
        $data = base64_decode($encrypted_data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
        return json_decode($decrypted, true);
    }
    
    private static function getEncryptionKey() {
        if (defined('ISM_ENCRYPTION_KEY')) {
            return ISM_ENCRYPTION_KEY;
        }
        
        // Fallback to WordPress salts
        return wp_hash('ism_encryption_key' . AUTH_KEY . SECURE_AUTH_KEY);
    }
}