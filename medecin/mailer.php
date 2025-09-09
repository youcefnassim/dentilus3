<?php
// Simple wrapper to send notification emails. Uses PHPMailer if available (composer), otherwise falls back to mail().
require_once __DIR__ . '/db.php';

function sendNotification($to, $subject, $body){
    global $SMTP_HOST, $SMTP_USER, $SMTP_PASS, $SMTP_PORT, $SMTP_SECURE, $MAIL_FROM;
    // try to use PHPMailer if installed
    if (file_exists(__DIR__ . '/../vendor/autoload.php')){
        require_once __DIR__ . '/../vendor/autoload.php';
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try{
            if ($SMTP_HOST) {
                $mail->isSMTP();
                $mail->Host = $SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = $SMTP_USER;
                $mail->Password = $SMTP_PASS;
                $mail->SMTPSecure = $SMTP_SECURE;
                $mail->Port = $SMTP_PORT;
            }
            $mail->setFrom($MAIL_FROM, 'Dentilus');
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);
            $mail->send();
            return true;
        }catch(Exception $e){
            error_log('PHPMailer error: '. $e->getMessage());
            // fallback
        }
    }

    // fallback to mail()
    $headers = 'From: ' . ($MAIL_FROM ?: 'no-reply@dentilus.local') . "\r\n" . 'Reply-To: ' . ($MAIL_FROM ?: 'no-reply@dentilus.local') . "\r\n" . 'X-Mailer: PHP/' . phpversion();
    return @mail($to, $subject, $body, $headers);
}
