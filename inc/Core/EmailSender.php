<?php
namespace InterpellerSonMaire\Core;

class EmailSender {
    
    public function sendToMayor($data) {
        $settings = get_option('ism_settings', []);
        
        // Prepare subject
        $subject = $settings['email_subject'] ?? 'Message de {prenom} {nom} - {commune}';
        $subject = str_replace(
            ['{prenom}', '{nom}', '{commune}', '{email}'],
            [$data['sender']['firstname'], $data['sender']['lastname'], $data['commune']->name, $data['sender']['email']],
            $subject
        );
        
        // Prepare headers
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $data['sender']['firstname'] . ' ' . $data['sender']['lastname'] . ' <' . $data['sender']['email'] . '>',
            'Reply-To: ' . $data['sender']['email']
        ];
        
        // Prepare message
        $message = $this->formatEmailMessage($data);
        
        // Send email
        return wp_mail($data['commune']->mayor_email, $subject, $message, $headers);
    }
    
    public function sendConfirmation($data) {
        $settings = get_option('ism_settings', []);
        
        $subject = __('Confirmation d\'envoi de votre message', 'interpeller-son-maire');
        
        $message = $settings['confirmation_message'] ?? 'Votre message a bien été transmis au maire de {commune}. Vous devriez recevoir une réponse dans les prochains jours.';
        $message = str_replace('{commune}', $data['commune_name'], $message);
        
        $html_message = $this->formatConfirmationEmail($data, $message);
        
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        ];
        
        return wp_mail($data['recipient_email'], $subject, $html_message, $headers);
    }
    
    private function formatEmailMessage($data) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo esc_html($data['commune']->name); ?></title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h2 style="color: #2563eb; margin: 0 0 10px 0;">
                        Message de <?php echo esc_html($data['sender']['firstname'] . ' ' . $data['sender']['lastname']); ?>
                    </h2>
                    <p style="margin: 0; color: #666;">
                        <strong>Commune :</strong> <?php echo esc_html($data['commune']->name); ?><br>
                        <strong>Email :</strong> <?php echo esc_html($data['sender']['email']); ?><br>
                        <strong>Date :</strong> <?php echo date('d/m/Y à H:i'); ?>
                    </p>
                </div>
                
                <div style="background: white; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    <?php echo nl2br(esc_html($data['message'])); ?>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: #f3f4f6; border-radius: 8px; font-size: 12px; color: #666;">
                    <p style="margin: 0;">
                        Ce message a été envoyé via le plugin "Interpeller son Maire" du site 
                        <a href="<?php echo home_url(); ?>" style="color: #2563eb;"><?php echo get_bloginfo('name'); ?></a>
                    </p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    private function formatConfirmationEmail($data, $confirmation_message) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Confirmation d'envoi</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <div style="background: #10b981; color: white; width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px;">
                        ✓
                    </div>
                    <h1 style="color: #1f2937; margin: 0;">Message envoyé avec succès !</h1>
                </div>
                
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <p style="margin: 0 0 15px 0;"><?php echo esc_html($confirmation_message); ?></p>
                    <p style="margin: 0; color: #666; font-size: 14px;">
                        <strong>Destinataire :</strong> Maire de <?php echo esc_html($data['commune_name']); ?>
                    </p>
                </div>
                
                <div style="background: white; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 20px;">
                    <h3 style="margin: 0 0 15px 0; color: #374151;">Rappel de votre message :</h3>
                    <div style="color: #6b7280; font-size: 14px;">
                        <?php echo nl2br(esc_html(substr($data['message'], 0, 300))); ?>
                        <?php if (strlen($data['message']) > 300): ?>
                            <em>... (message tronqué)</em>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f3f4f6; border-radius: 8px;">
                    <p style="margin: 0 0 15px 0; color: #666;">
                        Vous souhaitez contacter d'autres maires ?
                    </p>
                    <a href="<?php echo home_url(); ?>" 
                       style="display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                        Retour au site
                    </a>
                </div>
                
                <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #9ca3af;">
                    <p style="margin: 0;">
                        Plugin "Interpeller son Maire" - <?php echo get_bloginfo('name'); ?>
                    </p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}