<?php
namespace InterpellerSonMaire\Core;

class Database {
    
    public static function createTables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Table for communes
        $table_communes = $wpdb->prefix . 'ism_communes';
        $sql_communes = "CREATE TABLE $table_communes (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            code_insee varchar(10) DEFAULT NULL,
            mayor_email varchar(255) NOT NULL,
            population int(11) DEFAULT 0,
            region varchar(255) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY code_insee (code_insee),
            KEY name (name)
        ) $charset_collate;";
        
        // Table for message templates
        $table_templates = $wpdb->prefix . 'ism_templates';
        $sql_templates = "CREATE TABLE $table_templates (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            subject varchar(500) NOT NULL,
            content text NOT NULL,
            category varchar(50) DEFAULT 'waste',
            usage_count int(11) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY category (category),
            KEY is_active (is_active)
        ) $charset_collate;";
        
        // Table for logs (encrypted)
        $table_logs = $wpdb->prefix . 'ism_logs';
        $sql_logs = "CREATE TABLE $table_logs (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            encrypted_data text NOT NULL,
            commune_id mediumint(9) NOT NULL,
            template_id mediumint(9) DEFAULT NULL,
            ip_hash varchar(64) NOT NULL,
            user_agent_hash varchar(64) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY commune_id (commune_id),
            KEY template_id (template_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Table for blocked words
        $table_blocked_words = $wpdb->prefix . 'ism_blocked_words';
        $sql_blocked_words = "CREATE TABLE $table_blocked_words (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            word varchar(255) NOT NULL,
            is_regex tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY word (word)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_communes);
        dbDelta($sql_templates);
        dbDelta($sql_logs);
        dbDelta($sql_blocked_words);
        
        // Insert default data
        self::insertDefaultData();
    }
    
    public static function dropTables() {
        global $wpdb;
        
        $tables = [
            $wpdb->prefix . 'ism_communes',
            $wpdb->prefix . 'ism_templates',
            $wpdb->prefix . 'ism_logs',
            $wpdb->prefix . 'ism_blocked_words'
        ];
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
    
    private static function insertDefaultData() {
        global $wpdb;
        
        // Insert sample communes
        $communes = [
            ['Paris 1er Arrondissement', '75101', 'maire@paris1.fr', 16888, 'Île-de-France'],
            ['Lyon', '69123', 'maire@lyon.fr', 522228, 'Auvergne-Rhône-Alpes'],
            ['Marseille', '13055', 'maire@marseille.fr', 870018, 'Provence-Alpes-Côte d\'Azur'],
            ['Toulouse', '31555', 'maire@toulouse.fr', 486828, 'Occitanie'],
            ['Nice', '06088', 'maire@nice.fr', 341032, 'Provence-Alpes-Côte d\'Azur'],
            ['Nantes', '44109', 'maire@nantes.fr', 320732, 'Pays de la Loire']
        ];
        
        $table_communes = $wpdb->prefix . 'ism_communes';
        foreach ($communes as $commune) {
            $wpdb->insert($table_communes, [
                'name' => $commune[0],
                'code_insee' => $commune[1],
                'mayor_email' => $commune[2],
                'population' => $commune[3],
                'region' => $commune[4]
            ]);
        }
        
        // Insert default message templates
        $templates = [
            [
                'title' => 'Suppression du ramassage porte-à-porte',
                'subject' => 'Préoccupation concernant la suppression du ramassage des déchets',
                'content' => "Madame la Maire, Monsieur le Maire,\n\nJe vous écris en tant que citoyen(ne) de {commune} pour exprimer ma vive préoccupation concernant la suppression annoncée du ramassage porte-à-porte des déchets.\n\nCette décision aura un impact significatif sur la qualité de vie des habitants, particulièrement pour les personnes âgées et à mobilité réduite qui ne pourront pas facilement se déplacer vers les points de collecte.\n\nJe vous demande de reconsidérer cette décision et d'étudier des alternatives qui préservent l'accessibilité du service public de collecte des déchets.\n\nCordialement,\n{prenom} {nom}",
                'category' => 'waste'
            ],
            [
                'title' => 'Impact sur les personnes âgées',
                'subject' => 'Impact de la suppression du ramassage sur les personnes âgées',
                'content' => "Madame la Maire, Monsieur le Maire,\n\nEn tant que résident(e) de {commune}, je souhaite attirer votre attention sur l'impact particulièrement difficile que la suppression du ramassage porte-à-porte aura sur nos concitoyens âgés.\n\nBeaucoup de personnes âgées de notre commune ne peuvent physiquement pas porter leurs déchets jusqu'aux points de collecte, ce qui risque de créer des situations d'insalubrité domiciliaire.\n\nJe vous propose d'envisager un maintien du service pour les personnes de plus de 70 ans ou en situation de handicap.\n\nRespectuellement,\n{prenom} {nom}",
                'category' => 'waste'
            ],
            [
                'title' => 'Alternative écologique',
                'subject' => 'Proposition d\'alternatives écologiques',
                'content' => "Madame la Maire, Monsieur le Maire,\n\nJe comprends les enjeux budgétaires qui motivent la suppression du ramassage porte-à-porte, mais je souhaiterais proposer des alternatives qui concilient économies et service public.\n\nPourquoi ne pas envisager :\n- Un ramassage hebdomadaire au lieu de bi-hebdomadaire\n- Des bacs collectifs par îlots d'habitation\n- Un système de compostage communal encouragé\n\nCes solutions pourraient réduire les coûts tout en maintenant un service de qualité.\n\nCordialement,\n{prenom} {nom}",
                'category' => 'environment'
            ]
        ];
        
        $table_templates = $wpdb->prefix . 'ism_templates';
        foreach ($templates as $template) {
            $wpdb->insert($table_templates, $template);
        }
        
        // Insert default blocked words
        $blocked_words = ['spam', 'insulte', 'violence', 'harcèlement', 'connard', 'salaud', 'merde'];
        $table_blocked_words = $wpdb->prefix . 'ism_blocked_words';
        foreach ($blocked_words as $word) {
            $wpdb->insert($table_blocked_words, ['word' => $word]);
        }
        
        // Set default options
        $default_settings = [
            'email_subject' => 'Message de {prenom} {nom} - {commune}',
            'confirmation_message' => 'Votre message a bien été transmis au maire de {commune}. Vous devriez recevoir une réponse dans les prochains jours.',
            'recaptcha_enabled' => true,
            'auto_purge_enabled' => true,
            'purge_delay_months' => 12
        ];
        
        update_option('ism_settings', $default_settings);
    }
}