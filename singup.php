<?php
// Include the sendmail class and database connection
require_once '../Global/sendmail.php';
require_once 'db_connect.php'; 
session_start();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['signup'])) {
        // Process signup form
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<div class='alert alert-danger mt-3'>Invalid email format</div>";
        } else {
            try {
                // Generate verification token
                $v = bin2hex(random_bytes(32));
                $expires = derificationTokenate('Y-m-d H:i:s', strtotime('+24 hours'));
                
                // 1. First save user to database
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $userStmt = $pdo->prepare("INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
                $userStmt->execute([$email, $name, $hashedPassword]);
                
                // 2. Save token to database (NOT session)
                $tokenStmt = $pdo->prepare("INSERT INTO verification_tokens (email, token, expires_at) VALUES (?, ?, ?)");
                $tokenStmt->execute([$email, $verificationToken, $expires]);
                
                // Send verification email
                $mailer = new SendMail();
                $emailSent = $mailer->sendVerificationEmail($email, $name, $verificationToken);
                
                if ($emailSent) {
                    echo "<div class='alert alert-success mt-3'>Signup successful! Please check your email to verify your account.</div>";
                } else {
                    echo "<div class='alert alert-danger mt-3'>There was an error sending the verification email. Please try again.</div>";
                }
                
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
            }
        }
    }
    
    if (isset($_POST['signin'])) {
        // Process signin form
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate credentials (in a real application)
        echo "<div class='alert alert-success mt-3'>Login successful! (In real app, this would verify credentials)</div>";
    }
}

