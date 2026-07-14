<?php
/**
 * Contact handler for RSO For All Foundation.
 *
 * Set $to below to the address John wants messages delivered to.
 * Runs on Hostinger's PHP with mail() — no third-party form service,
 * so nobody but John ever sees what people write in.
 */

$to      = 'CHANGE-ME@rsoforallfoundation.com'; // <-- John's inbox
$subject = 'New message from rsoforallfoundation.com';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.html');
    exit;
}

// Honeypot: real people never fill this in, bots almost always do.
if (!empty($_POST['website'])) {
    header('Location: thank-you.html');
    exit;
}

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];
if ($name === '')                                     { $errors[] = 'name'; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL))       { $errors[] = 'email'; }
if ($message === '')                                  { $errors[] = 'message'; }

if ($errors) {
    header('Location: contact.html?error=1');
    exit;
}

// Strip anything that could be used to inject extra mail headers.
$safeName  = preg_replace('/[\r\n]+/', ' ', $name);
$safeEmail = preg_replace('/[\r\n]+/', '', $email);

$body = "Name: {$safeName}\n"
      . "Email: {$safeEmail}\n"
      . "Sent: " . date('j M Y, H:i') . "\n"
      . "-------------------------------------------\n\n"
      . $message . "\n";

$headers  = "From: RSO For All Foundation <noreply@rsoforallfoundation.com>\r\n";
$headers .= "Reply-To: {$safeName} <{$safeEmail}>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

@mail($to, $subject, $body, $headers);

header('Location: thank-you.html');
exit;
