<?php
// Include the sendmail class and database connection
require_once '../Global/sendmail.php';
require_once '../db_connect.php'; // Database connection
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
                $verificationToken = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                // 1. First save user to database
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $userStmt = $pdo->prepare("INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
                $userStmt->execute([$email, $name, $hashedPassword]);
                
                // 2. Save token to database
                $tokenStmt = $pdo->prepare("INSERT INTO verification_tokens (email, token, expires_at) VALUES (?, ?, ?)");
                $tokenStmt->execute([$email, $verificationToken, $expires]);
                
                // Send verification email
                $mailer = new SendMail();
                
                // Make sure sendVerificationEmail uses the correct URL format
                $emailSent = $mailer->sendVerificationEmail($email, $name, $verificationToken);
                
                if ($emailSent) {
                    echo "<div class='alert alert-success mt-3'>Signup successful! Please check your email to verify your account.</div>";
                    // Clear form
                    echo "<script>document.querySelector('form').reset();</script>";
                } else {
                    echo "<div class='alert alert-danger mt-3'>There was an error sending the verification email. Please try again.</div>";
                }
                
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate email
                    echo "<div class='alert alert-danger mt-3'>Email already exists. Please use a different email.</div>";
                } else {
                    echo "<div class='alert alert-danger mt-3'>Registration error. Please try again.</div>";
                }
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
    
    if (isset($_POST['signin'])) {
        // Process signin form
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        try {
            // Check if user exists and is verified
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_verified = TRUE");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                
                echo "<div class='alert alert-success mt-3'>Login successful! Welcome back, " . htmlspecialchars($user['name']) . "!</div>";
                echo "<script>setTimeout(function(){ window.location.href = 'dashboard.php'; }, 2000);</script>";
            } else {
                echo "<div class='alert alert-danger mt-3'>Invalid email, password, or account not verified.</div>";
            }
            
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger mt-3'>Login error. Please try again.</div>";
            error_log("Login error: " . $e->getMessage());
        }
    }
}

class forms{
    public function signup(){
?>
<style>
    .deks-hostels-form {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .form-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        max-width: 800px;
        width: 100%;
        transition: transform 0.3s ease;
    }
    .form-container:hover {
        transform: translateY(-5px);
    }
    .form-header {
        background: linear-gradient(90deg, #3498db, #2980b9);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
    }
    .form-header::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 40px;
        background: #3498db;
        border-radius: 50%;
    }
    .form-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 28px;
        letter-spacing: 0.5px;
    }
    .form-header p {
        margin: 10px 0 0;
        opacity: 0.9;
        font-size: 16px;
    }
    .form-body {
        padding: 40px;
    }
    .form-control {
        border-radius: 10px;
        padding: 15px 20px;
        border: 2px solid #eaeaea;
        transition: all 0.3s;
        font-size: 16px;
    }
    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    .form-label {
        font-weight: 600;
        margin-bottom: 10px;
        color: #2c3e50;
        font-size: 16px;
    }
    .btn-deks-primary {
        background: linear-gradient(90deg, #3498db, #2980b9);
        border: none;
        padding: 15px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 17px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .btn-deks-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(52, 152, 219, 0.4);
    }
    .form-footer {
        text-align: center;
        margin-top: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-top: 1px solid #eaeaea;
        border-radius: 0 0 15px 15px;
    }
    .form-link {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        position: relative;
    }
    .form-link:hover {
        color: #2980b9;
    }
    .form-link::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 2px;
        bottom: -2px;
        left: 0;
        background-color: #3498db;
        transform: scaleX(0);
        transition: transform 0.3s;
    }
    .form-link:hover::after {
        transform: scaleX(1);
    }
    .brand-logo {
        font-size: 32px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 15px;
        letter-spacing: 1px;
    }
    .form-text {
        font-size: 14px;
        color: #6c757d;
        margin-top: 8px;
    }
    .password-toggle {
        position: relative;
    }
    .password-toggle-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
    }
    .alert {
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }
</style>

<div class="deks-hostels-form">
    <div class="form-container">
        <div class="form-header">
            <div class="brand-logo">DEKS HOSTELS</div>
            <h2>Create Your Account</h2>
            <p>Join our community and enjoy premium hostel services</p>
        </div>
        
        <div class="form-body">
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="exampleInputName1" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="exampleInputName1" name="name" aria-describedby="nameHelp" required placeholder="Enter your full name">
                    <div id="nameHelp" class="form-text">We'll never share your information with anyone else.</div>
                </div>
                
                <div class="mb-4">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" name="email" aria-describedby="emailHelp" required placeholder="Enter your email">
                    <div id="emailHelp" class="form-text">We'll send a verification link to this email.</div>
                </div>
                
                <div class="mb-4 password-toggle">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="password" required placeholder="Create a strong password">
                    <span class="password-toggle-icon" onclick="togglePassword('exampleInputPassword1')">
                        <i class="fas fa-eye"></i>
                    </span>
                    <div class="form-text">Use at least 8 characters with a mix of letters and numbers.</div>
                </div>
                
                <div class="d-grid gap-2">
                    <?php $this->submit_button('Sign Up', 'signup'); ?>
                </div>
            </form>
        </div>
        
        <div class="form-footer">
            <p class="mb-0">Already have an account? <a href='?form=signin' class="form-link">Sign In</a></p>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = passwordInput.nextElementSibling.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<?php
    }

    private function submit_button($value, $name){
?>
        <button type='submit' class="btn btn-deks-primary btn-lg" name='<?php echo $name; ?>'>
            <i class="fas fa-user-plus me-2"></i><?php echo $value; ?>
        </button>
<?php
    }

    public function signin(){
?>
<style>
    .deks-hostels-form {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        min-height: 100vh;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .form-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        max-width: 600px;
        width: 100%;
        transition: transform 0.3s ease;
    }
    .form-container:hover {
        transform: translateY(-5px);
    }
    .form-header {
        background: linear-gradient(90deg, #5cdb95, #46c280);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
    }
    .form-header::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 40px;
        background: #5cdb95;
        border-radius: 50%;
    }
    .form-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 28px;
        letter-spacing: 0.5px;
    }
    .form-header p {
        margin: 10px 0 0;
        opacity: 0.9;
        font-size: 16px;
    }
    .form-body {
        padding: 40px;
    }
    .form-control {
        border-radius: 10px;
        padding: 15px 20px;
        border: 2px solid #eaeaea;
        transition: all 0.3s;
        font-size: 16px;
    }
    .form-control:focus {
        border-color: #5cdb95;
        box-shadow: 0 0 0 0.2rem rgba(92, 219, 149, 0.25);
    }
    .form-label {
        font-weight: 600;
        margin-bottom: 10px;
        color: #2c3e50;
        font-size: 16px;
    }
    .btn-deks-success {
        background: linear-gradient(90deg, #5cdb95, #46c280);
        border: none;
        padding: 15px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 17px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: white;
    }
    .btn-deks-success:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(92, 219, 149, 0.4);
    }
    .form-footer {
        text-align: center;
        margin-top: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-top: 1px solid #eaeaea;
        border-radius: 0 0 15px 15px;
    }
    .form-link {
        color: #5cdb95;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        position: relative;
    }
    .form-link:hover {
        color: #46c280;
    }
    .form-link::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 2px;
        bottom: -2px;
        left: 0;
        background-color: #5cdb95;
        transform: scaleX(0);
        transition: transform 0.3s;
    }
    .form-link:hover::after {
        transform: scaleX(1);
    }
    .brand-logo {
        font-size: 32px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 15px;
        letter-spacing: 1px;
    }
    .password-toggle {
        position: relative;
    }
    .password-toggle-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
    }
    .alert {
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }
</style>

<div class="deks-hostels-form">
    <div class="form-container">
        <div class="form-header">
            <div class="brand-logo">DEKS HOSTELS</div>
            <h2>Welcome Back</h2>
            <p>Sign in to access your account</p>
        </div>
        
        <div class="form-body">
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" name="email" aria-describedby="emailHelp" required placeholder="Enter your email">
                </div>
                
                <div class="mb-4 password-toggle">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="password" required placeholder="Enter your password">
                    <span class="password-toggle-icon" onclick="togglePassword('exampleInputPassword1')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="d-grid gap-2">
                    <?php $this->submit_button('Sign In', 'signin'); ?>
                </div>
            </form>
        </div>
        
        <div class="form-footer">
            <p class="mb-0">Don't have an account? <a href='?form=signup' class="form-link">Sign Up</a></p>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = passwordInput.nextElementSibling.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
<?php
    }
}

// Create an instance of the forms class
$forms = new forms();

// Check which form to display based on URL parameter
if (isset($_GET['form']) && $_GET['form'] == 'signin') {
    $forms->signin();
} else {
    $forms->signup();
}
?>