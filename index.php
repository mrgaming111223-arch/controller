<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // install via composer or download PHPMailer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = trim($_POST['to'] ?? '');
    $from_name = trim($_POST['from'] ?? 'Anonymous');
    $subject = trim($_POST['subject'] ?? 'No Subject');
    $message = trim($_POST['message'] ?? '');

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $status = "Invalid email!";
    } else {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp-relay.brevo.com'; // Brevo SMTP
            $mail->SMTPAuth   = true;
            $mail->Username   = '970bcc001@smtp-brevo.com'; // from Brevo dashboard
            $mail->Password   = 'xsmtpsib-67fa1d4d16094fed2908a6ee77cd9c55be19bb6afad382c80e6f8b8f4028bdb3-EeB9jfzlcmjQXrmR'; // SMTP key
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Sender & Recipient
            $mail->setFrom('your-verified-email@yourdomain.com', $from_name);
            $mail->addAddress($to);

            // Content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            $status = "Email sent successfully (check spam if not in inbox)!";
        } catch (Exception $e) {
            $status = "Failed: " . $mail->ErrorInfo;
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anonymous Email Sender</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; background: #f8f9fa; }
        h1 { color: #333; }
        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        label { display: block; margin: 10px 0 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007bff; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .status { margin-top: 20px; padding: 10px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error   { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <h1>Anonymous Email Sender</h1>

    <?php if (isset($status)): ?>
        <div class="status <?= strpos($status, 'success') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($status) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>To (recipient email):</label>
        <input type="email" name="to" required placeholder="friend@example.com">

        <label>From name (will be shown as sender):</label>
        <input type="text" name="from" required value="Anonymous Sender">

        <label>Subject:</label>
        <input type="text" name="subject" required value="Test Message">

        <label>Message:</label>
        <textarea name="message" rows="8" required placeholder="Write your message here..."></textarea>

        <button type="submit">Send Anonymous Email</button>
    </form>

    <p style="margin-top:30px; color:#666; font-size:0.9em;">
        Note: This uses your hosting server's mail() function.<br>
        Most emails will go to spam/junk or be blocked completely.<br>
        Use only for testing/educational purposes.
    </p>

</body>
</html>
