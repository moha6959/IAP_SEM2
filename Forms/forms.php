<?php
// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['signup'])) {
        // Process signup form
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate and process the data (in a real application)
        echo "<div class='alert alert-success mt-3'>Signup successful! (In real app, this would save to database)</div>";
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