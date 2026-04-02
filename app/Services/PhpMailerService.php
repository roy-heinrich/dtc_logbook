<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PhpMailerService
{
    public function send(string $toEmail, string $toName, string $subject, string $htmlBody): void
    {
        $mailer = new PHPMailer(true);

        try {
            $mailer->isSMTP();
            $smtpDebugLevel = (int) config('services.phpmailer.debug', 0);
            $mailer->SMTPDebug = $smtpDebugLevel;
            $mailer->Debugoutput = static function (string $message, int $level): void {
                Log::debug('PHPMailer SMTP debug', [
                    'level' => $level,
                    'message' => $message,
                ]);
            };
            $mailer->Host = (string) config('services.phpmailer.host');
            $mailer->Port = (int) config('services.phpmailer.port');

            $username = (string) config('services.phpmailer.username');
            $password = (string) config('services.phpmailer.password');
            $encryption = (string) config('services.phpmailer.encryption');

            $mailer->SMTPAuth = $username !== '';
            $mailer->Username = $username;
            $mailer->Password = $password;

            if ($encryption === 'ssl') {
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls') {
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $fromAddress = (string) config('services.phpmailer.from_address');
            $fromName = (string) config('services.phpmailer.from_name');

            $mailer->setFrom($fromAddress, $fromName);
            $mailer->addAddress($toEmail, $toName);
            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = $htmlBody;
            $mailer->AltBody = trim(strip_tags($htmlBody));

            $mailer->send();
        } catch (Exception $exception) {
            Log::error('PHPMailer failed to send email', [
                'error' => $exception->getMessage(),
                'to' => $toEmail,
                'subject' => $subject,
            ]);

            throw $exception;
        }
    }
}
