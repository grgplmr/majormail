<?php
global $wpdb;

// Handle form submissions
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'add_template' && wp_verify_nonce($_POST['ism_nonce'], 'ism_add_template')) {
        $table = $wpdb->prefix . 'ism_templates';
        $result = $wpdb->insert($table, [
            'title' => sanitize_text_field($_POST['template_title']),
            'subject' => sanitize_text_field($_POST['template_subject']),
            'content' => wp_kses_post($_POST['template_content']),
            'category' => sanitize_text_field($_POST['template_category'])
        ]);
        
        if ($result) {
            echo '<div class="notice notice-success"><p>Modèle ajouté avec succès</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Erreur lors de l\'ajout du modèle</p></div>';
        }
    }
}

// Get templates
$table = $wpdb->prefix . 'ism_templates';
$templates = $wpdb->get_results("SELECT * FROM $table WHERE is_active = 1 ORDER BY usage_count DESC, title ASC");
?>

<div class="wrap">
    <h1><?php _e('Modèles de messages', 'interpeller-son-maire'); ?></h1>
    <input type="hidden" id="ism_admin_nonce" value="<?php echo wp_create_nonce('ism_admin'); ?>">
    
    <div class="ism-admin-header">
        <div class="ism-admin-welcome">
            <h2><?php _e('Gestion des modèles', 'interpeller-son-maire'); ?></h2>
            <p><?php _e('Créez et gérez les modèles de messages pré-rédigés pour les citoyens.', 'interpeller-son-maire'); ?></p>
        </div>
    </div>
    
    <!-- Add New Template Form -->
    <div class="ism-panel" style="margin-bottom: 2rem;">
        <h3><?php _e('Ajouter un nouveau modèle', 'interpeller-son-maire'); ?></h3>
        <form method="post" action="">
            <?php wp_nonce_field('ism_add_template', 'ism_nonce'); ?>
            <input type="hidden" name="action" value="add_template">
            
            <div class="ism-field-row" style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="ism-form-group">
                    <label for="template_title"><?php _e('Titre du modèle *', 'interpeller-son-maire'); ?></label>
                    <input type="text" id="template_title" name="template_title" required>
                </div>
                <div class="ism-form-group">
                    <label for="template_category"><?php _e('Catégorie', 'interpeller-son-maire'); ?></label>
                    <select id="template_category" name="template_category">
                        <option value="waste"><?php _e('Déchets', 'interpeller-son-maire'); ?></option>
                        <option value="environment"><?php _e('Environnement', 'interpeller-son-maire'); ?></option>
                        <option value="services"><?php _e('Services publics', 'interpeller-son-maire'); ?></option>
                        <option value="transport"><?php _e('Transport', 'interpeller-son-maire'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="ism-form-group" style="margin-bottom: 1rem;">
                <label for="template_subject"><?php _e('Objet de l\'email *', 'interpeller-son-maire'); ?></label>
                <input type="text" id="template_subject" name="template_subject" required>
            </div>
            
            <div class="ism-form-group" style="margin-bottom: 1rem;">
                <label for="template_content"><?php _e('Contenu du message *', 'interpeller-son-maire'); ?></label>
                <textarea id="template_content" name="template_content" rows="12" required></textarea>
                <p class="description">
                    <?php _e('Variables disponibles: {prenom}, {nom}, {commune}, {email}', 'interpeller-son-maire'); ?>
                </p>
            </div>
            
            <button type="submit" class="ism-btn ism-btn-primary">
                <?php _e('Ajouter le modèle', 'interpeller-son-maire'); ?>
            </button>
        </form>
    </div>
    
    <!-- Templates List -->
    <div class="ism-panel">
        <h3><?php _e('Modèles existants', 'interpeller-son-maire'); ?></h3>
        
        <?php if (!empty($templates)): ?>
            <div style="display: grid; gap: 1.5rem;">
                <?php foreach ($templates as $template): ?>
                    <div class="template-item" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.5rem; background: white;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div>
                                <h4 style="margin: 0 0 0.5rem 0; font-size: 1.125rem; font-weight: 600;">
                                    <?php echo esc_html($template->title); ?>
                                </h4>
                                <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">
                                    <?php _e('Catégorie:', 'interpeller-son-maire'); ?> <?php echo esc_html($template->category); ?> • 
                                    <?php _e('Utilisé', 'interpeller-son-maire'); ?> <?php echo number_format($template->usage_count); ?> <?php _e('fois', 'interpeller-son-maire'); ?>
                                </p>
                            </div>
                            <div>
                                <button class="ism-btn ism-btn-secondary" style="margin-right: 0.5rem;">
                                    <?php _e('Modifier', 'interpeller-son-maire'); ?>
                                </button>
                                <button class="ism-btn ism-btn-danger delete-template" data-template-id="<?php echo $template->id; ?>">
                                    <?php _e('Supprimer', 'interpeller-son-maire'); ?>
                                </button>
                            </div>
                        </div>
                        
                        <div style="background: #f9fafb; border-radius: 6px; padding: 1rem;">
                            <p style="margin: 0 0 0.5rem 0; font-weight: 600; color: #374151;">
                                <?php _e('Objet:', 'interpeller-son-maire'); ?> <?php echo esc_html($template->subject); ?>
                            </p>
                            <div style="color: #6b7280; font-size: 0.875rem; white-space: pre-line;">
                                <?php echo esc_html(substr($template->content, 0, 300)); ?>
                                <?php if (strlen($template->content) > 300): ?>
                                    <em>... (<?php _e('message tronqué', 'interpeller-son-maire'); ?>)</em>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p><?php _e('Aucun modèle trouvé. Ajoutez votre premier modèle ci-dessus.', 'interpeller-son-maire'); ?></p>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.delete-template').on('click', function(e) {
        e.preventDefault();
        if (confirm('Êtes-vous sûr de vouloir supprimer ce modèle ?')) {
            var templateId = $(this).data('template-id');
            var item = $(this).closest('.template-item');
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'ism_delete_template',
                    template_id: templateId,
                    nonce: '<?php echo wp_create_nonce('ism_delete_template'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        item.fadeOut();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                }
            });
        }
    });
});
</script>