<?php
// === Embedded PHPMailer Lite (no Composer needed) ===
class PHPMailer {
    public $Host = '';
    public $SMTPAuth = true;
    public $Username = '';
    public $Password = '';
    public $SMTPSecure = 'tls';
    public $Port = 587;
    public $From = '';
    public $FromName = '';
    public $Subject = '';
    public $Body = '';
    public $isHTML = false;
    public $addAddress = [];
    public $ErrorInfo = '';

    public function setFrom($address, $name = '') {
        $this->From = $address;
        $this->FromName = $name;
    }

    public function addAddress($address) {
        $this->addAddress[] = $address;
    }

    public function send() {
        $to = implode(', ', $this->addAddress);
        $headers = "From: {$this->FromName} <{$this->From}>\r\n";
        $headers .= "Reply-To: {$this->From}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($to, $this->Subject, $this->Body, $headers)) {
            return true;
        } else {
            $this->ErrorInfo = error_get_last()['message'] ?? 'Unknown error';
            return false;
        }
    }
}

// === Your Form & Sending Logic ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to      = trim($_POST['to'] ?? '');
    $from_name = trim($_POST['from'] ?? 'Anonymous Sender');
    $subject = trim($_POST['subject'] ?? 'No Subject');
    $message = trim($_POST['message'] ?? '');

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $status = "Invalid recipient email!";
        $status_class = 'error';
    } elseif (empty($message)) {
        $status = "Message cannot be empty!";
        $status_class = 'error';
    } else {
        $mail = new PHPMailer();

        // === Change these to your Brevo details ===
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->Username   = '970bcc001@smtp-brevo.com';     // e.g. 970bcc001@smtp-brevo.com
        $mail->Password   = 'xsmtpsib-67fa1d4d16094fed2908a6ee77cd9c55be19bb6afad382c80e6f8b8f4028bdb3-fXfTDaioEhDPfhoI';          // Your real SMTP key
        $mail->Port       = 587;

        $mail->setFrom('mrgaming111223@gmail.com', $from_name);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        if ($mail->send()) {
            $status = "Email sent! (Check spam/junk folder if not in inbox)";
            $status_class = 'success';
        } else {
            $status = "Failed to send: " . $mail->ErrorInfo;
            $status_class = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anonymous Email Sender</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin: 12px 0 6px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1.1em;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .status {
            margin: 20px 0;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
        }
        .success { background: #d4edda; color: #155724; }
        .error   { background: #f8d7da; color: #721c24; }
        .note {
            margin-top: 20px;
            color: #666;
            font-size: 0.9em;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Anonymous Email Sender</h1>

    <?php if (isset($status)): ?>
        <div class="status <?= $status_class ?>">
            <?= htmlspecialchars($status) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>To (recipient email):</label>
        <input type="email" name="to" required placeholder="example@gmail.com">

        <label>From name (sender name shown):</label>
        <input type="text" name="from" required value="Anonymous">

        <label>Subject:</label>
        <input type="text" name="subject" required value="Hello from Anonymous">

        <label>Message:</label>
        <textarea name="message" rows="6" required placeholder="Your message here..."></textarea>

        <button type="submit">Send Email</button>
    </form>

    <div class="note">
        Note: Emails may land in spam/junk or get blocked.<br>
        Use only for legal, testing/educational purposes.
    </div>
</div>

</body>
</html>
