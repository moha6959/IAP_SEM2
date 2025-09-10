<?php
//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../plugins/PHPMailer/vendor/autoload.php';

// Creating a class
class SendMail {
    public function sendVerificationEmail($userEmail, $userName, $verificationToken) {
        // Configuration - update with your actual credentials
        $smtpHost = 'smtp.gmail.com';
        $smtpUsername = 'mohamedek.yussuf@strathmore.edu'; // Your email
        $smtpPassword = 'mgzb ownb bpsx hppp'; // Your app password
        $smtpPort = 465;
        
        // Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output
            $mail->isSMTP(); // Send using SMTP
            $mail->Host       = $smtpHost; // Set the SMTP server to send through
            $mail->SMTPAuth   = true; // Enable SMTP authentication
            $mail->Username   = $smtpUsername; // SMTP username
            $mail->Password   = $smtpPassword; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
            $mail->Port       = $smtpPort; // TCP port to connect to

            // Recipients
            $mail->setFrom('noreply@dekshostel.com', 'Dekshostel');
            $mail->addAddress($userEmail, $userName); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Welcome to Dekshostel - Account Verification';
            
            // Email body with verification link
            $verificationLink = "http://localhost/IAP_SEM2-1/verify.php?token=" . $verificationToken;
            
            $mail->Body = "
                <h2>Welcome to Dekshostel Account Verification</h2>
                <p>Hello $userName,</p>
                <p>You requested an account on Dekshostel.</p>
                <p>In order to use this account you need to <a href='$verificationLink'>Click Here</a> to complete the registration process.</p>
                <br>
                <p>Regards,<br>Systems Admin<br>Dekshostel</p>
            ";
            
            // Plain text version for non-HTML email clients
            $mail->AltBody = "Welcome to Dekshostel Account Verification\n\nHello $userName,\n\nYou requested an account on Dekshostel.\n\nIn order to use this account you need to visit the following link to complete the registration process: $verificationLink\n\nRegards,\nSystems Admin\nDekshostel";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}