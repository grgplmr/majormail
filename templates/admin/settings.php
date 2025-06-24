<?php
use InterpellerSonMaire\Admin\AdminMenu;

$settings = get_option('ism_settings', []);

// Handle form submission
if (isset($_POST['submit']) && wp_verify_nonce($_POST['ism_nonce'], 'ism_save_settings')) {
    $admin = new AdminMenu();
    $new_settings = $admin->sanitizeSettings($_POST);

    update_option('ism_settings', $new_settings);
    $settings = $new_settings;
    echo '<div class="notice notice-success"><p>Paramètres sauvegardés avec succès</p></div>';
}
?>

<div class="wrap">
    <h1><?php _e('Réglages', 'interpeller-son-maire'); ?></h1>
    
    <div class="ism-admin-header">
        <div class="ism-admin-welcome">
            <h2><?php _e('Configuration du plugin', 'interpeller-son-maire'); ?></h2>
            <p><?php _e('Personnalisez le comportement et les messages du plugin selon vos besoins.', 'interpeller-son-maire'); ?></p>
        </div>
    </div>
    
    <form method="post" action="" id="ism-settings-form">
        <?php wp_nonce_field('ism_save_settings', 'ism_nonce'); ?>
        <input type="hidden" id="ism_admin_nonce" value="<?php echo wp_create_nonce('ism_save_settings'); ?>">
        
        <!-- Email Configuration -->
        <div class="ism-panel" style="margin-bottom: 2rem;">
            <h3><?php _e('Configuration des emails', 'interpeller-son-maire'); ?></h3>
            
            <div class="ism-form-group">
                <label for="email_subject"><?php _e('Objet par défaut des emails', 'interpeller-son-maire'); ?></label>
                <input type="text" 
                       id="email_subject" 
                       name="email_subject" 
                       value="<?php echo esc_attr($settings['email_subject'] ?? 'Message de {prenom} {nom} - {commune}'); ?>"
                       style="width: 100%; max-width: 600px;">
                <p class="description">
                    <?php _e('Variables disponibles: {prenom}, {nom}, {commune}, {email}', 'interpeller-son-maire'); ?>
                </p>
            </div>
            
            <div class="ism-form-group">
                <label for="confirmation_message"><?php _e('Message d\'accusé de réception', 'interpeller-son-maire'); ?></label>
                <textarea id="confirmation_message" 
                          name="confirmation_message" 
                          rows="6"
                          style="width: 100%; max-width: 600px;"><?php echo esc_textarea($settings['confirmation_message'] ?? 'Votre message a bien été transmis au maire de {commune}. Vous devriez recevoir une réponse dans les prochains jours.'); ?></textarea>
                <p class="description">
                    <?php _e('Message envoyé aux citoyens après l\'envoi de leur message. Variable disponible: {commune}', 'interpeller-son-maire'); ?>
                </p>
            </div>
        </div>
        
        <!-- Security Settings -->
        <div class="ism-panel" style="margin-bottom: 2rem;">
            <h3><?php _e('Sécurité et protection', 'interpeller-son-maire'); ?></h3>
            
            <div class="ism-form-group">
                <label>
                    <input type="checkbox" 
                           name="recaptcha_enabled" 
                           <?php checked($settings['recaptcha_enabled'] ?? true); ?>>
                    <?php _e('Activer Google reCAPTCHA v3', 'interpeller-son-maire'); ?>
                </label>
                <p class="description">
                    <?php _e('Protection anti-spam recommandée. Configurez vos clés dans wp-config.php', 'interpeller-son-maire'); ?>
                </p>
            </div>
            
            <?php if (defined('ISM_RECAPTCHA_SITE_KEY') && defined('ISM_RECAPTCHA_SECRET')): ?>
                <div class="ism-notice ism-notice-success">
                    <p>✅ <?php _e('Clés reCAPTCHA configurées', 'interpeller-son-maire'); ?></p>
                </div>
            <?php else: ?>
                <div class="ism-notice ism-notice-warning">
                    <p>⚠️ <?php _e('Clés reCAPTCHA non configurées. Ajoutez dans wp-config.php:', 'interpeller-son-maire'); ?></p>
                    <code>define('ISM_RECAPTCHA_SITE_KEY', 'votre_cle_site');<br>define('ISM_RECAPTCHA_SECRET', 'votre_cle_secrete');</code>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Data Management -->
        <div class="ism-panel" style="margin-bottom: 2rem;">
            <h3><?php _e('Gestion des données (RGPD)', 'interpeller-son-maire'); ?></h3>
            
            <div class="ism-form-group">
                <label>
                    <input type="checkbox" 
                           name="auto_purge_enabled" 
                           <?php checked($settings['auto_purge_enabled'] ?? true); ?>>
                    <?php _e('Purge automatique des logs', 'interpeller-son-maire'); ?>
                </label>
                <p class="description">
                    <?php _e('Supprime automatiquement les logs anciens pour respecter le RGPD', 'interpeller-son-maire'); ?>
                </p>
            </div>
            
            <div class="ism-form-group">
                <label for="purge_delay_months"><?php _e('Délai de conservation (mois)', 'interpeller-son-maire'); ?></label>
                <input type="number" 
                       id="purge_delay_months" 
                       name="purge_delay_months" 
                       value="<?php echo esc_attr($settings['purge_delay_months'] ?? 12); ?>"
                       min="1" 
                       max="60"
                       style="width: 100px;">
                <p class="description">
                    <?php _e('Durée de conservation des logs avant suppression automatique', 'interpeller-son-maire'); ?>
                </p>
            </div>
        </div>
        
        <!-- Integration Settings -->
        <div class="ism-panel" style="margin-bottom: 2rem;">
            <h3><?php _e('Intégrations', 'interpeller-son-maire'); ?></h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <h4><?php _e('FluentSMTP', 'interpeller-son-maire'); ?></h4>
                    <?php if (is_plugin_active('fluent-smtp/fluent-smtp.php')): ?>
                        <div class="ism-notice ism-notice-success">
                            <p>✅ <?php _e('FluentSMTP activé et configuré', 'interpeller-son-maire'); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="ism-notice ism-notice-warning">
                            <p>⚠️ <?php _e('FluentSMTP non détecté. Installation recommandée pour un envoi d\'emails fiable.', 'interpeller-son-maire'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <h4><?php _e('FluentCRM', 'interpeller-son-maire'); ?></h4>
                    <?php if (is_plugin_active('fluent-crm/fluent-crm.php')): ?>
                        <div class="ism-notice ism-notice-success">
                            <p>✅ <?php _e('FluentCRM activé - Gestion des contacts maires disponible', 'interpeller-son-maire'); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="ism-notice ism-notice-info">
                            <p>ℹ️ <?php _e('FluentCRM non détecté. Fonctionnalité optionnelle pour la gestion avancée des contacts.', 'interpeller-son-maire'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- System Information -->
        <div class="ism-panel" style="margin-bottom: 2rem;">
            <h3><?php _e('Informations système', 'interpeller-son-maire'); ?></h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <p><strong><?php _e('Version du plugin:', 'interpeller-son-maire'); ?></strong> <?php echo ISM_PLUGIN_VERSION; ?></p>
                    <p><strong><?php _e('Version WordPress:', 'interpeller-son-maire'); ?></strong> <?php echo get_bloginfo('version'); ?></p>
                    <p><strong><?php _e('Version PHP:', 'interpeller-son-maire'); ?></strong> <?php echo PHP_VERSION; ?></p>
                </div>
                <div>
                    <p><strong><?php _e('Thème actif:', 'interpeller-son-maire'); ?></strong> <?php echo wp_get_theme()->get('Name'); ?></p>
                    <p><strong><?php _e('Multisite:', 'interpeller-son-maire'); ?></strong> <?php echo is_multisite() ? 'Oui' : 'Non'; ?></p>
                    <p><strong><?php _e('Debug WordPress:', 'interpeller-son-maire'); ?></strong> <?php echo WP_DEBUG ? 'Activé' : 'Désactivé'; ?></p>
                </div>
            </div>
        </div>
        
        <button type="submit" name="submit" class="ism-btn ism-btn-primary">
            <?php _e('Sauvegarder les paramètres', 'interpeller-son-maire'); ?>
        </button>
    </form>
</div>