=== Interpeller son Maire ===
Contributors: touchepasamespoubelles
Tags: civic engagement, mayor contact, municipal communication, citizen participation
Requires at least: 6.5
Tested up to: 6.4
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin permettant aux citoyens de contacter facilement leur maire concernant la suppression du ramassage porte-à-porte des déchets.

== Description ==

**Interpeller son Maire** est un plugin WordPress professionnel qui facilite la communication entre les citoyens et leurs élus locaux. Spécialement conçu pour les préoccupations concernant la suppression du ramassage porte-à-porte des déchets, ce plugin offre une solution complète et sécurisée.

### Fonctionnalités principales

* **Formulaire intelligent** : Sélection automatique de commune avec recherche en temps réel
* **Messages pré-rédigés** : Plus de 70 modèles de messages professionnels
* **Sécurité renforcée** : Protection anti-spam avec Google reCAPTCHA v3
* **Conformité RGPD** : Chiffrement des données et purge automatique
* **Statistiques détaillées** : Suivi des envois et analytics complets
* **Interface d'administration** : Gestion complète des communes et modèles

### Intégrations

* **FluentCRM** : Gestion des contacts maires
* **FluentSMTP** : Envoi d'emails sécurisé
* **Gutenberg** : Bloc dédié pour l'éditeur
* **GeneratePress** : Compatibilité thème optimisée

### Utilisation

Utilisez le shortcode `[interpeller_maire]` pour afficher le formulaire sur vos pages.

Options disponibles :
* `default_template=""` - ID du modèle par défaut
* `button_color="#2563eb"` - Couleur du bouton d'envoi
* `show_templates="true"` - Afficher la sélection de modèles

Exemple : `[interpeller_maire button_color="#059669" show_templates="false"]`

== Installation ==

1. Téléchargez et décompressez le plugin
2. Uploadez le dossier `interpeller-son-maire` dans `/wp-content/plugins/`
3. Activez le plugin via l'interface d'administration WordPress
4. Configurez les paramètres dans "Interpeller son Maire" > "Réglages"
5. Ajoutez vos communes dans "Communes & Maires"
6. Utilisez le shortcode `[interpeller_maire]` sur vos pages

== Configuration ==

### Prérequis

* WordPress 6.5+
* PHP 8.1+
* FluentCRM (recommandé)
* FluentSMTP (recommandé)

### Configuration reCAPTCHA

Ajoutez vos clés Google reCAPTCHA v3 dans `wp-config.php` :

```php
define('ISM_RECAPTCHA_SITE_KEY', 'votre_cle_site');
define('ISM_RECAPTCHA_SECRET', 'votre_cle_secrete');
```

### Chiffrement des données

Pour une sécurité renforcée, définissez une clé de chiffrement :

```php
define('ISM_ENCRYPTION_KEY', 'votre_cle_de_chiffrement_32_caracteres');
```

== Frequently Asked Questions ==

= Comment ajouter de nouvelles communes ? =

Rendez-vous dans "Interpeller son Maire" > "Communes & Maires" et cliquez sur "Ajouter commune". Vous pouvez également importer un fichier CSV.

= Les données sont-elles sécurisées ? =

Oui, toutes les données sensibles sont chiffrées avec AES-256 et automatiquement purgées après 12 mois (configurable).

= Comment personnaliser les modèles de messages ? =

Accédez à "Modèles de messages" dans l'administration. Vous pouvez créer, modifier et supprimer des modèles. Utilisez les variables {prenom}, {nom}, {commune}, {email} pour la personnalisation.

= Le plugin est-il compatible avec mon thème ? =

Le plugin est conçu pour être compatible avec tous les thèmes WordPress modernes, avec une optimisation spéciale pour GeneratePress.

== Screenshots ==

1. Formulaire de contact citoyen avec recherche de commune
2. Interface d'administration - Tableau de bord
3. Gestion des communes et maires
4. Modèles de messages pré-rédigés
5. Statistiques et analytics détaillées
6. Bloc Gutenberg intégré

== Changelog ==

= 1.0.0 =
* Version initiale
* Formulaire de contact avec recherche de commune
* Système de modèles de messages
* Interface d'administration complète
* Intégration FluentCRM/FluentSMTP
* Sécurité reCAPTCHA v3
* Conformité RGPD
* Statistiques et exports
* Bloc Gutenberg
* Responsive design

== Upgrade Notice ==

= 1.0.0 =
Version initiale du plugin Interpeller son Maire.

== Support ==

Pour obtenir de l'aide :

* Documentation : [https://touchepasamespoubelles.fr/docs](https://touchepasamespoubelles.fr/docs)
* Support : [https://touchepasamespoubelles.fr/support](https://touchepasamespoubelles.fr/support)
* GitHub : [https://github.com/touchepasamespoubelles/interpeller-son-maire](https://github.com/touchepasamespoubelles/interpeller-son-maire)

== Développement ==

Ce plugin suit les standards WordPress et utilise :

* PSR-12 pour le code PHP
* WordPress Coding Standards
* Tests unitaires PHPUnit
* CI/CD avec GitHub Actions
* Internationalisation gettext

Contributions bienvenues sur GitHub !