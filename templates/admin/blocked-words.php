<?php
global $wpdb;

// Handle form submissions
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'save_blocked_words' && wp_verify_nonce($_POST['ism_nonce'], 'ism_save_blocked_words')) {
        $words = sanitize_textarea_field($_POST['blocked_words']);
        $words_array = array_filter(array_map('trim', explode("\n", $words)));
        
        // Clear existing words
        $table = $wpdb->prefix . 'ism_blocked_words';
        $wpdb->query("DELETE FROM $table");
        
        // Insert new words
        foreach ($words_array as $word) {
            $wpdb->insert($table, [
                'word' => $word,
                'is_regex' => 0
            ]);
        }
        
        echo '<div class="notice notice-success"><p>Mots interdits sauvegardés avec succès</p></div>';
    }
}

// Get current blocked words
$table = $wpdb->prefix . 'ism_blocked_words';
$blocked_words = $wpdb->get_results("SELECT word FROM $table ORDER BY word ASC");
$words_text = implode("\n", array_column($blocked_words, 'word'));
?>

<div class="wrap">
    <h1><?php _e('Mots interdits', 'interpeller-son-maire'); ?></h1>
    <input type="hidden" id="ism_admin_nonce" value="<?php echo wp_create_nonce('ism_admin'); ?>">
    
    <div class="ism-admin-header">
        <div class="ism-admin-welcome">
            <h2><?php _e('Modération des contenus', 'interpeller-son-maire'); ?></h2>
            <p><?php _e('Gérez la liste des mots interdits pour filtrer les messages inappropriés.', 'interpeller-son-maire'); ?></p>
        </div>
    </div>
    
    <div class="ism-panel">
        <h3><?php _e('Liste des mots interdits', 'interpeller-son-maire'); ?></h3>
        <p><?php _e('Entrez un mot par ligne. Les messages contenant ces mots seront automatiquement rejetés.', 'interpeller-son-maire'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('ism_save_blocked_words', 'ism_nonce'); ?>
            <input type="hidden" name="action" value="save_blocked_words">
            
            <div class="ism-form-group" style="margin-bottom: 1.5rem;">
                <label for="blocked_words"><?php _e('Mots interdits (un par ligne)', 'interpeller-son-maire'); ?></label>
                <textarea id="blocked_words" name="blocked_words" rows="15" style="width: 100%; max-width: 600px;"><?php echo esc_textarea($words_text); ?></textarea>
                <p class="description">
                    <?php _e('Exemples: spam, insulte, violence, harcèlement', 'interpeller-son-maire'); ?>
                </p>
            </div>
            
            <button type="submit" class="ism-btn ism-btn-primary">
                <?php _e('Sauvegarder les mots interdits', 'interpeller-son-maire'); ?>
            </button>
        </form>
    </div>
    
    <div class="ism-panel" style="margin-top: 2rem;">
        <h3><?php _e('Statistiques de modération', 'interpeller-son-maire'); ?></h3>
        
        <div class="ism-dashboard-widgets">
            <div class="ism-widget">
                <div class="ism-widget-icon">🚫</div>
                <div class="ism-widget-content">
                    <h3><?php echo count($blocked_words); ?></h3>
                    <p><?php _e('Mots interdits actifs', 'interpeller-son-maire'); ?></p>
                </div>
            </div>
            
            <div class="ism-widget">
                <div class="ism-widget-icon">⚠️</div>
                <div class="ism-widget-content">
                    <h3>0</h3>
                    <p><?php _e('Messages bloqués ce mois', 'interpeller-son-maire'); ?></p>
                </div>
            </div>
            
            <div class="ism-widget">
                <div class="ism-widget-icon">✅</div>
                <div class="ism-widget-content">
                    <h3>100%</h3>
                    <p><?php _e('Taux de validation', 'interpeller-son-maire'); ?></p>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 1.5rem;">
            <h4><?php _e('Conseils pour une modération efficace', 'interpeller-son-maire'); ?></h4>
            <ul style="margin-left: 1.5rem;">
                <li><?php _e('Ajoutez les variantes d\'orthographe des mots problématiques', 'interpeller-son-maire'); ?></li>
                <li><?php _e('Évitez les mots trop génériques qui pourraient bloquer des messages légitimes', 'interpeller-son-maire'); ?></li>
                <li><?php _e('Testez régulièrement votre liste avec des exemples de messages', 'interpeller-son-maire'); ?></li>
                <li><?php _e('Considérez le contexte : certains mots peuvent être appropriés selon le sujet', 'interpeller-son-maire'); ?></li>
            </ul>
        </div>
    </div>
</div>