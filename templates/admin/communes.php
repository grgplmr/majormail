<?php
global $wpdb;

// Handle form submissions
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'add_commune' && wp_verify_nonce($_POST['ism_nonce'], 'ism_add_commune')) {
        $table = $wpdb->prefix . 'ism_communes';
        $result = $wpdb->insert($table, [
            'name' => sanitize_text_field($_POST['commune_name']),
            'code_insee' => sanitize_text_field($_POST['code_insee']),
            'mayor_email' => sanitize_email($_POST['mayor_email']),
            'population' => absint($_POST['population']),
            'region' => sanitize_text_field($_POST['region'])
        ]);
        
        if ($result) {
            echo '<div class="notice notice-success"><p>Commune ajoutée avec succès</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Erreur lors de l\'ajout de la commune</p></div>';
        }
    }
}

// Get communes
$table = $wpdb->prefix . 'ism_communes';
$communes = $wpdb->get_results("SELECT * FROM $table ORDER BY name ASC");
?>

<div class="wrap">
    <h1><?php _e('Communes & Maires', 'interpeller-son-maire'); ?></h1>
    <input type="hidden" id="ism_admin_nonce" value="<?php echo wp_create_nonce('ism_admin'); ?>">
    
    <div class="ism-admin-header">
        <div class="ism-admin-welcome">
            <h2><?php _e('Gestion des communes', 'interpeller-son-maire'); ?></h2>
            <p><?php _e('Ajoutez, modifiez ou supprimez les communes et leurs maires.', 'interpeller-son-maire'); ?></p>
        </div>
    </div>
    
    <!-- Add New Commune Form -->
    <div class="ism-panel" style="margin-bottom: 2rem;">
        <h3><?php _e('Ajouter une nouvelle commune', 'interpeller-son-maire'); ?></h3>
        <form method="post" action="">
            <?php wp_nonce_field('ism_add_commune', 'ism_nonce'); ?>
            <input type="hidden" name="action" value="add_commune">
            
            <div class="ism-field-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="ism-form-group">
                    <label for="commune_name"><?php _e('Nom de la commune *', 'interpeller-son-maire'); ?></label>
                    <input type="text" id="commune_name" name="commune_name" required>
                </div>
                <div class="ism-form-group">
                    <label for="code_insee"><?php _e('Code INSEE *', 'interpeller-son-maire'); ?></label>
                    <input type="text" id="code_insee" name="code_insee" required>
                </div>
            </div>
            
            <div class="ism-field-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="ism-form-group">
                    <label for="mayor_email"><?php _e('Email du maire *', 'interpeller-son-maire'); ?></label>
                    <input type="email" id="mayor_email" name="mayor_email" required>
                </div>
                <div class="ism-form-group">
                    <label for="population"><?php _e('Population', 'interpeller-son-maire'); ?></label>
                    <input type="number" id="population" name="population">
                </div>
            </div>
            
            <div class="ism-form-group" style="margin-bottom: 1rem;">
                <label for="region"><?php _e('Région', 'interpeller-son-maire'); ?></label>
                <input type="text" id="region" name="region">
            </div>
            
            <button type="submit" class="ism-btn ism-btn-primary">
                <?php _e('Ajouter la commune', 'interpeller-son-maire'); ?>
            </button>
        </form>
    </div>
    
    <!-- Communes List -->
    <div class="ism-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3><?php _e('Communes existantes', 'interpeller-son-maire'); ?></h3>
            <div>
                <button class="ism-btn ism-btn-secondary" onclick="document.getElementById('csv-import').click()">
                    <?php _e('Importer CSV', 'interpeller-son-maire'); ?>
                </button>
                <input type="file" id="csv-import" accept=".csv" style="display: none;">
            </div>
        </div>
        
        <?php if (!empty($communes)): ?>
            <table class="ism-table">
                <thead>
                    <tr>
                        <th><?php _e('Commune', 'interpeller-son-maire'); ?></th>
                        <th><?php _e('Code INSEE', 'interpeller-son-maire'); ?></th>
                        <th><?php _e('Population', 'interpeller-son-maire'); ?></th>
                        <th><?php _e('Email Maire', 'interpeller-son-maire'); ?></th>
                        <th><?php _e('Région', 'interpeller-son-maire'); ?></th>
                        <th><?php _e('Actions', 'interpeller-son-maire'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($communes as $commune): ?>
                        <tr>
                            <td><strong><?php echo esc_html($commune->name); ?></strong></td>
                            <td><?php echo esc_html($commune->code_insee); ?></td>
                            <td><?php echo number_format($commune->population); ?></td>
                            <td><?php echo esc_html($commune->mayor_email); ?></td>
                            <td><?php echo esc_html($commune->region); ?></td>
                            <td>
                                <button class="ism-btn ism-btn-secondary" style="margin-right: 0.5rem;">
                                    <?php _e('Modifier', 'interpeller-son-maire'); ?>
                                </button>
                                <button class="ism-btn ism-btn-danger delete-commune" data-commune-id="<?php echo $commune->id; ?>">
                                    <?php _e('Supprimer', 'interpeller-son-maire'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php _e('Aucune commune trouvée. Ajoutez votre première commune ci-dessus.', 'interpeller-son-maire'); ?></p>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.delete-commune').on('click', function(e) {
        e.preventDefault();
        if (confirm('Êtes-vous sûr de vouloir supprimer cette commune ?')) {
            var communeId = $(this).data('commune-id');
            var row = $(this).closest('tr');
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'ism_delete_commune',
                    commune_id: communeId,
                    nonce: '<?php echo wp_create_nonce('ism_delete_commune'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        row.fadeOut();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                }
            });
        }
    });
});
</script>