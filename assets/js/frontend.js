// Plugin Interpeller son Maire - Frontend JavaScript

(function($) {
    'use strict';
    
    class InterpellerSonMaire {
        constructor() {
            this.form = $('#ism-contact-form');
            this.communeSearch = $('#ism-commune');
            this.communeResults = $('#ism-commune-results');
            this.communeId = $('#ism-commune-id');
            this.messageType = $('input[name="message_type"]');
            this.templateSelect = $('#ism-template');
            this.messageTextarea = $('#ism-message');
            this.messageLabel = $('#ism-message-label');
            this.submitBtn = $('.ism-submit-btn');
            
            this.communes = [];
            this.templates = [];
            this.searchTimeout = null;
            
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.loadTemplates();
            this.initRecaptcha();
        }
        
        bindEvents() {
            // Commune search
            this.communeSearch.on('input', (e) => this.handleCommuneSearch(e));
            this.communeSearch.on('focus', () => this.showCommuneResults());
            $(document).on('click', (e) => {
                if (!$(e.target).closest('.ism-commune-search').length) {
                    this.hideCommuneResults();
                }
            });
            
            // Message type change
            this.messageType.on('change', (e) => this.handleMessageTypeChange(e));
            
            // Template selection
            this.templateSelect.on('change', (e) => this.handleTemplateChange(e));
            
            // Form submission
            this.form.on('submit', (e) => this.handleSubmit(e));
            
            // Reset button
            $(document).on('click', '.ism-reset-btn', () => this.resetForm());
            
            // Form validation
            this.form.find('input, select, textarea').on('input change', () => this.validateForm());
        }
        
        handleCommuneSearch(e) {
            const query = e.target.value.trim();
            
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                if (query.length >= 2) {
                    this.searchCommunes(query);
                } else {
                    this.hideCommuneResults();
                }
            }, 300);
        }
        
        searchCommunes(query) {
            $.ajax({
                url: ismAjax.restUrl + 'communes',
                method: 'GET',
                data: { search: query },
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', ismAjax.restNonce);
                },
                success: (data) => {
                    this.displayCommuneResults(data);
                },
                error: (xhr) => {
                    console.error('Erreur lors de la recherche de communes:', xhr);
                }
            });
        }
        
        displayCommuneResults(communes) {
            this.communeResults.empty();
            
            if (communes.length === 0) {
                this.communeResults.html('<div class="ism-commune-result">Aucune commune trouvée</div>');
            } else {
                communes.forEach(commune => {
                    const result = $(`
                        <div class="ism-commune-result" data-commune-id="${commune.id}">
                            <div class="ism-commune-name">${commune.name}</div>
                            <div class="ism-commune-details">${commune.region} • ${parseInt(commune.population).toLocaleString()} habitants</div>
                        </div>
                    `);
                    
                    result.on('click', () => this.selectCommune(commune));
                    this.communeResults.append(result);
                });
            }
            
            this.showCommuneResults();
        }
        
        selectCommune(commune) {
            this.communeSearch.val(commune.name);
            this.communeId.val(commune.id);
            this.hideCommuneResults();
            this.validateForm();
            this.updateMessagePlaceholders();
        }
        
        showCommuneResults() {
            this.communeResults.addClass('show');
        }
        
        hideCommuneResults() {
            this.communeResults.removeClass('show');
        }
        
        loadTemplates() {
            $.ajax({
                url: ismAjax.restUrl + 'templates',
                method: 'GET',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', ismAjax.restNonce);
                },
                success: (data) => {
                    this.templates = data;
                    this.populateTemplateSelect();
                },
                error: (xhr) => {
                    console.error('Erreur lors du chargement des modèles:', xhr);
                }
            });
        }
        
        populateTemplateSelect() {
            this.templateSelect.find('option:not(:first)').remove();
            
            this.templates.forEach(template => {
                const option = $(`<option value="${template.id}">${template.title} (utilisé ${template.usage_count} fois)</option>`);
                this.templateSelect.append(option);
            });
        }
        
        handleMessageTypeChange(e) {
            const messageType = e.target.value;
            
            if (messageType === 'template') {
                $('#ism-template-selection').show();
                this.messageLabel.text('Aperçu du message');
                this.messageTextarea.prop('readonly', true);
                this.handleTemplateChange();
            } else {
                $('#ism-template-selection').hide();
                this.messageLabel.text('Votre message *');
                this.messageTextarea.prop('readonly', false).val('');
            }
            
            this.validateForm();
        }
        
        handleTemplateChange() {
            const templateId = this.templateSelect.val();
            
            if (templateId) {
                const template = this.templates.find(t => t.id == templateId);
                if (template) {
                    let content = template.content;
                    content = this.replacePlaceholders(content);
                    this.messageTextarea.val(content);
                }
            } else {
                this.messageTextarea.val('');
            }
            
            this.validateForm();
        }
        
        replacePlaceholders(content) {
            const firstname = $('#ism-firstname').val() || '{prenom}';
            const lastname = $('#ism-lastname').val() || '{nom}';
            const email = $('#ism-email').val() || '{email}';
            const commune = this.communeSearch.val() || '{commune}';
            
            return content
                .replace(/\{prenom\}/g, firstname)
                .replace(/\{nom\}/g, lastname)
                .replace(/\{email\}/g, email)
                .replace(/\{commune\}/g, commune);
        }
        
        updateMessagePlaceholders() {
            if ($('input[name="message_type"]:checked').val() === 'template' && this.templateSelect.val()) {
                this.handleTemplateChange();
            }
        }
        
        validateForm() {
            const isValid = this.isFormValid();
            this.submitBtn.prop('disabled', !isValid);
        }
        
        isFormValid() {
            const communeId = this.communeId.val();
            const firstname = $('#ism-firstname').val().trim();
            const lastname = $('#ism-lastname').val().trim();
            const email = $('#ism-email').val().trim();
            const message = this.messageTextarea.val().trim();
            const messageType = $('input[name="message_type"]:checked').val();
            
            if (!communeId || !firstname || !lastname || !email || !message) {
                return false;
            }
            
            if (messageType === 'template' && !this.templateSelect.val()) {
                return false;
            }
            
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                return false;
            }
            
            return true;
        }
        
        handleSubmit(e) {
            e.preventDefault();
            
            if (!this.isFormValid()) {
                return;
            }
            
            this.showLoading();
            
            this.getRecaptchaToken().then(token => {
                const formData = {
                    commune_id: this.communeId.val(),
                    firstname: $('#ism-firstname').val().trim(),
                    lastname: $('#ism-lastname').val().trim(),
                    email: $('#ism-email').val().trim(),
                    message: this.messageTextarea.val().trim(),
                    template_id: this.templateSelect.val() || null,
                    recaptcha_token: token
                };

                $.ajax({
                    url: ismAjax.restUrl + 'send-message',
                    method: 'POST',
                    data: formData,
                    beforeSend: (xhr) => {
                        xhr.setRequestHeader('X-WP-Nonce', ismAjax.restNonce);
                    },
                    success: (data) => {
                        this.showSuccess();
                    },
                    error: (xhr) => {
                        this.hideLoading();
                        this.showError(xhr.responseJSON?.message || 'Une erreur est survenue');
                    }
                });
            });
        }
        
        showLoading() {
            this.form.addClass('loading');
            this.submitBtn.find('.ism-btn-text').hide();
            this.submitBtn.find('.ism-btn-loading').show();
        }
        
        hideLoading() {
            this.form.removeClass('loading');
            this.submitBtn.find('.ism-btn-text').show();
            this.submitBtn.find('.ism-btn-loading').hide();
        }
        
        showSuccess() {
            this.form.hide();
            $('#ism-success-message').show();
        }
        
        showError(message) {
            alert('Erreur: ' + message);
        }
        
        resetForm() {
            this.form[0].reset();
            this.communeId.val('');
            this.messageTextarea.val('');
            this.hideCommuneResults();
            this.validateForm();
            
            $('#ism-success-message').hide();
            this.form.show();
        }
        
        initRecaptcha() {
            if (typeof grecaptcha === 'undefined' || !ismAjax.recaptchaEnabled) {
                return;
            }

            grecaptcha.ready(() => {
                grecaptcha.execute(ismAjax.recaptchaSiteKey, { action: 'ism_form' })
                    .then(token => { this.recaptchaToken = token; });
            });
        }

        getRecaptchaToken() {
            if (typeof grecaptcha === 'undefined' || !ismAjax.recaptchaEnabled) {
                return Promise.resolve('');
            }

            return new Promise(resolve => {
                grecaptcha.ready(() => {
                    grecaptcha.execute(ismAjax.recaptchaSiteKey, { action: 'ism_form' })
                        .then(resolve)
                        .catch(() => resolve(''));
                });
            });
        }
    }
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        new InterpellerSonMaire();
    });
    
    // Update placeholders when personal info changes
    $(document).on('input', '#ism-firstname, #ism-lastname, #ism-email', function() {
        if ($('input[name="message_type"]:checked').val() === 'template') {
            const templateId = $('#ism-template').val();
            if (templateId) {
                // Trigger template change to update placeholders
                $('#ism-template').trigger('change');
            }
        }
    });
    
})(jQuery);