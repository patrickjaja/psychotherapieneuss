<?php
namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUser;
    private string $smtpPass;
    private string $fromEmail;
    private string $fromName;

    public function __construct(
        string $smtpHost,
        string $smtpPort,
        string $smtpUser,
        string $smtpPass,
        string $fromEmail,
        string $fromName
    ) {
        // Handle environment variable placeholders
        if (strpos($smtpHost, 'env_') === 0) {
            $smtpHost = $_ENV['SMTP_HOST'] ?? '';
        }
        if (strpos($smtpPort, 'env_') === 0) {
            $smtpPort = $_ENV['SMTP_PORT'] ?? '465';
        }
        if (strpos($smtpUser, 'env_') === 0) {
            $smtpUser = $_ENV['SMTP_USER'] ?? '';
        }
        if (strpos($smtpPass, 'env_') === 0) {
            $smtpPass = $_ENV['SMTP_PASS'] ?? '';
        }
        if (strpos($fromEmail, 'env_') === 0) {
            $fromEmail = $_ENV['SMTP_FROM'] ?? '';
        }
        if (strpos($fromName, 'env_') === 0) {
            $fromName = $_ENV['SMTP_FROM_NAME'] ?? '';
        }
        
        $this->smtpHost = $smtpHost;
        $this->smtpPort = (int) $smtpPort;
        $this->smtpUser = $smtpUser;
        $this->smtpPass = $smtpPass;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function sendContactEmail(array $data): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUser;
            $mail->Password   = $this->smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $this->smtpPort;
            $mail->CharSet    = 'UTF-8';

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($this->fromEmail); // Send to practice
            $mail->addReplyTo($data['email'], $data['name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Neue Kontaktanfrage über die Website';
            
            $html = $this->createContactEmailHtml($data);
            $text = $this->createContactEmailText($data);
            
            $mail->Body    = $html;
            $mail->AltBody = $text;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public function sendConfirmationEmail(array $data): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUser;
            $mail->Password   = $this->smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $this->smtpPort;
            $mail->CharSet    = 'UTF-8';

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($data['email'], $data['name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Ihre Kontaktanfrage bei Psychotherapie Neuss';
            
            $html = $this->createConfirmationEmailHtml($data);
            $text = $this->createConfirmationEmailText($data);
            
            $mail->Body    = $html;
            $mail->AltBody = $text;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public function createContactEmailHtml(array $data): string
    {
        $message = nl2br(htmlspecialchars($data['message']));
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #10b981; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f8f9fa; padding: 20px; margin-top: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #666; }
        .value { margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Neue Kontaktanfrage</h2>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Vorname:</div>
                <div class="value">{$data['firstname']}</div>
            </div>
            <div class="field">
                <div class="label">Nachname:</div>
                <div class="value">{$data['lastname']}</div>
            </div>
            <div class="field">
                <div class="label">E-Mail:</div>
                <div class="value"><a href="mailto:{$data['email']}">{$data['email']}</a></div>
            </div>
            <div class="field">
                <div class="label">Telefon:</div>
                <div class="value">{$data['phone']}</div>
            </div>
            <div class="field">
                <div class="label">Nachricht:</div>
                <div class="value">{$message}</div>
            </div>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
            <div class="field">
                <div class="label">Datum/Zeit:</div>
                <div class="value">{$data['timestamp']}</div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    public function createContactEmailText(array $data): string
    {
        return <<<TEXT
Neue Kontaktanfrage über die Website

Vorname: {$data['firstname']}
Nachname: {$data['lastname']}
E-Mail: {$data['email']}
Telefon: {$data['phone']}

Nachricht:
{$data['message']}

------------------------
Datum/Zeit: {$data['timestamp']}
TEXT;
    }

    public function createConfirmationEmailHtml(array $data): string
    {
        $message = nl2br(htmlspecialchars($data['message']));
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #10b981; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .summary { background-color: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .footer { text-align: center; color: #666; font-size: 14px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vielen Dank für Ihre Kontaktanfrage</h1>
        </div>
        <div class="content">
            <p>Sehr geehrte/r {$data['name']},</p>
            
            <p>vielen Dank für Ihre Kontaktaufnahme. Ich habe Ihre Anfrage erhalten und werde mich schnellstmöglich bei Ihnen melden.</p>
            
            <div class="summary">
                <h3>Zusammenfassung Ihrer Anfrage:</h3>
                <p><strong>Ihre Nachricht:</strong><br>{$message}</p>
                <p><strong>Ihre Kontaktdaten:</strong><br>
                E-Mail: {$data['email']}<br>
                Telefon: {$data['phone']}</p>
            </div>
            
            <p>In der Regel melde ich mich innerhalb von 1-2 Werktagen bei Ihnen. Sollten Sie dringende Anliegen haben, können Sie mich auch telefonisch erreichen.</p>
            
            <p>Mit freundlichen Grüßen<br>
            Dr. rer. nat. Pia Dornberger</p>
        </div>
        <div class="footer">
            <p>Psychotherapie Neuss<br>
            Friedrichstr. 10, 41460 Neuss<br>
            info@psychotherapieneuss.de</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    public function createConfirmationEmailText(array $data): string
    {
        return <<<TEXT
Vielen Dank für Ihre Kontaktanfrage

Sehr geehrte/r {$data['name']},

vielen Dank für Ihre Kontaktaufnahme. Ich habe Ihre Anfrage erhalten und werde mich schnellstmöglich bei Ihnen melden.

Zusammenfassung Ihrer Anfrage:

Ihre Nachricht:
{$data['message']}

Ihre Kontaktdaten:
E-Mail: {$data['email']}
Telefon: {$data['phone']}

In der Regel melde ich mich innerhalb von 1-2 Werktagen bei Ihnen. Sollten Sie dringende Anliegen haben, können Sie mich auch telefonisch erreichen.

Mit freundlichen Grüßen
Dr. rer. nat. Pia Dornberger

--
Psychotherapie Neuss
Friedrichstr. 10, 41460 Neuss
info@psychotherapieneuss.de
TEXT;
    }
}