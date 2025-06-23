<?php
namespace InterpellerSonMaire\Api;

use InterpellerSonMaire\Core\Security;
use InterpellerSonMaire\Core\EmailSender;
use InterpellerSonMaire\Core\Logger;

class RestApi {
    
    public function __construct() {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }
    
    public function registerRoutes() {
        register_rest_route('interpeller-son-maire/v1', '/communes', [
            'methods' => 'GET',
            'callback' => [$this, 'searchCommunes'],
            'permission_callback' => '__return_true',
            'args' => [
                'search' => [
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ]
            ]
        ]);
        
        register_rest_route('interpeller-son-maire/v1', '/templates', [
            'methods' => 'GET',
            'callback' => [$this, 'getTemplates'],
            'permission_callback' => '__return_true'
        ]);
        
        register_rest_route('interpeller-son-maire/v1', '/send-message', [
            'methods' => 'POST',
            'callback' => [$this, 'sendMessage'],
            'permission_callback' => [$this, 'checkNonce'],
            'args' => [
                'commune_id' => [
                    'required' => true,
                    'type' => 'integer',
                    'validate_callback' => [$this, 'validateCommuneId']
                ],
                'firstname' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'lastname' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'email' => [
                    'required' => true,
                    'type' => 'string',
                    'validate_callback' => 'is_email'
                ],
                'message' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => [$this, 'sanitizeMessage']
                ],
                'template_id' => [
                    'required' => false,
                    'type' => 'integer'
                ],
                'recaptcha_token' => [
                    'required' => true,
                    'type' => 'string'
                ]
            ]
        ]);
    }
    
    public function searchCommunes($request) {
        global $wpdb;
        
        $search = $request->get_param('search');
        $table = $wpdb->prefix . 'ism_communes';
        
        if (empty($search)) {
            $results = $wpdb->get_results(
                "SELECT id, name, code_insee, population, region FROM $table ORDER BY name LIMIT 20"
            );
        } else {
            $search = '%' . $wpdb->esc_like($search) . '%';
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT id, name, code_insee, population, region FROM $table 
                 WHERE name LIKE %s OR code_insee LIKE %s 
                 ORDER BY name LIMIT 20",
                $search, $search
            ));
        }
        
        return rest_ensure_response($results);
    }
    
    public function getTemplates($request) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'ism_templates';
        $results = $wpdb->get_results(
            "SELECT id, title, subject, content, category, usage_count 
             FROM $table 
             WHERE is_active = 1 
             ORDER BY usage_count DESC, title ASC"
        );
        
        return rest_ensure_response($results);
    }
    
    public function sendMessage($request) {
        // Verify reCAPTCHA
        $recaptcha_token = $request->get_param('recaptcha_token');
        if (!Security::verifyRecaptcha($recaptcha_token)) {
            return new \WP_Error('recaptcha_failed', __('Vérification reCAPTCHA échouée', 'interpeller-son-maire'), ['status' => 400]);
        }
        
        // Get parameters
        $commune_id = $request->get_param('commune_id');
        $firstname = $request->get_param('firstname');
        $lastname = $request->get_param('lastname');
        $email = $request->get_param('email');
        $message = $request->get_param('message');
        $template_id = $request->get_param('template_id');
        
        // Check for blocked words
        if (Security::containsBlockedWords($message)) {
            return new \WP_Error('blocked_words', __('Votre message contient des mots interdits', 'interpeller-son-maire'), ['status' => 400]);
        }
        
        // Get commune data
        global $wpdb;
        $commune_table = $wpdb->prefix . 'ism_communes';
        $commune = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $commune_table WHERE id = %d",
            $commune_id
        ));
        
        if (!$commune) {
            return new \WP_Error('commune_not_found', __('Commune non trouvée', 'interpeller-son-maire'), ['status' => 404]);
        }
        
        // Process message with placeholders
        $processed_message = str_replace(
            ['{prenom}', '{nom}', '{commune}', '{email}'],
            [$firstname, $lastname, $commune->name, $email],
            $message
        );
        
        // Send email
        $email_sender = new EmailSender();
        $sent = $email_sender->sendToMayor([
            'commune' => $commune,
            'sender' => [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email
            ],
            'message' => $processed_message,
            'template_id' => $template_id
        ]);
        
        if (!$sent) {
            return new \WP_Error('email_failed', __('Erreur lors de l\'envoi de l\'email', 'interpeller-son-maire'), ['status' => 500]);
        }
        
        // Log the message
        Logger::logMessage([
            'commune_id' => $commune_id,
            'template_id' => $template_id,
            'sender_email' => $email,
            'sender_name' => $firstname . ' ' . $lastname,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        // Update template usage count
        if ($template_id) {
            $template_table = $wpdb->prefix . 'ism_templates';
            $wpdb->query($wpdb->prepare(
                "UPDATE $template_table SET usage_count = usage_count + 1 WHERE id = %d",
                $template_id
            ));
        }
        
        // Send confirmation email
        $email_sender->sendConfirmation([
            'recipient_email' => $email,
            'recipient_name' => $firstname . ' ' . $lastname,
            'commune_name' => $commune->name,
            'message' => $processed_message
        ]);
        
        return rest_ensure_response([
            'success' => true,
            'message' => __('Message envoyé avec succès', 'interpeller-son-maire')
        ]);
    }
    
    public function checkNonce($request) {
        $nonce = $request->get_header('X-WP-Nonce');
        return wp_verify_nonce($nonce, 'wp_rest');
    }
    
    public function validateCommuneId($value) {
        global $wpdb;
        $table = $wpdb->prefix . 'ism_communes';
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE id = %d",
            $value
        ));
        return $exists > 0;
    }
    
    public function sanitizeMessage($value) {
        return wp_kses($value, [
            'p' => [],
            'br' => [],
            'strong' => [],
            'em' => [],
            'ul' => [],
            'ol' => [],
            'li' => []
        ]);
    }
}