<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Charger l'autoloader de Composer
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Service d'envoi d'emails avec PHPMailer
 */
$mail_services = [
    /**
     * Envoie un email en utilisant PHPMailer
     * 
     * @param string $to Adresse email du destinataire
     * @param string $subject Sujet de l'email
     * @param string $message Corps de l'email (peut contenir du HTML)
     * @param array $attachments Pièces jointes (chemin des fichiers)
     * @return bool Succès ou échec de l'envoi
     */
    'send_mail' => function($to, $subject, $message, $attachments = []) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration du serveur
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Remplacer par votre serveur SMTP
            $mail->SMTPAuth   = true;
            $mail->Username   = 'mapathendiaye542@gmail.com'; // Remplacer par votre email
            $mail->Password   = 'zcqt ffmg wrtp sxhb';         // Remplacer par votre mot de passe
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            // Destinataires
            $mail->setFrom('mapathendiaye542@gmail.com', 'Sonatel Academy');
            $mail->addAddress($to);
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->CharSet = 'UTF-8';
            
            // Ajouter des pièces jointes si nécessaire
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $mail->addAttachment($attachment);
                }
            }
            
            // Envoyer l'email
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Journaliser l'erreur
            error_log("Erreur d'envoi d'email: " . $mail->ErrorInfo);
            return false;
        }
    },
    
    /**
     * Envoie un email de bienvenue à un nouvel apprenant
     * 
     * @param array $apprenant Données de l'apprenant
     * @param array $promotion Données de la promotion
     * @param array $referentiel Données du référentiel
     * @param string $password Mot de passe par défaut
     * @return bool Succès ou échec de l'envoi
     */
    'send_welcome_email' => function($apprenant, $promotion, $referentiel, $password = 'passer123') use (&$mail_services) {
        $to = $apprenant['email'];
        $subject = "Bienvenue à Sonatel Academy - " . $promotion['name'];
        
        // Corps du message en HTML
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #19A88C; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
                .info-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                .info-table td { padding: 8px; border-bottom: 1px solid #ddd; }
                .info-table td:first-child { font-weight: bold; width: 40%; }
                .button { display: inline-block; background-color: #19A88C; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
                .credentials { background-color: #fff; padding: 15px; border-left: 4px solid #fd7e14; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Bienvenue à Sonatel Academy</h1>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>" . htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) . "</strong>,</p>
                    
                    <p>Nous sommes ravis de vous accueillir à Sonatel Academy pour la promotion <strong>" . htmlspecialchars($promotion['name']) . "</strong>.</p>
                    
                    <p>Voici un récapitulatif de vos informations :</p>
                    
                    <table class='info-table'>
                        <tr>
                            <td>Matricule</td>
                            <td>" . htmlspecialchars($apprenant['matricule']) . "</td>
                        </tr>
                        <tr>
                            <td>Référentiel</td>
                            <td>" . htmlspecialchars($referentiel['name']) . "</td>
                        </tr>
                        <tr>
                            <td>Date d'inscription</td>
                            <td>" . htmlspecialchars($apprenant['date_inscription']) . "</td>
                        </tr>
                    </table>
                    
                    <div class='credentials'>
                        <h3>Vos identifiants de connexion</h3>
                        <p><strong>Email :</strong> " . htmlspecialchars($apprenant['email']) . "</p>
                        <p><strong>Mot de passe par défaut :</strong> " . htmlspecialchars($password) . "</p>
                        <p><strong>Important :</strong> Lors de votre première connexion, vous devrez changer ce mot de passe.</p>
                    </div>
                    
                    <p>Veuillez conserver précieusement votre matricule, il vous sera demandé pour toutes vos démarches administratives.</p>
                    
                    <p>Pour toute question, n'hésitez pas à contacter l'administration de Sonatel Academy.</p>
                    
                    <p>Cordialement,<br>L'équipe Sonatel Academy</p>
                </div>
                <div class='footer'>
                    <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
                    <p>© " . date('Y') . " Sonatel Academy. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Envoyer l'email
        return $mail_services['send_mail']($to, $subject, $message);
    },
    
    /**
     * Envoie un email avec les identifiants de connexion à un apprenant
     * 
     * @param array $apprenant Données de l'apprenant
     * @param array $referentiel Données du référentiel
     * @param string $password Mot de passe par défaut
     * @return bool Succès ou échec de l'envoi
     */
    'send_credentials_email' => function($apprenant, $referentiel, $password = 'passer123') use (&$mail_services) {
        $to = $apprenant['email'];
        $subject = "Vos identifiants de connexion - Orange Digital Center";
        
        // Corps du message en HTML
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #fd7e14; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
                .credentials { background-color: #fff; padding: 15px; border-left: 4px solid #19A88C; margin: 15px 0; }
                .info-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                .info-table td { padding: 8px; border-bottom: 1px solid #ddd; }
                .info-table td:first-child { font-weight: bold; width: 40%; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Orange Digital Center</h1>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>" . htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) . "</strong>,</p>
                    
                    <p>Votre compte a été créé avec succès. Voici un récapitulatif de vos informations :</p>
                    
                    <table class='info-table'>
                        <tr>
                            <td>Matricule</td>
                            <td>" . htmlspecialchars($apprenant['matricule']) . "</td>
                        </tr>
                        <tr>
                            <td>Référentiel</td>
                            <td>" . htmlspecialchars($referentiel['name']) . "</td>
                        </tr>
                    </table>
                    
                    <div class='credentials'>
                        <h3>Vos identifiants de connexion</h3>
                        <p><strong>Email :</strong> " . htmlspecialchars($apprenant['email']) . "</p>
                        <p><strong>Mot de passe par défaut :</strong> " . htmlspecialchars($password) . "</p>
                        <p><strong>Important :</strong> Lors de votre première connexion, vous devrez changer ce mot de passe.</p>
                    </div>
                    
                    <p>Pour vous connecter, veuillez cliquer sur le lien suivant : <a href='http://localhost:8009/?page=login'>Se connecter</a></p>
                    
                    <p>Cordialement,<br>L'équipe Orange Digital Center</p>
                </div>
                <div class='footer'>
                    <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
                    <p>© " . date('Y') . " Orange Digital Center. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Envoyer l'email
        return $mail_services['send_mail']($to, $subject, $message);
    }
];