<?php
namespace InterpellerSonMaire\Gutenberg;

class Block {
    
    public function __construct() {
        add_action('init', [$this, 'registerBlock']);
    }
    
    public function registerBlock() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        wp_register_script(
            'ism-block-editor',
            ISM_PLUGIN_URL . 'assets/js/block-editor.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
            ISM_PLUGIN_VERSION
        );
        
        register_block_type('interpeller-son-maire/contact-form', [
            'editor_script' => 'ism-block-editor',
            'render_callback' => [$this, 'renderBlock'],
            'attributes' => [
                'defaultTemplate' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'buttonColor' => [
                    'type' => 'string',
                    'default' => '#2563eb'
                ],
                'showTemplates' => [
                    'type' => 'boolean',
                    'default' => true
                ]
            ]
        ]);
    }
    
    public function renderBlock($attributes) {
        $shortcode = new \InterpellerSonMaire\Frontend\Shortcode();
        return $shortcode->renderShortcode([
            'default_template' => $attributes['defaultTemplate'] ?? '',
            'button_color' => $attributes['buttonColor'] ?? '#2563eb',
            'show_templates' => $attributes['showTemplates'] ? 'true' : 'false'
        ]);
    }
}