<?php
use InterpellerSonMaire\Core\Logger;

$stats = Logger::getStatistics(30);
?>

<div class="wrap">
    <h1><?php _e('Interpeller son Maire - Tableau de bord', 'interpeller-son-maire'); ?></h1>
    
    <div class="ism-admin-header">
        <div class="ism-admin-welcome">
            <h2><?php _e('Bienvenue dans l\'administration', 'interpeller-son-maire'); ?></h2>
            <p><?php _e('Gérez les communes, modèles de messages et consultez les statistiques d\'utilisation.', 'interpeller-son-maire'); ?></p>
        </div>
    </div>
    
    <div class="ism-dashboard-widgets">
        <div class="ism-widget">
            <div class="ism-widget-icon ism-icon-messages">📧</div>
            <div class="ism-widget-content">
                <h3><?php echo number_format($stats['total_messages']); ?></h3>
                <p><?php _e('Messages totaux', 'interpeller-son-maire'); ?></p>
            </div>
        </div>
        
        <div class="ism-widget">
            <div class="ism-widget-icon ism-icon-week">📈</div>
            <div class="ism-widget-content">
                <h3><?php echo number_format($stats['messages_this_week']); ?></h3>
                <p><?php _e('Cette semaine', 'interpeller-son-maire'); ?></p>
            </div>
        </div>
        
        <div class="ism-widget">
            <div class="ism-widget-icon ism-icon-communes">🏛️</div>
            <div class="ism-widget-content">
                <h3><?php echo count($stats['top_communes']); ?></h3>
                <p><?php _e('Communes actives', 'interpeller-son-maire'); ?></p>
            </div>
        </div>
        
        <div class="ism-widget">
            <div class="ism-widget-icon ism-icon-templates">📝</div>
            <div class="ism-widget-content">
                <h3><?php echo count($stats['template_usage']); ?></h3>
                <p><?php _e('Modèles utilisés', 'interpeller-son-maire'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="ism-dashboard-content">
        <div class="ism-dashboard-left">
            <div class="ism-panel">
                <h3><?php _e('Top communes (30 derniers jours)', 'interpeller-son-maire'); ?></h3>
                <?php if (!empty($stats['top_communes'])): ?>
                    <ul class="ism-top-list">
                        <?php foreach (array_slice($stats['top_communes'], 0, 5) as $index => $commune): ?>
                            <li>
                                <span class="ism-rank"><?php echo $index + 1; ?></span>
                                <span class="ism-name"><?php echo esc_html($commune->name); ?></span>
                                <span class="ism-count"><?php echo number_format($commune->count); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><?php _e('Aucune donnée disponible', 'interpeller-son-maire'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="ism-dashboard-right">
            <div class="ism-panel">
                <h3><?php _e('Actions rapides', 'interpeller-son-maire'); ?></h3>
                <div class="ism-quick-actions">
                    <a href="<?php echo admin_url('admin.php?page=interpeller-son-maire-communes'); ?>" class="ism-quick-action">
                        <div class="ism-action-icon">🏛️</div>
                        <div class="ism-action-text">
                            <strong><?php _e('Gérer les communes', 'interpeller-son-maire'); ?></strong>
                            <span><?php _e('Ajouter, modifier ou supprimer des communes', 'interpeller-son-maire'); ?></span>
                        </div>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=interpeller-son-maire-templates'); ?>" class="ism-quick-action">
                        <div class="ism-action-icon">📝</div>
                        <div class="ism-action-text">
                            <strong><?php _e('Modèles de messages', 'interpeller-son-maire'); ?></strong>
                            <span><?php _e('Créer et modifier les modèles', 'interpeller-son-maire'); ?></span>
                        </div>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=interpeller-son-maire-stats'); ?>" class="ism-quick-action">
                        <div class="ism-action-icon">📊</div>
                        <div class="ism-action-text">
                            <strong><?php _e('Voir les statistiques', 'interpeller-son-maire'); ?></strong>
                            <span><?php _e('Analyser l\'utilisation du plugin', 'interpeller-son-maire'); ?></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="ism-shortcode-info">
        <h3><?php _e('Utilisation du shortcode', 'interpeller-son-maire'); ?></h3>
        <p><?php _e('Pour afficher le formulaire de contact sur vos pages, utilisez le shortcode suivant :', 'interpeller-son-maire'); ?></p>
        <code>[interpeller_maire]</code>
        
        <h4><?php _e('Options disponibles :', 'interpeller-son-maire'); ?></h4>
        <ul>
            <li><code>default_template=""</code> - <?php _e('ID du modèle par défaut', 'interpeller-son-maire'); ?></li>
            <li><code>button_color="#2563eb"</code> - <?php _e('Couleur du bouton d\'envoi', 'interpeller-son-maire'); ?></li>
            <li><code>show_templates="true"</code> - <?php _e('Afficher la sélection de modèles', 'interpeller-son-maire'); ?></li>
        </ul>
        
        <p><strong><?php _e('Exemple :', 'interpeller-son-maire'); ?></strong></p>
        <code>[interpeller_maire button_color="#059669" show_templates="false"]</code>
    </div>
</div>