<?php
require 'vendor/PHPMailer/PHPMailer.php';
require 'vendor/PHPMailer/SMTP.php';
require 'vendor/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jadhavmahesh3329@gmail.com';
    $mail->Password = 'gfir mzmm tqzl trlr'; // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('jadhavmahesh3329@gmail.com', 'Grocery Test');
    $mail->addAddress('your-email@example.com', 'Mahesh');

    $mail->isHTML(true);
    $mail->Subject = 'Test Delivery OTP Email';
    $mail->Body    = 'This is a test email to verify PHPMailer SMTP settings.';

    $mail->send();
    echo "Test email sent successfully";
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>
