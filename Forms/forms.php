<?php
// Include the sendmail class
require_once '../Global/sendmail.php';
// Start session to store verification tokens
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
            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            
            // Store token in session (in a real app, store in database)
            $_SESSION['verification_tokens'][$email] = [
                'token' => $verificationToken,
                'name' => $name,
                'expires' => time() + (24 * 60 * 60) // 24 hours from now
            ];
            
            // Send verification email
            $mailer = new SendMail();
            $emailSent = $mailer->sendVerificationEmail($email, $name, $verificationToken);
            
            if ($emailSent) {
                echo "<div class='alert alert-success mt-3'>Signup successful! Please check your email to verify your account.</div>";
            } else {
                echo "<div class='alert alert-danger mt-3'>There was an error sending the verification email. Please try again.</div>";
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

class forms{
    public function signup(){
?>
<form method="POST" action="">
  <div class="mb-3">
    <label for="exampleInputName1" class="form-label">Name</label>
    <input type="text" class="form-control" id="exampleInputName1" name="name" aria-describedby="nameHelp" required>
    <div id="nameHelp" class="form-text"></div>
  </div>
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Email address</label>
    <input type="email" class="form-control" id="exampleInputEmail1" name="email" aria-describedby="emailHelp" required>
    <div id="emailHelp" class="form-text"></div>
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" name="password" required>
  </div>
      <?php $this->submit_button('Sign Up', 'signup'); ?> <a href='?form=signin'>Already have an account? Login</a>
</form>

<?php
    }

    private function submit_button($value, $name){
?>
        <button type='submit' class="btn btn-primary" name='<?php echo $name; ?>'><?php echo $value; ?></button>
<?php
    }

    public function signin(){
?>

<form method="POST" action="">
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Email address</label>
    <input type="email" class="form-control" id="exampleInputEmail1" name="email" aria-describedby="emailHelp" required>
    <div id="emailHelp" class="form-text"></div>
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" name="password" required>
  </div>
    <?php $this->submit_button('Sign In', 'signin'); ?> <a href='?form=signup'>Don't have an account? Sign Up</a>
</form>
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