<?php
session_start();
require_once 'db_connect.php';

// Get form data
$email = $_POST['email'];
$name = $_POST['name'];
$password = $_POST['password'];

// Generate verification token (URL-safe)
$token = bin2hex(random_bytes(50));
$expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

// DEBUG: Show what we're doing
error_log("REGISTRATION: Email=$email, Name=$name");
error_log("REGISTRATION: Token generated: $token");

try {
    // 1. First insert user into users table
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $userStmt = $pdo->prepare("INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
    
    if ($userStmt->execute([$email, $name, $hashedPassword])) {
        error_log("REGISTRATION: User inserted successfully");
        
        // 2. Store token in verification_tokens table
        $tokenStmt = $pdo->prepare("INSERT INTO verification_tokens (email, token, expires_at) VALUES (?, ?, ?)");
        
        if ($tokenStmt->execute([$email, $token, $expires])) {
            error_log("REGISTRATION: Token saved successfully");
            
            // 3. Send verification email
            $verificationLink = "http://localhost/IAP_SEM2-1/verify.php?token=" . urlencode($token);
            $subject = "Verify Your Email Address";
            $message = "Hello " . $name . ",\n\n";
            $message .= "Please click the following link to verify your email address:\n";
            $message .= $verificationLink . "\n\n";
            $message .= "This link will expire in 24 hours.\n\n";
            $message .= "If you didn't create an account, please ignore this email.\n";

            $headers = "From: no-reply@yourdomain.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($email, $subject, $message, $headers)) {
                $_SESSION['message'] = "Registration successful! Please check your email to verify your account.";
                header("Location: registration_success.php");
                exit();
            } else {
                $_SESSION['error'] = "Registration successful but verification email failed to send.";
                header("Location: register.php");
                exit();
            }
        } else {
            error_log("REGISTRATION: FAILED to save token");
            $_SESSION['error'] = "Error during registration. Please try again.";
            header("Location: register.php");
            exit();
        }
    } else {
        error_log("REGISTRATION: FAILED to insert user");
        $_SESSION['error'] = "Error during registration. Please try again.";
        header("Location: register.php");
        exit();
    }
    
} catch (PDOException $e) {
    error_log("REGISTRATION: Database error - " . $e->getMessage());
    $_SESSION['error'] = "An error occurred during registration. Please try again.";
    header("Location: register.php");
    exit();
}
?>