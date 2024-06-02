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

    // get information from configuration file
    $lignes = explode("\n", file_get_contents('../app/configuration.txt'));
    $config = [];

    // loop to get line information
    foreach ($lignes as $ligne)
    {
        // extract params and value
        $conf = explode('=', $ligne);           
        // Store key value in the array
        $config[$conf[0]] = trim($conf[1]);
    }
    // var_dump($config);
    // die();
        try 
        {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = $config['mailHost'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['mailUsername'];
            $mail->Password = $config['mailPassword'];
            $mail->SMTPSecure = $config['SMTPSecure'];
            $mail->Port = $config['mailPort'];

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