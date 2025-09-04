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
            // Gmail SMTP config using .env values
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER');
            $mail->Password   = getenv('SMTP_PASS');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = getenv('SMTP_PORT');

            // Sender & recipient
            $mail->setFrom(getenv('SMTP_FROM'), 'CI4 Test');
            $mail->addAddress('kahuenz@gmail.com', 'User'); // fixed typo

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'PHPMailer Test from CodeIgniter';
            $mail->Body    = '<h3>This is a test email sent securely 🎉</h3>';
            $mail->AltBody = 'This is the plain text version of the email.';

            $mail->send();
            echo '✅ Message has been sent';
        } catch (Exception $e) {
            echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
