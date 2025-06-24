<?php
global $wpdb;

// Handle form submissions
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'add_commune' && wp_verify_nonce($_POST['ism_nonce'], 'ism_add_commune')) {
        $table = $wpdb->prefix . 'ism_communes';

        if (empty($_POST['commune_name']) || empty($_POST['mayor_email'])) {
            echo '<div class="notice notice-error"><p>' . __('Veuillez renseigner au minimum le nom de la commune et l\'email du maire.', 'interpeller-son-maire') . '</p></div>';
        } else {
            $result = $wpdb->insert($table, [
                'name' => sanitize_text_field($_POST['commune_name']),
                'code_insee' => $_POST['code_insee'] !== '' ? sanitize_text_field($_POST['code_insee']) : null,
                'mayor_email' => sanitize_email($_POST['mayor_email']),
                'population' => isset($_POST['population']) ? absint($_POST['population']) : null,
                'region' => sanitize_text_field($_POST['region'])
            ]);

            if ($result) {
                echo '<div class="notice notice-success"><p>Commune ajoutée avec succès</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Erreur lors de l\'ajout de la commune</p></div>';
            }
        }
    }
}

// Get communes
$table = $wpdb->prefix . 'ism_communes';
$communes = $wpdb->get_results("SELECT * FROM $table ORDER BY name ASC");
?>

<div class="wrap">
    <h1><?php _e('Communes & Maires', 'interpeller-son-maire'); ?></h1>
    
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
                    <label for="code_insee"><?php _e('Code INSEE', 'interpeller-son-maire'); ?></label>
                    <input type="text" id="code_insee" name="code_insee">
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
                                <button class="ism-btn ism-btn-secondary edit-commune" style="margin-right: 0.5rem;"
                                    data-commune-id="<?php echo $commune->id; ?>"
                                    data-commune-name="<?php echo esc_attr($commune->name); ?>"
                                    data-code-insee="<?php echo esc_attr($commune->code_insee); ?>"
                                    data-mayor-email="<?php echo esc_attr($commune->mayor_email); ?>"
                                    data-population="<?php echo esc_attr($commune->population); ?>"
                                    data-region="<?php echo esc_attr($commune->region); ?>">
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

<!-- Edit Commune Modal -->
<div id="ism-edit-commune-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:10000;">
    <div style="background:#fff; padding:1.5rem; border-radius:8px; max-width:600px; width:100%;">
        <h3 style="margin-top:0;">
            <?php _e('Modifier la commune', 'interpeller-son-maire'); ?>
        </h3>
        <form id="ism-edit-commune-form">
            <?php wp_nonce_field('ism_edit_commune', 'ism_edit_commune_nonce'); ?>
            <input type="hidden" id="edit_commune_id" name="commune_id">
            <div class="ism-form-group" style="margin-bottom:1rem;">
                <label for="edit_commune_name"><?php _e('Nom de la commune', 'interpeller-son-maire'); ?></label>
                <input type="text" id="edit_commune_name" name="commune_name" required>
            </div>
            <div class="ism-form-group" style="margin-bottom:1rem;">
                <label for="edit_code_insee"><?php _e('Code INSEE', 'interpeller-son-maire'); ?></label>
                <input type="text" id="edit_code_insee" name="code_insee">
            </div>
            <div class="ism-form-group" style="margin-bottom:1rem;">
                <label for="edit_mayor_email"><?php _e('Email du maire', 'interpeller-son-maire'); ?></label>
                <input type="email" id="edit_mayor_email" name="mayor_email" required>
            </div>
            <div class="ism-form-group" style="margin-bottom:1rem;">
                <label for="edit_population"><?php _e('Population', 'interpeller-son-maire'); ?></label>
                <input type="number" id="edit_population" name="population">
            </div>
            <div class="ism-form-group" style="margin-bottom:1rem;">
                <label for="edit_region"><?php _e('Région', 'interpeller-son-maire'); ?></label>
                <input type="text" id="edit_region" name="region">
            </div>
            <button type="submit" class="ism-btn ism-btn-primary" style="margin-right:0.5rem;">
                <?php _e('Enregistrer', 'interpeller-son-maire'); ?>
            </button>
            <button type="button" class="ism-btn ism-btn-secondary" id="ism-edit-commune-cancel">
                <?php _e('Annuler', 'interpeller-son-maire'); ?>
            </button>
        </form>
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

    $('.edit-commune').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        $('#edit_commune_id').val(btn.data('commune-id'));
        $('#edit_commune_name').val(btn.data('commune-name'));
        $('#edit_code_insee').val(btn.data('code-insee'));
        $('#edit_mayor_email').val(btn.data('mayor-email'));
        $('#edit_population').val(btn.data('population'));
        $('#edit_region').val(btn.data('region'));
        $('#ism-edit-commune-modal').css('display', 'flex');
    });

    $('#ism-edit-commune-cancel').on('click', function() {
        $('#ism-edit-commune-modal').css('display', 'none');
    });

    $('#ism-edit-commune-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'ism_edit_commune',
                nonce: $('#ism_edit_commune_nonce').val(),
                commune_id: $('#edit_commune_id').val(),
                commune_name: $('#edit_commune_name').val(),
                code_insee: $('#edit_code_insee').val(),
                mayor_email: $('#edit_mayor_email').val(),
                population: $('#edit_population').val(),
                region: $('#edit_region').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + response.data);
                }
            },
            error: function() {
                alert('Erreur lors de la mise à jour');
            }
        });
    });
});
</script>