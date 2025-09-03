<?php

namespace App\Controllers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailTest extends BaseController
{
    public function index()
    {
        require '../vendor/autoload.php'; // load PHPMailer

        $mail = new PHPMailer(true);

        try {
            // Gmail SMTP config
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'caspequiel27@gmail.com'; // your Gmail
            $mail->Password   = 'lgle qffa oehs tuoe';   // Gmail App Password (NOT your Gmail login password)
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Sender & recipient
            $mail->setFrom('caspequiel27@gmail.com', 'CI4 Test');
            $mail->addAddress('kahuenzgmail.com', 'User'); 

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'PHPMailer Test from CodeIgniter';
            $mail->Body    = '<h3>This is a test email sent using Gmail SMTP + PHPMailer + CodeIgniter 🎉</h3>';
            $mail->AltBody = 'This is the plain text version of the email.';

            $mail->send();
            echo '✅ Message has been sent';
        } catch (Exception $e) {
            echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
