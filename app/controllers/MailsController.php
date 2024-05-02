<?php
require_once '../public/assets/phpmailer/src/PHPMailer.php';
require_once '../public/assets/phpmailer/src/SMTP.php';
require_once '../public/assets/phpmailer/src/SMTP.php';
require_once '../public/assets/phpmailer/src/Exception.php';


use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailsController
{
    public function sendMail($address,$subject,$message):bool
    {
    // Créer une nouvelle instance de PHPMailer
    $mail = new PHPMailer(true);

        try 
        {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.ionos.fr';
            $mail->SMTPAuth = true;
            $mail->Username = 'auto@zewiki.fr';
            $mail->Password = 'G4xuAlPNhgSbTa8GfNZ0@2';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Destinataire et expéditeur
            $mail->setFrom('auto@zewiki.fr', ' Zewiki mail agent');
            $mail->addAddress($address, '');

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->CharSet = 'UTF8';
            $mail->Subject = $subject;
            $mail->Body    =  $message;
     
            // Envoyer l'email
            $mail->send();
            return true;
        } catch (Exception $e)
        {
            return false;
        }
        
    }
 

}