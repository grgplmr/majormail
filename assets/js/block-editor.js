// Plugin Interpeller son Maire - Gutenberg Block

(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, ColorPicker, ToggleControl } = wp.components;
    const { Fragment } = wp.element;
    
    registerBlockType('interpeller-son-maire/contact-form', {
        title: 'Formulaire Interpeller son Maire',
        description: 'Formulaire de contact pour interpeller les maires',
        icon: 'email-alt',
        category: 'widgets',
        attributes: {
            defaultTemplate: {
                type: 'string',
                default: ''
            },
            buttonColor: {
                type: 'string',
                default: '#2563eb'
            },
            showTemplates: {
                type: 'boolean',
                default: true
            }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { defaultTemplate, buttonColor, showTemplates } = attributes;
            
            return Fragment({}, [
                wp.element.createElement(InspectorControls, { key: 'inspector' }, [
                    wp.element.createElement(PanelBody, {
                        key: 'settings',
                        title: 'Paramètres du formulaire',
                        initialOpen: true
                    }, [
                        wp.element.createElement(TextControl, {
                            key: 'template',
                            label: 'Modèle par défaut (ID)',
                            value: defaultTemplate,
                            onChange: (value) => setAttributes({ defaultTemplate: value })
                        }),
                        wp.element.createElement('p', { key: 'color-label' }, 'Couleur du bouton'),
                        wp.element.createElement(ColorPicker, {
                            key: 'color',
                            color: buttonColor,
                            onChange: (value) => setAttributes({ buttonColor: value })
                        }),
                        wp.element.createElement(ToggleControl, {
                            key: 'templates',
                            label: 'Afficher la sélection de modèles',
                            checked: showTemplates,
                            onChange: (value) => setAttributes({ showTemplates: value })
                        })
                    ])
                ]),
                wp.element.createElement('div', {
                    key: 'preview',
                    className: 'ism-block-preview',
                    style: {
                        padding: '20px',
                        border: '2px dashed #ccc',
                        textAlign: 'center',
                        backgroundColor: '#f9f9f9'
                    }
                }, [
                    wp.element.createElement('h3', { key: 'title' }, 'Formulaire Interpeller son Maire'),
                    wp.element.createElement('p', { key: 'desc' }, 'Le formulaire de contact sera affiché ici sur le frontend.'),
                    wp.element.createElement('div', {
                        key: 'button-preview',
                        style: {
                            display: 'inline-block',
                            padding: '10px 20px',
                            backgroundColor: buttonColor,
                            color: 'white',
                            borderRadius: '5px',
                            marginTop: '10px'
                        }
                    }, 'Aperçu du bouton')
                ])
            ]);
        },
        
        save: function() {
            // Server-side rendering
            return null;
        }
    });
    
})(window.wp);