<?php
// verify.php - Email verification handler
session_start();

// Start HTML output
echo "<!DOCTYPE html>
<html>
<head>
    <title>Email Verification - ICS 2.2</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { background-color: #d4edda; color: #155724; padding: 20px; border-radius: 5px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; }
        .container { padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>Email Verification</h2>";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $verified = false;
    
    // Check if token exists in our session storage
    if (isset($_SESSION['verification_tokens'])) {
        foreach ($_SESSION['verification_tokens'] as $email => $data) {
            if ($data['token'] === $token) {
                // Check if token has expired
                if (time() < $data['expires']) {
                    $userName = $data['name'];
                    $verified = true;
                    
                    // In a real application, you would:
                    // 1. Mark the user as verified in the database
                    // 2. Remove the token from storage
                    
                    // Remove this token from session
                    unset($_SESSION['verification_tokens'][$email]);
                    
                    echo "<div class='success'>
                            <h3>✓ Verification Successful!</h3>
                            <p>Hello $userName,</p>
                            <p>Thank you for verifying your email address ($email).</p>
                            <p>Your account has been successfully activated.</p>
                            <p>You can now <a href='forms.php?form=signin'>login to your account</a>.</p>
                          </div>";
                } else {
                    echo "<div class='error'>
                            <h3>✗ Verification Link Expired</h3>
                            <p>This verification link has expired. Please request a new verification email.</p>
                          </div>";
                }
                break;
            }
        }
    }
    
    if (!$verified) {
        echo "<div class='error'>
                <h3>✗ Verification Failed</h3>
                <p>Invalid verification link. Please make sure you used the correct link from your email.</p>
                <p>If you continue to have problems, please contact support.</p>
              </div>";
    }
    
} else {
    echo "<div class='error'>
            <h3>✗ Verification Failed</h3>
            <p>Invalid verification link. Please make sure you used the correct link from your email.</p>
            <p>If you continue to have problems, please contact support.</p>
          </div>";
}

echo "</div></body></html>";