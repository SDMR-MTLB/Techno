<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/mail.php';

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// Basic validation
if (!$name || !$email || !$subject || !$message) {
    header('Location: ' . BASE_URL . '/contact.php?error=missing_fields');
    exit;
}

// Send email to admin
$body = "<h2>Contact Form Submission</h2>
         <p><strong>Name:</strong> $name</p>
         <p><strong>Email:</strong> $email</p>
         <p><strong>Subject:</strong> $subject</p>
         <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";

$sent = sendEmail('admin@pisowifivendo.com', "Contact: $subject", $body);

if ($sent) {
    header('Location: ' . BASE_URL . '/contact.php?success=1');
} else {
    header('Location: ' . BASE_URL . '/contact.php?error=send_failed');
}
exit;