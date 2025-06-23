// Plugin Interpeller son Maire - Admin JavaScript

(function($) {
    'use strict';
    
    class InterpellerSonMaireAdmin {
        constructor() {
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.initCharts();
        }
        
        bindEvents() {
            // Add commune form
            $('#add-commune-form').on('submit', (e) => this.handleAddCommune(e));
            
            // Delete commune
            $('.delete-commune').on('click', (e) => this.handleDeleteCommune(e));
            
            // Import CSV
            $('#import-csv').on('change', (e) => this.handleImportCSV(e));
            
            // Template management
            $('.delete-template').on('click', (e) => this.handleDeleteTemplate(e));
            
            // Settings form
            $('#ism-settings-form').on('submit', (e) => this.handleSaveSettings(e));
        }
        
        handleAddCommune(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'ism_add_commune',
                    nonce: $('#ism_admin_nonce').val(),
                    ...Object.fromEntries(formData)
                },
                success: (response) => {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Erreur: ' + response.data);
                    }
                },
                error: () => {
                    alert('Erreur lors de l\'ajout de la commune');
                }
            });
        }
        
        handleDeleteCommune(e) {
            e.preventDefault();
            
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette commune ?')) {
                return;
            }
            
            const communeId = $(e.target).data('commune-id');
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'ism_delete_commune',
                    nonce: $('#ism_admin_nonce').val(),
                    commune_id: communeId
                },
                success: (response) => {
                    if (response.success) {
                        $(e.target).closest('tr').fadeOut();
                    } else {
                        alert('Erreur: ' + response.data);
                    }
                },
                error: () => {
                    alert('Erreur lors de la suppression');
                }
            });
        }
        
        handleImportCSV(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('csv_file', file);
            formData.append('action', 'ism_import_csv');
            formData.append('nonce', $('#ism_admin_nonce').val());
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    if (response.success) {
                        alert('Import réussi: ' + response.data.imported + ' communes importées');
                        location.reload();
                    } else {
                        alert('Erreur: ' + response.data);
                    }
                },
                error: () => {
                    alert('Erreur lors de l\'import');
                }
            });
        }
        
        handleDeleteTemplate(e) {
            e.preventDefault();
            
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce modèle ?')) {
                return;
            }
            
            const templateId = $(e.target).data('template-id');
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'ism_delete_template',
                    nonce: $('#ism_admin_nonce').val(),
                    template_id: templateId
                },
                success: (response) => {
                    if (response.success) {
                        $(e.target).closest('.template-item').fadeOut();
                    } else {
                        alert('Erreur: ' + response.data);
                    }
                },
                error: () => {
                    alert('Erreur lors de la suppression');
                }
            });
        }
        
        handleSaveSettings(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'ism_save_settings',
                    nonce: $('#ism_admin_nonce').val(),
                    ...Object.fromEntries(formData)
                },
                success: (response) => {
                    if (response.success) {
                        $('.notice').remove();
                        $('.wrap h1').after('<div class="notice notice-success is-dismissible"><p>Paramètres sauvegardés avec succès</p></div>');
                    } else {
                        alert('Erreur: ' + response.data);
                    }
                },
                error: () => {
                    alert('Erreur lors de la sauvegarde');
                }
            });
        }
        
        initCharts() {
            // Initialize Chart.js if available
            if (typeof Chart !== 'undefined' && $('#ism-stats-chart').length) {
                this.renderStatsChart();
            }
        }
        
        renderStatsChart() {
            const ctx = document.getElementById('ism-stats-chart').getContext('2d');
            const chartData = window.ismChartData || [];
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(item => item.date),
                    datasets: [{
                        label: 'Messages envoyés',
                        data: chartData.map(item => item.count),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Messages envoyés (7 derniers jours)'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        new InterpellerSonMaireAdmin();
    });
    
})(jQuery);