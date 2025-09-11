<?php
session_start();
require_once 'db_connect.php';

echo "<!DOCTYPE html><html><head>
    <title>Email Verification</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { background-color: #d4edda; color: #155724; padding: 20px; border-radius: 5px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; }
        .container { padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body><div class='container'><h2>Email Verification</h2>";

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);

    // 🔍 Debug: get the last token saved in DB (safe fetch)
    $stmt = $pdo->query("SELECT email, token, expires_at FROM verification_tokens ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<pre>";
    echo "DEBUG - Token from URL: " . htmlspecialchars($token) . "\n";
    if ($last) {
        echo "DEBUG - Last token in DB: " . htmlspecialchars($last['token']) . "\n";
        echo "DEBUG - Email for token: " . htmlspecialchars($last['email']) . "\n";
        echo "DEBUG - Expiry: " . htmlspecialchars($last['expires_at']) . "\n";
    } else {
        echo "DEBUG - No token found in database.\n";
    }
    echo "</pre>";

    // ✅ Now check token normally
    $stmt = $pdo->prepare("SELECT email, expires_at FROM verification_tokens WHERE token = ?");
    $stmt->execute([$token]);
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tokenData) {
        if (strtotime($tokenData['expires_at']) > time()) {
            $updateStmt = $pdo->prepare("UPDATE users SET is_verified = TRUE WHERE email = ?");
            $updateStmt->execute([$tokenData['email']]);

            $deleteStmt = $pdo->prepare("DELETE FROM verification_tokens WHERE token = ?");
            $deleteStmt->execute([$token]);

            echo "<div class='success'>
                    <h3>✓ Verification Successful!</h3>
                    <p>Your email has been verified successfully.</p>
                    <p>You can now <a href='forms.php?form=signin'>login to your account</a>.</p>
                  </div>";
        } else {
            echo "<div class='error'><h3>✗ Verification Failed</h3><p>Link expired. Request a new one.</p></div>";
        }
    } else {
        echo "<div class='error'><h3>✗ Verification Failed</h3><p>Invalid verification token.</p></div>";
    }
} else {
    echo "<div class='error'><h3>✗ Verification Failed</h3><p>No token provided.</p></div>";
}

echo "</div></body></html>";
