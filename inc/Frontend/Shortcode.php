<?php
namespace InterpellerSonMaire\Frontend;

class Shortcode {
    
    public function __construct() {
        add_shortcode('interpeller_maire', [$this, 'renderShortcode']);
    }
    
    public function renderShortcode($atts) {
        $atts = shortcode_atts([
            'default_template' => '',
            'button_color' => '#2563eb',
            'show_templates' => 'true'
        ], $atts);
        
        ob_start();
        $this->renderForm($atts);
        return ob_get_clean();
    }
    
    private function renderForm($atts) {
        $nonce = wp_create_nonce('ism_send_message');
        ?>
        <div class="ism-form-container" id="ism-form-container">
            <div class="ism-form-header">
                <h2><?php _e('Contactez votre maire en quelques clics', 'interpeller-son-maire'); ?></h2>
                <p><?php _e('Exprimez vos préoccupations concernant la suppression du ramassage porte-à-porte', 'interpeller-son-maire'); ?></p>
            </div>
            
            <form id="ism-contact-form" class="ism-form" method="post">
                <?php wp_nonce_field('ism_send_message', 'ism_nonce'); ?>
                
                <!-- Commune Selection -->
                <div class="ism-field-group">
                    <label for="ism-commune"><?php _e('Votre commune *', 'interpeller-son-maire'); ?></label>
                    <div class="ism-commune-search">
                        <input type="text" 
                               id="ism-commune" 
                               name="commune_search" 
                               placeholder="<?php _e('Rechercher votre commune...', 'interpeller-son-maire'); ?>" 
                               autocomplete="off" 
                               required>
                        <input type="hidden" id="ism-commune-id" name="commune_id" required>
                        <div id="ism-commune-results" class="ism-search-results"></div>
                    </div>
                </div>
                
                <!-- Personal Information -->
                <div class="ism-field-row">
                    <div class="ism-field-group">
                        <label for="ism-firstname"><?php _e('Prénom *', 'interpeller-son-maire'); ?></label>
                        <input type="text" id="ism-firstname" name="firstname" required>
                    </div>
                    <div class="ism-field-group">
                        <label for="ism-lastname"><?php _e('Nom *', 'interpeller-son-maire'); ?></label>
                        <input type="text" id="ism-lastname" name="lastname" required>
                    </div>
                </div>
                
                <div class="ism-field-group">
                    <label for="ism-email"><?php _e('Email *', 'interpeller-son-maire'); ?></label>
                    <input type="email" id="ism-email" name="email" required>
                </div>
                
                <?php if ($atts['show_templates'] === 'true'): ?>
                <!-- Message Type Selection -->
                <div class="ism-field-group">
                    <label><?php _e('Type de message', 'interpeller-son-maire'); ?></label>
                    <div class="ism-message-type-selector">
                        <label class="ism-radio-card">
                            <input type="radio" name="message_type" value="template" checked>
                            <div class="ism-radio-content">
                                <div class="ism-radio-icon">📝</div>
                                <div class="ism-radio-title"><?php _e('Message type', 'interpeller-son-maire'); ?></div>
                                <div class="ism-radio-desc"><?php _e('Choisir parmi nos modèles', 'interpeller-son-maire'); ?></div>
                            </div>
                        </label>
                        <label class="ism-radio-card">
                            <input type="radio" name="message_type" value="custom">
                            <div class="ism-radio-content">
                                <div class="ism-radio-icon">✏️</div>
                                <div class="ism-radio-title"><?php _e('Message personnel', 'interpeller-son-maire'); ?></div>
                                <div class="ism-radio-desc"><?php _e('Rédiger votre propre message', 'interpeller-son-maire'); ?></div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Template Selection -->
                <div class="ism-field-group" id="ism-template-selection">
                    <label for="ism-template"><?php _e('Choisir un modèle', 'interpeller-son-maire'); ?></label>
                    <select id="ism-template" name="template_id">
                        <option value=""><?php _e('Sélectionner un modèle...', 'interpeller-son-maire'); ?></option>
                    </select>
                </div>
                <?php endif; ?>
                
                <!-- Message Content -->
                <div class="ism-field-group">
                    <label for="ism-message">
                        <span id="ism-message-label"><?php _e('Aperçu du message', 'interpeller-son-maire'); ?></span>
                    </label>
                    <textarea id="ism-message" 
                              name="message" 
                              rows="12" 
                              placeholder="<?php _e('Rédigez votre message...', 'interpeller-son-maire'); ?>"
                              required></textarea>
                </div>
                
                <!-- reCAPTCHA -->
                <div class="ism-recaptcha" id="ism-recaptcha"></div>
                
                <!-- Submit Button -->
                <div class="ism-submit-container">
                    <button type="submit" 
                            class="ism-submit-btn" 
                            style="background-color: <?php echo esc_attr($atts['button_color']); ?>"
                            disabled>
                        <span class="ism-btn-text"><?php _e('Envoyer le message', 'interpeller-son-maire'); ?></span>
                        <span class="ism-btn-loading" style="display: none;">
                            <span class="ism-spinner"></span>
                            <?php _e('Envoi en cours...', 'interpeller-son-maire'); ?>
                        </span>
                    </button>
                </div>
            </form>
            
            <!-- Success Message -->
            <div id="ism-success-message" class="ism-success" style="display: none;">
                <div class="ism-success-icon">✅</div>
                <h3><?php _e('Message envoyé avec succès !', 'interpeller-son-maire'); ?></h3>
                <p><?php _e('Votre message a été transmis au maire. Vous recevrez un accusé de réception par email.', 'interpeller-son-maire'); ?></p>
                <button type="button" class="ism-reset-btn"><?php _e('Envoyer un autre message', 'interpeller-son-maire'); ?></button>
            </div>
        </div>
        <?php
    }
}