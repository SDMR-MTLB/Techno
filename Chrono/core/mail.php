<?php
// core/mail.php
// Email sending helper using PHPMailer

// Load PHPMailer (you need to install via composer or manually include)
// For simplicity, we'll assume it's installed in vendor/autoload.php
// If not, adjust path accordingly.
require_once __DIR__ . '/../vendor/autoload.php'; // Adjust if needed

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an email.
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body HTML body
 * @param string $altBody Plain text alternative (optional)
 * @return bool True if sent, false otherwise
 */
function sendEmail($to, $subject, $body, $altBody = '') {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;      // Set in config/app.php
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}