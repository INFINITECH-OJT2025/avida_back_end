<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Generic method to send email using PHPMailer
     */
    public static function sendEmail($to, $subject, $body)
    {
        Log::info("📧 [MailService] Preparing to send email", [
            'to' => $to,
            'type' => gettype($to)
        ]);

        // Validate email recipient
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Log::error("❌ Invalid email address provided", ['email' => $to]);
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', PHPMailer::ENCRYPTION_STARTTLS);
            $mail->Port       = env('MAIL_PORT', 587);

            // Sender and Recipient
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $mail->addAddress($to);

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            // Send Email
            if (!$mail->send()) {
                Log::error("📤 Email failed to send", ['error' => $mail->ErrorInfo]);
                return false;
            }

            Log::info("✅ Email successfully sent to: $to");
            return true;
        } catch (Exception $e) {
            Log::error("💥 PHPMailer Exception", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send a verification email with a verification link
     */
    public static function sendVerificationEmail($to, $name, $verifyLink)
    {
        $subject = "Verify Your Email Address";
        $body = "
            <h2>Hello, $name!</h2>
            <p>Thank you for registering. Please click the button below to verify your email:</p>
            <p><a href='$verifyLink' style='padding:10px 20px;background:#990e15;color:white;text-decoration:none;border-radius:5px;'>Verify Email</a></p>
            <p>If you did not sign up, please ignore this message.</p>
        ";

        return self::sendEmail($to, $subject, $body);
    }
}
