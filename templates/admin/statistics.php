<?php
use InterpellerSonMaire\Core\Logger;

$stats = Logger::getStatistics(30);
$weekly_stats = Logger::getStatistics(7);
?>

<div class="wrap">
    <h1><?php _e('Statistiques', 'interpeller-son-maire'); ?></h1>
    
    <div class="ism-admin-header">
        <div class="ism-admin-welcome">
            <h2><?php _e('Analyse d\'utilisation', 'interpeller-son-maire'); ?></h2>
            <p><?php _e('Consultez les statistiques détaillées d\'utilisation du plugin et l\'engagement citoyen.', 'interpeller-son-maire'); ?></p>
        </div>
    </div>
    
    <!-- Key Metrics -->
    <div class="ism-dashboard-widgets">
        <div class="ism-widget">
            <div class="ism-widget-icon">📧</div>
            <div class="ism-widget-content">
                <h3><?php echo number_format($stats['total_messages']); ?></h3>
                <p><?php _e('Messages totaux', 'interpeller-son-maire'); ?></p>
            </div>
        </div>
        
        <div class="ism-widget">
            <div class="ism-widget-icon">📈</div>
            <div class="ism-widget-content">
                <h3><?php echo number_format($stats['messages_this_week']); ?></h3>
                <p><?php _e('Cette semaine', 'interpeller-son-maire'); ?></p>
            </div>
        </div>
        
        <div class="ism-widget">
            <div class="ism-widget-icon">🏛️</div>
            <div class="ism-widget-content">
                <h3><?php echo count($stats['top_communes']); ?></h3>
                <p><?php _e('Communes actives', 'interpeller-son-maire'); ?></p>
            </div>
        </div>
        
        <div class="ism-widget">
            <div class="ism-widget-icon">📊</div>
            <div class="ism-widget-content">
                <h3><?php echo !empty($stats['daily_stats']) ? round(array_sum(array_column($stats['daily_stats'], 'count')) / count($stats['daily_stats'])) : 0; ?></h3>
                <p><?php _e('Moyenne/jour', 'interpeller-son-maire'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="ism-dashboard-content">
        <!-- Top Communes -->
        <div class="ism-dashboard-left">
            <div class="ism-panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3><?php _e('Top communes (30 derniers jours)', 'interpeller-son-maire'); ?></h3>
                    <button class="ism-btn ism-btn-secondary" onclick="exportData('communes')">
                        <?php _e('Exporter CSV', 'interpeller-son-maire'); ?>
                    </button>
                </div>
                
                <?php if (!empty($stats['top_communes'])): ?>
                    <ul class="ism-top-list">
                        <?php foreach (array_slice($stats['top_communes'], 0, 10) as $index => $commune): ?>
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
            
            <!-- Daily Activity Chart -->
            <div class="ism-panel" style="margin-top: 2rem;">
                <h3><?php _e('Activité des 7 derniers jours', 'interpeller-son-maire'); ?></h3>
                <div class="ism-chart-container">
                    <canvas id="ism-stats-chart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Template Usage & Export -->
        <div class="ism-dashboard-right">
            <div class="ism-panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3><?php _e('Modèles populaires', 'interpeller-son-maire'); ?></h3>
                    <button class="ism-btn ism-btn-secondary" onclick="exportData('templates')">
                        <?php _e('Exporter', 'interpeller-son-maire'); ?>
                    </button>
                </div>
                
                <?php if (!empty($stats['template_usage'])): ?>
                    <ul class="ism-top-list">
                        <?php foreach (array_slice($stats['template_usage'], 0, 5) as $index => $template): ?>
                            <li>
                                <span class="ism-rank"><?php echo $index + 1; ?></span>
                                <span class="ism-name"><?php echo esc_html($template->title); ?></span>
                                <span class="ism-count"><?php echo number_format($template->count); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><?php _e('Aucune donnée disponible', 'interpeller-son-maire'); ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Export Options -->
            <div class="ism-panel" style="margin-top: 2rem;">
                <h3><?php _e('Exports', 'interpeller-son-maire'); ?></h3>
                <div style="display: grid; gap: 1rem;">
                    <button class="ism-btn ism-btn-primary" onclick="exportData('monthly')">
                        <?php _e('📊 Rapport mensuel (CSV)', 'interpeller-son-maire'); ?>
                    </button>
                    <button class="ism-btn ism-btn-primary" onclick="exportData('yearly')">
                        <?php _e('📈 Rapport annuel (Excel)', 'interpeller-son-maire'); ?>
                    </button>
                    <button class="ism-btn ism-btn-primary" onclick="exportData('detailed')">
                        <?php _e('📋 Statistiques détaillées (PDF)', 'interpeller-son-maire'); ?>
                    </button>
                </div>
            </div>
            
            <!-- Data Management -->
            <div class="ism-panel" style="margin-top: 2rem;">
                <h3><?php _e('Gestion des données', 'interpeller-son-maire'); ?></h3>
                <p style="margin-bottom: 1rem; color: #6b7280; font-size: 0.875rem;">
                    <?php _e('Les logs sont automatiquement purgés après 12 mois pour respecter le RGPD.', 'interpeller-son-maire'); ?>
                </p>
                <button class="ism-btn ism-btn-danger" onclick="purgeOldLogs()">
                    <?php _e('🗑️ Purger les anciens logs', 'interpeller-son-maire'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Chart data
window.ismChartData = <?php echo json_encode($stats['daily_stats']); ?>;

function exportData(type) {
    var url = ajaxurl + '?action=ism_export_data&type=' + type + '&nonce=<?php echo wp_create_nonce('ism_export'); ?>';
    window.open(url, '_blank');
}

function purgeOldLogs() {
    if (confirm('Êtes-vous sûr de vouloir purger les anciens logs ? Cette action est irréversible.')) {
        jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'ism_purge_logs',
                nonce: '<?php echo wp_create_nonce('ism_purge_logs'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('Logs purgés avec succès: ' + response.data.deleted + ' entrées supprimées');
                    location.reload();
                } else {
                    alert('Erreur lors de la purge: ' + response.data);
                }
            }
        });
    }
}
</script>